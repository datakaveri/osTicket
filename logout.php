<?php
/*********************************************************************
    logout.php

    Destroy clients session.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/

require('client.inc.php');
require_once INCLUDE_DIR . 'client/iudx-keycloak-config.inc.php';

$flashPath = ROOT_PATH ?: '/';
setcookie('mahaagx_flash', 'logged_out', time() + 120, $flashPath, '', false, true);

$hadIudxSession = !empty($_SESSION['iudx_keycloak_access_token'])
    || !empty($_SESSION['iudx_keycloak_refresh_token']);
$iudxIdToken = $_SESSION['iudx_keycloak_id_token'] ?? null;

//Check token: Make sure the user actually clicked on the link to logout.
if ($thisclient && $_GET['auth'] && $ost->validateLinkToken($_GET['auth']))
   $thisclient->logOut();

unset(
    $_SESSION['iudx_keycloak_access_token'],
    $_SESSION['iudx_keycloak_refresh_token'],
    $_SESSION['iudx_keycloak_id_token'],
    $_SESSION['oauth2_access_token'],
    $_SESSION['oauth2_refresh_token']
);

osTicketSession::destroyCookie();
session_destroy();

if ($hadIudxSession) {
    header('Location: ' . iudx_keycloak_logout_endpoint(iudx_keycloak_base_url(), $iudxIdToken));
    exit;
}

// fallback
Http::redirect('index.php');
?>
