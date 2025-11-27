<?php
$title = ($cfg && is_object($cfg) && $cfg->getTitle())
    ? $cfg->getTitle() : 'osTicket :: ' . __('Support Ticket System');

// Find OAuth2 plugin instance dynamically
$signin_url = ROOT_PATH . "login.php";
$oauth2_plugin = null;

// Only try to find OAuth2 plugin if the class exists
if (class_exists('OAuth2Plugin')) {
    foreach (PluginManager::allInstalled() as $path => $plugin) {
        if ($plugin instanceof OAuth2Plugin && $plugin->isActive()) {
            $oauth2_plugin = $plugin;
            break;
        }
    }
    if ($oauth2_plugin) {
        // Get the first active instance of the plugin
        $instances = $oauth2_plugin->getActiveInstances();
        if ($instances && $instances->count() > 0) {
            $instance = $instances->first();
            $signin_url = ROOT_PATH . "login.php?do=ext&bk=oauth2.user.p" . $oauth2_plugin->getId() . "i" . $instance->getId();
        }
    }
}
$signout_url = ROOT_PATH . "logout.php?auth=" . $ost->getLinkToken();

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
        <link rel="icon" type="image/png" href="<?php echo ROOT_PATH ?>assets/default/images/forest-logo.svg" sizes="32x32" />
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

    <body>
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
                                // Logged-in user (not guest): show initials
                                $initials = strtoupper(substr($thisclient->getName(), 0, 1) .
                                    (strpos($thisclient->getName(), ' ') !== false ? substr($thisclient->getName(), strpos($thisclient->getName(), ' ') + 1, 1) : ''));
                                echo '<span class="user_avatar_mob">' . Format::htmlchars($initials) . '</span>';
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
                                <?php echo __('SUPPORT CENTER HOME'); ?>
                            </a>

                            <a href="<?php echo ROOT_PATH; ?>open.php"
                                class="<?php echo activeTabClass('open.php'); ?>">
                                <?php echo __('OPEN A NEW TICKET'); ?>
                            </a>

                            <?php
                            if ($thisclient && is_object($thisclient) && $thisclient->isValid() && !$thisclient->isGuest()) {
                                echo '<a href="' . ROOT_PATH . 'tickets.php" class="ticket_link_header ' . activeTabClass('tickets.php') . '">' .
                                    sprintf(__('TICKETS <b>(%d)</b>'), $thisclient->getNumTickets()) .
                                    '</a>';

                                echo '<a class="signout_btn_header" href="' . $signout_url . '">' . __('SIGN OUT') . '</a>';

                                $initials = strtoupper(substr($thisclient->getName(), 0, 1) .
                                    (strpos($thisclient->getName(), ' ') !== false ? substr($thisclient->getName(), strpos($thisclient->getName(), ' ') + 1, 1) : ''));

                                echo '<a class="user_avatar_header">' . Format::htmlchars($initials) . '</a>';
                            } elseif ($nav) {
                                if ($cfg->getClientRegistrationMode() != 'disabled') {
                            ?>
                                    <a style="color: #4d7c0f;" href="<?php echo $signin_url; ?>" class="signin_btn_header"><?php echo __('SIGN IN'); ?></a>
                                <?php
                                }

                                if ($thisclient && $thisclient->isValid() && $thisclient->isGuest()) {
                                    echo '<a href="' . $signout_url . '">' . __('SIGN OUT') . '</a>';
                                } elseif ($cfg->getClientRegistrationMode() == 'public') {
                                ?>
                                    <span class="guest_user_header">
                                        <?php echo __('Guest User'); ?>
                                    </span>
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
                    <a class="pull-left" id="logo" href="https://forest-stack.digivan.forest.rajasthan.gov.in/" title="<?php echo __('Support Center'); ?>">
                            <span class="valign-helper"></span>
                            <img src="<?php echo ROOT_PATH ?>assets/default/images/forest-logo.svg" class="tg_govt_header">
                        </a>
                        <div class="logo_title">
                            <p style="font-weight:700" class="logo_title_name">Forest Stack</p>
                            <p style="font-weight:500" class="logo_title_department">Rajasthan</p>
                        </div>
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

                <?php if ($errors['err']) { ?>
                    <div id="msg_error"><?php echo $errors['err']; ?></div>
                <?php } elseif ($msg) { ?>
                    <div id="msg_notice"><?php echo $msg; ?></div>
                <?php } elseif ($warn) { ?>
                    <div id="msg_warning"><?php echo $warn; ?></div>
                <?php } ?>