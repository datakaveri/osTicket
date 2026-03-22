<?php
require_once INCLUDE_DIR . 'client/mahaagx-keycloak-url.inc.php';

if (!function_exists('mahaagx_client_header_initials')) {
    function mahaagx_client_header_initials($name) {
        $name = trim((string) $name);
        if ($name === '') {
            return '?';
        }
        if (function_exists('mb_substr') && function_exists('mb_strlen')) {
            $parts = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY);
            if (count($parts) >= 2) {
                return strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1));
            }
            $one = $parts[0];
            $len = mb_strlen($one);
            return strtoupper(mb_substr($one, 0, $len >= 2 ? 2 : 1));
        }
        $parts = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY);
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr($parts[count($parts) - 1], 0, 1));
        }
        $one = $parts[0];
        return strtoupper(substr($one, 0, min(2, strlen($one))));
    }
}

$titleBase = 'Help Desk | MahaAgX';
$pageTitle = ($ost && is_object($ost) && ($pt = $ost->getPageTitle()))
    ? $pt
    : null;
$title = $pageTitle ? ($pageTitle . ' | MahaAgX') : $titleBase;

// Login URL: always use IUDX Keycloak for MahaAgX client sign-in.
$signin_url = MAHAAGX_KEYCLOAK_AUTH_URL;

$signout_url = ROOT_PATH . "logout.php?auth=" . $ost->getLinkToken();
$mahaagxFlash = $_COOKIE['mahaagx_flash'] ?? '';
if ($mahaagxFlash !== '') {
    setcookie('mahaagx_flash', '', time() - 3600, ROOT_PATH ?: '/', '', false, true);
}

header("Content-Type: text/html; charset=UTF-8");
header("Content-Security-Policy: frame-ancestors " . $cfg->getAllowIframes() . "; script-src 'self' 'unsafe-inline'; object-src 'none'");

if (($lang = Internationalization::getCurrentLanguage())) {
    $langs = array_unique(array($lang, $cfg->getPrimaryLanguage()));
    $langs = Internationalization::rfc1766($langs);
    header("Content-Language: " . implode(', ', $langs));
}
?>
<!DOCTYPE html>
<html<?php
        if (
            $lang
            && ($info = Internationalization::getLanguageInfo($lang))
            && (@$info['direction'] == 'rtl')
        )
            echo ' dir="rtl" class="rtl"';
        if ($lang) {
            echo ' lang="' . $lang . '"';
        }

        // Dropped IE Support Warning
        if (osTicket::is_ie())
            $ost->setWarning(__('osTicket no longer supports Internet Explorer.'));
        ?>>

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php echo Format::htmlchars($title); ?></title>
        <meta name="description" content="customer support platform">
        <meta name="keywords" content="osTicket, Customer support system, support ticket system">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/osticket.css" media="screen">
        <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>css/theme.css" media="screen">
        <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>css/print.css" media="print">
        <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/typeahead.css"
            media="screen" />
        <link type="text/css" href="<?php echo ROOT_PATH; ?>css/ui-lightness/jquery-ui-1.13.2.custom.min.css"
            rel="stylesheet" media="screen" />
        <link rel="stylesheet" href="<?php echo ROOT_PATH ?>css/jquery-ui-timepicker-addon.css" media="all">
        <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/thread.css" media="screen">
        <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/redactor.css" media="screen">
        <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/font-awesome.min.css">
        <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/flags.css">
        <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/rtl.css" />
        <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/select2.min.css">
        <!-- Favicons -->
        <link rel="icon" type="image/png" href="<?php echo ROOT_PATH ?>assets/default/images/mahaagx/mahaagx-logo.svg" sizes="32x32" />
        <!-- <link rel="icon" type="image/png" href="<?php echo ROOT_PATH ?>images/tgdex_favicon.png" sizes="16x16" /> -->
        <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-3.7.0.min.js"></script>
        <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-ui-1.13.2.custom.min.js"></script>
        <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-ui-timepicker-addon.js"></script>
        <script src="<?php echo ROOT_PATH; ?>js/osticket.js"></script>
        <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/filedrop.field.js"></script>
        <script src="<?php echo ROOT_PATH; ?>js/bootstrap-typeahead.js"></script>
        <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor.min.js"></script>
        <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor-plugins.js"></script>
        <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor-osticket.js"></script>
        <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/select2.min.js"></script>
        <style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap');
