<?php
/**
 * MahaAgX Keycloak settings (shared by auth URL builder and token callback).
 * Redirect URI should be stable in production to avoid callback mismatches.
 */
if (!defined('IUDX_KEYCLOAK_BASE')) {
    define('IUDX_KEYCLOAK_BASE', 'https://mahaagx.maharashtra.gov.in/auth');
}
if (!defined('IUDX_KEYCLOAK_REALM')) {
    define('IUDX_KEYCLOAK_REALM', 'mahaagx-prod');
}
if (!defined('IUDX_KEYCLOAK_CLIENT_ID')) {
    define('IUDX_KEYCLOAK_CLIENT_ID', 'angular-client');
}
/** Optional; leave empty for public client */
if (!defined('IUDX_KEYCLOAK_CLIENT_SECRET')) {
    define('IUDX_KEYCLOAK_CLIENT_SECRET', '');
}
if (!defined('IUDX_KEYCLOAK_PROD_HOST')) {
    define('IUDX_KEYCLOAK_PROD_HOST', 'mahaagx.maharashtra.gov.in');
}
if (!function_exists('iudx_keycloak_current_origin')) {
    function iudx_keycloak_current_origin() {
        $scheme = osTicket::is_https() ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        return $scheme . '://' . $host;
    }
}
if (!function_exists('iudx_keycloak_base_url')) {
    function iudx_keycloak_base_url() {
        $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
        if ($host === IUDX_KEYCLOAK_PROD_HOST) {
            return 'https://' . IUDX_KEYCLOAK_PROD_HOST
                . rtrim(ROOT_PATH, '/') . '/';
        }
        return rtrim(iudx_keycloak_current_origin(), '/')
            . rtrim(ROOT_PATH, '/') . '/';
    }
}
if (!function_exists('iudx_keycloak_callback_url')) {
    function iudx_keycloak_callback_url() {
        $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
        if ($host === IUDX_KEYCLOAK_PROD_HOST) {
            return 'https://' . IUDX_KEYCLOAK_PROD_HOST
                . rtrim(ROOT_PATH, '/') . '/keycloak-callback.php';
        }
        return rtrim(iudx_keycloak_current_origin(), '/')
            . rtrim(ROOT_PATH, '/') . '/keycloak-callback.php';
    }
}
/**
 * Must match Keycloak client redirect URI. Prefer dedicated callback so PHP receives ?code=
 * (fragments #code= are invisible to the server).
 */
if (!defined('IUDX_KEYCLOAK_REDIRECT_URI')) {
    define('IUDX_KEYCLOAK_REDIRECT_URI', iudx_keycloak_callback_url());
}

if (!function_exists('iudx_keycloak_token_endpoint')) {
    function iudx_keycloak_token_endpoint() {
        return IUDX_KEYCLOAK_BASE . '/realms/' . IUDX_KEYCLOAK_REALM . '/protocol/openid-connect/token';
    }
}
if (!function_exists('iudx_keycloak_userinfo_endpoint')) {
    function iudx_keycloak_userinfo_endpoint() {
        return IUDX_KEYCLOAK_BASE . '/realms/' . IUDX_KEYCLOAK_REALM . '/protocol/openid-connect/userinfo';
    }
}
if (!function_exists('iudx_keycloak_logout_endpoint')) {
    function iudx_keycloak_logout_endpoint($post_logout_redirect_uri=null, $id_token_hint=null) {
        $params = array(
            'client_id' => IUDX_KEYCLOAK_CLIENT_ID,
        );
        if ($id_token_hint) {
            $params['id_token_hint'] = $id_token_hint;
        }
        if ($post_logout_redirect_uri) {
            $params['post_logout_redirect_uri'] = $post_logout_redirect_uri;
        }
        return IUDX_KEYCLOAK_BASE . '/realms/' . IUDX_KEYCLOAK_REALM
            . '/protocol/openid-connect/logout?'
            . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }
}
