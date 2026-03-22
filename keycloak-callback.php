<?php
/**
 * Keycloak OAuth2/OIDC redirect URI handler — exchanges ?code= for osTicket client session.
 * Set IUDX_KEYCLOAK_REDIRECT_URI to this file's URL in Keycloak (angular-client).
 */
require_once dirname(__FILE__) . '/client.inc.php';
require_once INCLUDE_DIR . 'client/iudx-keycloak-callback.inc.php';
