<?php
/**
 * MahaAgX Keycloak settings (shared by auth URL builder and token callback).
 * Set IUDX_KEYCLOAK_REDIRECT_URI to the exact URL registered in Keycloak
 * "Valid redirect URIs" for angular-client (must match byte-for-byte).
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
/**
 * Must match Keycloak client redirect URI. Prefer dedicated callback so PHP receives ?code=
 * (fragments #code= are invisible to the server).
 */
if (!defined('IUDX_KEYCLOAK_REDIRECT_URI')) {
    define('IUDX_KEYCLOAK_REDIRECT_URI', 'http://localhost/osticket/keycloak-callback.php');
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
