<?php
/**
 * Exchange Keycloak authorization code for tokens and sign in osTicket client.
 * Included from keycloak-callback.php after bootstrap.
 */
if (!defined('INCLUDE_DIR')) {
    die('Bootstrap required');
}

require_once INCLUDE_DIR . 'client/iudx-keycloak-config.inc.php';
require_once INCLUDE_DIR . 'class.client.php';

function iudx_keycloak_callback_error($msg) {
    Http::redirect(ROOT_PATH . 'index.php?sso_error=1&msg=' . rawurlencode($msg));
    exit;
}

// Keycloak error redirect
if (!empty($_GET['error'])) {
    iudx_keycloak_callback_error($_GET['error_description'] ?: $_GET['error']);
}

$code = $_GET['code'] ?? '';
$state = $_GET['state'] ?? '';
if (!$code || !$state) {
    Http::redirect(ROOT_PATH . 'index.php');
    exit;
}

$verifier = null;
$redirectUsed = IUDX_KEYCLOAK_REDIRECT_URI;

if (session_status() === PHP_SESSION_ACTIVE) {
    if (!empty($_SESSION['iudx_keycloak_oauth_state_register'])
            && hash_equals((string) $_SESSION['iudx_keycloak_oauth_state_register'], (string) $state)) {
        $verifier = $_SESSION['iudx_keycloak_pkce_verifier_register'] ?? null;
        $redirectUsed = $_SESSION['iudx_keycloak_redirect_uri_used_register'] ?? IUDX_KEYCLOAK_REDIRECT_URI;
    } elseif (!empty($_SESSION['iudx_keycloak_oauth_state'])
            && hash_equals((string) $_SESSION['iudx_keycloak_oauth_state'], (string) $state)) {
        $verifier = $_SESSION['iudx_keycloak_pkce_verifier'] ?? null;
        $redirectUsed = $_SESSION['iudx_keycloak_redirect_uri_used'] ?? IUDX_KEYCLOAK_REDIRECT_URI;
    }
}

if ($verifier === null) {
    iudx_keycloak_callback_error(__('Invalid or expired sign-in session. Please try Login again.'));
}

// One-time use
unset(
    $_SESSION['iudx_keycloak_oauth_state'],
    $_SESSION['iudx_keycloak_oauth_state_register'],
    $_SESSION['iudx_keycloak_pkce_verifier'],
    $_SESSION['iudx_keycloak_pkce_verifier_register'],
    $_SESSION['iudx_keycloak_redirect_uri_used'],
    $_SESSION['iudx_keycloak_redirect_uri_used_register']
);

$postFields = array(
    'grant_type' => 'authorization_code',
    'client_id' => IUDX_KEYCLOAK_CLIENT_ID,
    'code' => $code,
    'redirect_uri' => $redirectUsed,
    'code_verifier' => $verifier,
);
if (IUDX_KEYCLOAK_CLIENT_SECRET !== '') {
    $postFields['client_secret'] = IUDX_KEYCLOAK_CLIENT_SECRET;
}

$ch = curl_init(iudx_keycloak_token_endpoint());
curl_setopt_array($ch, array(
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($postFields),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
));
$tokenBody = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($tokenBody === false || $httpCode < 200 || $httpCode >= 300) {
    iudx_keycloak_callback_error(__('Could not complete sign-in with Keycloak (token exchange failed).'));
}

$tokenJson = json_decode($tokenBody, true);
if (!is_array($tokenJson) || empty($tokenJson['access_token'])) {
    iudx_keycloak_callback_error(__('Invalid token response from Keycloak.'));
}

$accessToken = $tokenJson['access_token'];

$ch = curl_init(iudx_keycloak_userinfo_endpoint());
curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $accessToken,
        'Accept: application/json',
    ),
));
$userinfoBody = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($userinfoBody === false || $httpCode < 200 || $httpCode >= 300) {
    iudx_keycloak_callback_error(__('Could not load your profile from Keycloak.'));
}

$claims = json_decode($userinfoBody, true);
if (!is_array($claims)) {
    iudx_keycloak_callback_error(__('Invalid user profile from Keycloak.'));
}

$email = $claims['email'] ?? '';
if (!$email && !empty($claims['preferred_username'])) {
    $email = $claims['preferred_username'];
}
$email = trim((string) $email);
if (!Validator::is_email($email)) {
    iudx_keycloak_callback_error(__('Your Keycloak account does not include a valid email address required for this help desk.'));
}

$name = $claims['name'] ?? '';
if (!$name && !empty($claims['given_name'])) {
    $name = trim($claims['given_name'] . ' ' . ($claims['family_name'] ?? ''));
}
if (!$name) {
    $name = $email;
}

global $cfg;
$user = User::lookupByEmail($email);
if (!$user) {
    if (!$cfg || $cfg->getClientRegistrationMode() === 'disabled') {
        iudx_keycloak_callback_error(__('No help desk account exists for your email. Registration is disabled; please contact support.'));
    }
    $user = User::fromVars(array(
        'email' => $email,
        'name' => $name,
    ), true, false);
}
if (!$user || !$user->getId()) {
    iudx_keycloak_callback_error(__('Could not create or load your help desk user.'));
}

$account = $user->getAccount();
if (!$account) {
    $account = ClientAccount::createForUser($user, array(
        'timezone' => $cfg ? $cfg->getDefaultTimezone() : null,
        'username' => $email,
    ));
    if (!$account || !$account->confirm()) {
        iudx_keycloak_callback_error(__('Could not create a help desk account for your Keycloak user.'));
    }
} elseif (!$account->isConfirmed() && !$account->confirm()) {
    iudx_keycloak_callback_error(__('Could not confirm your help desk account.'));
}

$bk = UserAuthenticationBackend::getBackend('client');
if (!$bk) {
    iudx_keycloak_callback_error(__('Authentication backend is not available.'));
}

$client = new ClientSession(new EndUser($user));
try {
    $bk->login($client, $bk);
} catch (AccessDenied $e) {
    iudx_keycloak_callback_error($e->getMessage());
}

// Use IUDX-specific keys — NOT oauth2_access_token: client.inc.php would call
// OAuth2Plugin userinfo (wrong host if plugin points elsewhere) and destroy the session.
$_SESSION['iudx_keycloak_access_token'] = $accessToken;
if (!empty($tokenJson['refresh_token'])) {
    $_SESSION['iudx_keycloak_refresh_token'] = $tokenJson['refresh_token'];
}
if (!empty($tokenJson['id_token'])) {
    $_SESSION['iudx_keycloak_id_token'] = $tokenJson['id_token'];
}
unset($_SESSION['oauth2_access_token'], $_SESSION['oauth2_refresh_token']);

// Strip OAuth query params if user lands here via fragment→query redirect
Http::redirect(ROOT_PATH . 'index.php');
exit;