</style>
        <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Caladea:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
        <?php
        if ($ost && ($headers = $ost->getExtraHeaders())) {
            echo "\n\t" . implode("\n\t", $headers) . "\n";
        }

        // Offer alternate links for search engines
        // @see https://support.google.com/webmasters/answer/189077?hl=en
        if (($all_langs = Internationalization::getConfiguredSystemLanguages())
            && (count($all_langs) > 1)
        ) {
            $langs = Internationalization::rfc1766(array_keys($all_langs));
            $qs = array();
            parse_str($_SERVER['QUERY_STRING'], $qs);
            foreach ($langs as $L) {
                $qs['lang'] = $L; ?>
                <link rel="alternate" href="//<?php echo $_SERVER['HTTP_HOST'] . htmlspecialchars($_SERVER['REQUEST_URI']); ?>?<?php
                                                                                                                                echo http_build_query($qs); ?>" hreflang="<?php echo $L; ?>" />
            <?php
            } ?>
            <link rel="alternate" href="//<?php echo $_SERVER['HTTP_HOST'] . htmlspecialchars($_SERVER['REQUEST_URI']); ?>"
                hreflang="x-default" />
        <?php
        }
        ?>
    </head>
    <script>
        function toggleMenu() {
            var nav = document.getElementById('headerNav');
            nav.classList.toggle('show');
        }
    </script>

    <body<?php echo (basename($_SERVER['SCRIPT_NAME'] ?? '') === 'login.php') ? ' class="page-login"' : ''; ?>>
        <?php /* Keycloak response_mode=fragment: #code= never reaches PHP — forward to callback */ ?>
        <script>
        (function () {
            try {
                var h = window.location.hash;
                if (!h || h.indexOf('code=') === -1) return;
                var q = h.charAt(0) === '#' ? h.substring(1) : h;
                window.location.replace(<?php echo json_encode(ROOT_PATH . 'keycloak-callback.php?'); ?> + q);
            } catch (e) {}
        })();
        </script>
        <?php if ($mahaagxFlash === 'logged_out') { ?>
        <div class="toaster--container" id="mahaToastContainer" aria-live="polite" aria-atomic="true">
            <div class="toaster--item toaster--success" id="mahaToast" role="status">
                <div class="toaster--content">
                    <div class="toaster--icon" aria-hidden="true">
                        <svg
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="toaster--message"><?php echo __('You\'ve logged out successfully'); ?></div>
                    <button class="toaster--close" type="button" aria-label="<?php echo __('Close notification'); ?>" onclick="window.dismissMahaToast && window.dismissMahaToast()">
                        <svg
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="toaster--progress">
                    <div class="toaster--progress-bar"></div>
                </div>
            </div>
        </div>
        <script>
        (function () {
            var toast = document.getElementById('mahaToast');
            if (!toast) return;
            var removeToast = function () {
                toast.classList.add('removing');
                window.setTimeout(function () {
                    if (toast && toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 280);
            };
            window.dismissMahaToast = removeToast;
            window.setTimeout(removeToast, 3600);
        })();
        </script>
        <?php } ?>
        <div id="container">
            <?php
            if ($ost->getError())
                echo sprintf('<div class="error_bar">%s</div>', $ost->getError());
            elseif ($ost->getWarning())
                echo sprintf('<div class="warning_bar">%s</div>', $ost->getWarning());
            elseif ($ost->getNotice())
                echo sprintf('<div class="notice_bar">%s</div>', $ost->getNotice());
            ?>
            <div id="header">
                <div style="display: flex; align-items:center;flex-direction:row-reverse">
                    <div class="show-guest-mob">
                        <?php
                        if ($thisclient && is_object($thisclient) && $thisclient->isValid()) {
                            if (!$thisclient->isGuest()) {
                                $mobInitials = mahaagx_client_header_initials($thisclient->getName());
                                $orgMob = $thisclient->canSeeOrgTickets();
                                $mobTicketTotal = (int) $thisclient->getNumOpenTickets($orgMob)
                                    + (int) $thisclient->getNumClosedTickets($orgMob);
                                echo '<a class="tickets_link_header_mob" href="' . ROOT_PATH . 'tickets.php">'
                                    . __('Tickets') . ' (' . $mobTicketTotal . ')</a> ';
                                echo '<a class="signout_btn_header_mob" href="' . Format::htmlchars($signout_url) . '">'
                                    . __('SIGN OUT') . '</a> ';
                                echo '<span class="user_avatar_mob" title="'
                                    . Format::htmlchars($thisclient->getName()) . '">'
                                    . Format::htmlchars($mobInitials) . '</span>';
                            } else {
                                // Guest user: show sign out
                                echo '<a href="' . $signout_url . '">' . __('SIGN OUT') . '</a>';
                            }
                        } elseif ($cfg->getClientRegistrationMode() == 'public') {
                            // Public registration mode and not logged in: show 'GU'
                            echo '<span class="guest_user_header_mob">' . __('GU') . '</span>';
                        }
                        ?>
                    </div>

                    <!-- Responsive Menu Button -->
                    <button class="menu-button" onclick="toggleMenu()">Menu ⋮</button>

                    <div class="header_items" id="headerNav">
                        <p>

                            <?php
                            $current = basename($_SERVER['SCRIPT_NAME']);
                            function activeTabClass($file)
                            {
                                return basename($_SERVER['SCRIPT_NAME']) === $file ? 'active_tab_header' : '';
                            }
                            ?>
                            <a href="<?php echo ROOT_PATH; ?>index.php"
                                class="<?php echo activeTabClass('index.php'); ?>">
                                <?php echo __('Home'); ?>
                            </a>

                            <a href="<?php echo ROOT_PATH; ?>open.php"
                                class="<?php echo activeTabClass('open.php'); ?>">
                                <?php echo __('New Ticket'); ?>
                            </a>

                            <a href="https://mahaagx.maharashtra.gov.in/about-mahaagx" target="_blank">
                                <?php echo __('About MahaAgX'); ?>
                            </a>

                            <a href="https://mahaagx.maharashtra.gov.in/datasets" target="_blank">
                                <?php echo __('Datasets'); ?>
                            </a>

                            <a href="https://mahaagx.maharashtra.gov.in/models" target="_blank">
                                <?php echo __('Models'); ?>
                            </a>

                            <a href="https://mahaagx.maharashtra.gov.in/usecases" target="_blank">
                                <?php echo __('Usecases'); ?>
                            </a>

                            <a href="https://mahaagx.maharashtra.gov.in/challenges" target="_blank">
                                <?php echo __('Challenges'); ?>
                            </a>

                            <?php
                            if ($thisclient && is_object($thisclient) && $thisclient->isValid() && !$thisclient->isGuest()) {
                                $orgHdr = $thisclient->canSeeOrgTickets();
                                $hdrTicketTotal = (int) $thisclient->getNumOpenTickets($orgHdr)
                                    + (int) $thisclient->getNumClosedTickets($orgHdr);
                                $hdrInitials = mahaagx_client_header_initials($thisclient->getName());
                                $profileName = $thisclient->getName();
                                ?>
                                <span class="header-user-profile" role="group" aria-label="<?php echo Format::htmlchars(__('Your account')); ?>">
                                    <a href="<?php echo ROOT_PATH; ?>tickets.php"
                                        class="tickets_count_header <?php echo activeTabClass('tickets.php'); ?>">
                                        <?php echo __('Tickets'); ?> (<?php echo (int) $hdrTicketTotal; ?>)
                                    </a>
                                    <a class="signout_btn_header" href="<?php echo Format::htmlchars($signout_url); ?>"><?php echo __('SIGN OUT'); ?></a>
                                    <span class="user_avatar_header"
                                        title="<?php echo Format::htmlchars($profileName); ?>"><?php echo Format::htmlchars($hdrInitials); ?></span>
                                </span>
                            <?php
                            } else {
                                // Logged out state — show MahaAgX-style Login / Register buttons
                                if ($cfg->getClientRegistrationMode() != 'disabled') {
                                    $register_url = MAHAAGX_KEYCLOAK_REGISTER_URL;
                            ?>
                                    <a href="<?php echo Format::htmlchars($signin_url); ?>" class="signin_btn_header"><?php echo __('Login'); ?></a>
                                    <a href="<?php echo Format::htmlchars($register_url); ?>" class="register_btn_header"><?php echo __('Register'); ?></a>
                                <?php
                                }
                            }
                            ?>
                        </p>

                        <p>
                            <?php
                            if (($all_langs = Internationalization::getConfiguredSystemLanguages()) && count($all_langs) > 1) {
                                $qs = array();
                                parse_str($_SERVER['QUERY_STRING'], $qs);
                                foreach ($all_langs as $code => $info) {
                                    list($lang, $locale) = explode('_', $code);
                                    $qs['lang'] = $code;
                            ?>
                                    <a class="flag flag-<?php echo strtolower($info['flag'] ?: $locale ?: $lang); ?>"
                                        href="?<?php echo http_build_query($qs); ?>"
                                        title="<?php echo Internationalization::getLanguageDescription($code); ?>">&nbsp;</a>
                            <?php }
                            } ?>
                        </p>
                    </div>
                </div>
                <div class="logo_container_header">
                    <!-- <a class="pull-left" style="" id="logo" href="https://tgdex.telangana.gov.in/" title="<?php echo __('Support Center'); ?>">
                        <span class="valign-helper"></span>
                        <img src="../assets/default/images/forest-logo.svg" class="tg_govt_header">
                    </a> -->
                    <a class="pull-left" id="logo" href="<?php echo ROOT_PATH; ?>" title="<?php echo __('Support Center'); ?>">
                            <span class="valign-helper"></span>
                            <img src="<?php echo ROOT_PATH ?>assets/default/images/mahaagx/mahaagx-logo.svg" class="tg_govt_header">
                        </a>
                        <!-- Removed previous Forest Stack Rajasthan text branding -->
                    <!-- <span class="tg_short_logo_header">
                        <a class="pull-left" style="" id="logo" href="https://tgdex.telangana.gov.in/" title="<?php echo __('Support Center'); ?>">
                            <span class="valign-helper"></span>
                            <img src="../assets/default/images/tgdex_short_logo_Green.svg">
                        </a>
                    </span>
                    <span class="tg_big_logo_header">

                        <a class="pull-left" id="logo" href="https://tgdex.telangana.gov.in/" title="<?php echo __('Support Center'); ?>">
                            <span class="valign-helper"></span>
                            <img src="<?php echo ROOT_PATH; ?>logo.php" border=0 alt="<?php echo $ost->getConfig()->getTitle(); ?>">
                        </a>
                    </span> -->

                </div>

            </div>

            <div class="clear"></div>

            <div id="content">

                <?php
                if (!empty($_GET['sso_error']) && !empty($_GET['msg'])) {
                    echo '<div id="msg_error">' . Format::htmlchars($_GET['msg']) . '</div>';
                }
                ?>
                <?php if ($errors['err']) { ?>
                    <div id="msg_error"><?php echo $errors['err']; ?></div>
                <?php } elseif ($msg) { ?>
                    <div id="msg_notice"><?php echo $msg; ?></div>
                <?php } elseif ($warn) { ?>
                    <div id="msg_warning"><?php echo $warn; ?></div>
                <?php } ?>