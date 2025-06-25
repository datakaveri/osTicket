<?php
$title = ($cfg && is_object($cfg) && $cfg->getTitle())
    ? $cfg->getTitle() : 'osTicket :: ' . __('Support Ticket System');
$signin_url = ROOT_PATH . "login.php?do=ext&bk=oauth2.user.p1i1";
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
        <link rel="icon" type="image/png" href="<?php echo ROOT_PATH ?>images/tgdex_favicon.png" sizes="32x32" />
        <link rel="icon" type="image/png" href="<?php echo ROOT_PATH ?>images/tgdex_favicon.png" sizes="16x16" />
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
                <div class="header_items">
                    <p>
                        <?php
                        $current = basename($_SERVER['SCRIPT_NAME']);
                        function activeTabStyle($file)
                        {
                            return basename($_SERVER['SCRIPT_NAME']) === $file
                                ? 'padding:0.5rem; background-color: #f1fffb;border:.5px solid light-dark(rgba(16,16,16,.3),rgba(255,255,255,.3)); border-radius:6.25rem;'
                                : '';
                        }
                        ?>

                        <a href="<?php echo ROOT_PATH; ?>index.php"
                            style=" <?php echo activeTabStyle('index.php'); ?>">
                            <?php echo __('SUPPORT CENTER HOME'); ?>
                        </a>

                        <a href="<?php echo ROOT_PATH; ?>open.php"
                            style=" <?php echo activeTabStyle('open.php'); ?>">
                            <?php echo __('OPEN A NEW TICKET'); ?>
                        </a>

                        <?php
                        if (
                            $thisclient && is_object($thisclient) && $thisclient->isValid()
                            && !$thisclient->isGuest()
                        ) {
                            echo '<a href="' . ROOT_PATH . 'tickets.php" style=" margin-right: 10px; ' . activeTabStyle('tickets.php') . '">' .
                                sprintf(__('TICKETS <b>(%d)</b>'), $thisclient->getNumTickets()) .
                                '</a>';

                            echo '<a style=" padding:0.5rem; border-radius:999px; font-weight:500;   display: inline-block; margin:0rem 1rem;
                border: 1px solid #1f2937;
                color: black;
                padding: 8px 20px;
                font-size: 14px;" href="' . $signout_url . '">' . __('SIGN OUT') . '</a>';

                            $initials = strtoupper(substr($thisclient->getName(), 0, 1) . (strpos($thisclient->getName(), ' ') !== false ? substr($thisclient->getName(), strpos($thisclient->getName(), ' ') + 1, 1) : ''));

                            echo '<a href="' . ROOT_PATH . 'profile.php" style="
    display: inline-flex;
    justify-content: center;
    align-items: center;
    width: 36px;
    height: 36px;
    background-color: #28a745;
    color: white;
    font-size: 0.85em;
    border-radius: 50%;
    font-weight: 600;
    margin: 0 5px;
    text-transform: uppercase;
    text-decoration: none;
                        ">' . Format::htmlchars($initials) . '</a>';
                        } elseif ($nav) {
                            if ($cfg->getClientRegistrationMode() != 'disabled') {
                        ?>
                                <a href="<?php echo $signin_url; ?>"
                                    style="
                display: inline-block;
                border: 1px solid #1f2937;
                color: black;
                padding: 8px 20px;
                font-size: 14px;
                border-radius: 999px;
                text-transform: capitalize;
                font-weight:500;
                margin:0rem 1rem;
           "><?php echo __('SIGN IN'); ?></a>
                            <?php
                            }

                            if ($thisclient && $thisclient->isValid() && $thisclient->isGuest()) {
                                echo '<a href="' . $signout_url . '">' . __('SIGN OUT') . '</a>';
                            } elseif ($cfg->getClientRegistrationMode() == 'public') {
                            ?>
                                <span style="
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 0.5rem;
            font-size: 1rem;
            border-radius: 999px;
            font-weight:600;
            text-transform: capitalize;
            margin-right:5px;
        ">
                                    <?php echo __('Guest User'); ?>
                                </span>
                        <?php
                            }
                        }
                        ?>
                    </p>

                    <p>
                        <?php
                        if (($all_langs = Internationalization::getConfiguredSystemLanguages())
                            && (count($all_langs) > 1)
                        ) {
                            $qs = array();
                            parse_str($_SERVER['QUERY_STRING'], $qs);
                            foreach ($all_langs as $code => $info) {
                                list($lang, $locale) = explode('_', $code);
                                $qs['lang'] = $code;
                        ?>
                                <a class="flag flag-<?php echo strtolower($info['flag'] ?: $locale ?: $lang); ?>"
                                    href="?<?php echo http_build_query($qs);
                                            ?>" title="<?php echo Internationalization::getLanguageDescription($code); ?>">&nbsp;</a>
                        <?php }
                        } ?>
                    </p>
                </div>
                <a class="pull-left" id="logo" href="<?php echo ROOT_PATH; ?>index.php"
                    title="<?php echo __('Support Center'); ?>">
                    <span class="valign-helper"></span>
                    <img src="<?php echo ROOT_PATH; ?>logo.php" border=0 alt="<?php
                                                                                echo $ost->getConfig()->getTitle(); ?>">
                </a>
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