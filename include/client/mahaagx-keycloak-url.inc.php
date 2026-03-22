<?php
/**
 * Keycloak authorization URLs for Login / Register links.
 *
 * Uses response_mode=query so ?code= reaches PHP (see keycloak-callback.php).
 * Session stores state + PKCE verifier + redirect_uri for the token exchange.
 */
require_once dirname(__FILE__) . '/iudx-keycloak-config.inc.php';

if (!defined('MAHAAGX_KEYCLOAK_AUTH_URL')) {
    $authEndpoint = IUDX_KEYCLOAK_BASE . '/realms/' . IUDX_KEYCLOAK_REALM . '/protocol/openid-connect/auth';
    $state = bin2hex(random_bytes(16));
    $params = array(
        'client_id' => IUDX_KEYCLOAK_CLIENT_ID,
        'redirect_uri' => IUDX_KEYCLOAK_REDIRECT_URI,
        'state' => $state,
        'response_mode' => 'query',
        'response_type' => 'code',
        'scope' => 'openid email profile',
        'nonce' => bin2hex(random_bytes(16)),
        'prompt' => 'login',
    );
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION['iudx_keycloak_oauth_state'] = $state;
        $_SESSION['iudx_keycloak_redirect_uri_used'] = IUDX_KEYCLOAK_REDIRECT_URI;
        $verifier = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
        $_SESSION['iudx_keycloak_pkce_verifier'] = $verifier;
        $params['code_challenge'] = $challenge;
        $params['code_challenge_method'] = 'S256';
    }
    define(
        'MAHAAGX_KEYCLOAK_AUTH_URL',
        $authEndpoint . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986)
    );
}

if (!defined('MAHAAGX_KEYCLOAK_REGISTER_URL')) {
    $authEndpoint = IUDX_KEYCLOAK_BASE . '/realms/' . IUDX_KEYCLOAK_REALM . '/protocol/openid-connect/auth';
    $state = bin2hex(random_bytes(16));
    $params = array(
        'client_id' => IUDX_KEYCLOAK_CLIENT_ID,
        'redirect_uri' => IUDX_KEYCLOAK_REDIRECT_URI,
        'state' => $state,
        'response_mode' => 'query',
        'response_type' => 'code',
        'scope' => 'openid email profile',
        'nonce' => bin2hex(random_bytes(16)),
        'kc_action' => 'register',
    );
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION['iudx_keycloak_oauth_state_register'] = $state;
        $_SESSION['iudx_keycloak_redirect_uri_used_register'] = IUDX_KEYCLOAK_REDIRECT_URI;
        $verifier = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
        $_SESSION['iudx_keycloak_pkce_verifier_register'] = $verifier;
        $params['code_challenge'] = $challenge;
        $params['code_challenge_method'] = 'S256';
    }
    define(
        'MAHAAGX_KEYCLOAK_REGISTER_URL',
        $authEndpoint . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986)
    );
}
