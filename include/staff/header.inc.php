<?php
header("Content-Type: text/html; charset=UTF-8");
header("Content-Security-Policy: frame-ancestors " . $cfg->getAllowIframes() . "; script-src 'self' 'unsafe-inline' 'unsafe-eval'; object-src 'none'");

$title = ($ost && ($title = $ost->getPageTitle()))
    ? $title : ('osTicket :: ' . __('Staff Control Panel'));

if (!isset($_SERVER['HTTP_X_PJAX'])) { ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
    <html<?php
            if (($lang = Internationalization::getCurrentLanguage())
                && ($info = Internationalization::getLanguageInfo($lang))
                && (@$info['direction'] == 'rtl')
            )
                echo ' dir="rtl" class="rtl"';
            if ($lang) {
                echo ' lang="' . Internationalization::rfc1766($lang) . '"';
            }

            // Dropped IE Support Warning
            if (osTicket::is_ie())
                $ost->setWarning(__('osTicket no longer supports Internet Explorer.'));
            ?>>

        <head>
            <meta http-equiv="content-type" content="text/html; charset=UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
            <meta http-equiv="cache-control" content="no-cache" />
            <meta http-equiv="pragma" content="no-cache" />
            <meta http-equiv="x-pjax-version" content="<?php echo GIT_VERSION; ?>">
            <title><?php echo Format::htmlchars($title); ?></title>
            <style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap');
</style>

            <!--[if IE]>
    <style type="text/css">
        .tip_shadow { display:block !important; }
    </style>
    <![endif]-->
            <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-3.7.0.min.js"></script>
            <link rel="stylesheet" href="<?php echo ROOT_PATH ?>css/thread.css" media="all">
            <link rel="stylesheet" href="<?php echo ROOT_PATH ?>scp/css/scp.css" media="all">
            <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/redactor.css" media="screen">
            <link rel="stylesheet" href="<?php echo ROOT_PATH ?>css/typeahead.css" media="screen">
            <link type="text/css" href="<?php echo ROOT_PATH; ?>css/ui-lightness/jquery-ui-1.13.2.custom.min.css"
                rel="stylesheet" media="screen" />
            <link rel="stylesheet" href="<?php echo ROOT_PATH ?>css/jquery-ui-timepicker-addon.css" media="all">
            <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/font-awesome.min.css">
            <!--[if IE 7]>
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/font-awesome-ie7.min.css">
    <![endif]-->
            <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH ?>scp/css/dropdown.css">
            <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/loadingbar.css" />
            <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/flags.css">
            <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/select2.min.css">
            <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/rtl.css" />
            <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH ?>scp/css/translatable.css" />
            <!-- Favicons -->
            <link rel="icon" type="image/png" href="<?php echo ROOT_PATH ?>assets/default/images/forest-logo.svg" sizes="32x32" />
            <!-- <link rel="icon" type="image/png" href="<?php echo ROOT_PATH ?>images/tgdex_favicon.png" sizes="16x16" /> -->

            <?php
            if ($ost && ($headers = $ost->getExtraHeaders())) {
                echo "\n\t" . implode("\n\t", $headers) . "\n";
            }
            ?>
        </head>

        <body>
            <div id="container">
                <?php
                if ($ost->getError())
                    echo sprintf('<div id="error_bar">%s</div>', $ost->getError());
                elseif ($ost->getWarning())
                    echo sprintf('<div id="warning_bar">%s</div>', $ost->getWarning());
                elseif ($ost->getNotice())
                    echo sprintf('<div id="notice_bar">%s</div>', $ost->getNotice());
                ?>
                <div id="header">
                    <div style="display: flex; align-items:center; justify-content: space-between; width: 100%;">
                        <div style="display: flex; align-items: center; flex: 1;">
                            <div class="logo_container_header">
                                <a class="pull-left" id="logo" href="https://dev.catalogue.forest.iudx.io/" title="<?php echo __('Support Center'); ?>">
                                    <span class="valign-helper"></span>
                                    <img src="<?php echo ROOT_PATH ?>assets/default/images/forest-logo.svg" class="tg_govt_header">
                                </a>
                                <div class="logo_title">
                                    <p class="logo_title_name">Forest Stack</p>
                                    <p class="logo_title_department">Rajasthan</p>
                                </div>
                            </div>

                            <!-- Client Navigation Menu -->
                            <div class="staff-client-nav">
                                <a href="<?php echo ROOT_PATH; ?>index.php" class="no-pjax">
                                    <?php echo __('Home'); ?>
                                </a>
                                <a href="<?php echo ROOT_PATH; ?>open.php" class="no-pjax">
                                    <?php echo __('New Ticket'); ?>
                                </a>
                                <a href="https://forest-stack.digivan.forest.rajasthan.gov.in/datasets" target="_blank">
                                    <?php echo __('Datasets'); ?>
                                </a>
                                <a href="https://forest-stack.digivan.forest.rajasthan.gov.in/models" target="_blank">
                                    <?php echo __('Models'); ?>
                                </a>
                                <a href="https://forest-stack.digivan.forest.rajasthan.gov.in/usecases" target="_blank">
                                    <?php echo __('Use Cases'); ?>
                                </a>
                                <a href="https://forest-stack.digivan.forest.rajasthan.gov.in/innovations" target="_blank">
                                    <?php echo __('Challenges'); ?>
                                </a>
                                <a href="https://forest-stack.digivan.forest.rajasthan.gov.in/about-us" target="_blank">
                                    <?php echo __('About Us'); ?>
                                </a>
                            </div>
                        </div>

                        <div class="header_items" id="headerNav">
                            <p id="info" class="pull-right no-pjax">
                                <?php
                                if ($thisstaff->isAdmin() && !defined('ADMINPAGE')) { ?>
                                    <a href="<?php echo ROOT_PATH ?>scp/admin.php" class="no-pjax"><?php echo __('Admin Panel'); ?></a>
                                <?php } else { ?>
                                    <a href="<?php echo ROOT_PATH ?>scp/index.php" class="no-pjax"><?php echo __('Agent Panel'); ?></a>
                                <?php } ?>
                                <a href="<?php echo ROOT_PATH ?>scp/profile.php"><?php echo __('Profile'); ?></a>
                                <a href="<?php echo ROOT_PATH ?>scp/logout.php?auth=<?php echo $ost->getLinkToken(); ?>" class="no-pjax signout_btn_header"><?php echo __('Log Out'); ?></a>
                                <?php $initials = strtoupper(substr($thisstaff->getName(), 0, 1) . (strpos($thisstaff->getName(), ' ') !== false ? substr($thisstaff->getName(), strpos($thisstaff->getName(), ' ') + 1, 1) : ''));
                                echo '<a class="user_avatar_header">' . Format::htmlchars($initials) . '</a>'; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div id="pjax-container" class="<?php if ($_POST) echo 'no-pjax'; ?>">
                    <?php } else {
                    header('X-PJAX-Version: ' . GIT_VERSION);
                    if ($pjax = $ost->getExtraPjax()) { ?>
                        <script type="text/javascript">
                            <?php foreach (array_filter($pjax) as $s) echo $s . ";"; ?>
                        </script>
                    <?php }
                    foreach ($ost->getExtraHeaders() as $h) {
                        if (strpos($h, '<script ') !== false)
                            echo $h;
                    } ?>
                    <title><?php echo ($ost && ($title = $ost->getPageTitle())) ? $title : 'TGDeX :: ' . __('Staff Control Panel'); ?></title><?php
                                                                                                                                            } # endif X_PJAX 
                                                                                                                                                ?>
                <ul id="nav">
                    <?php include STAFFINC_DIR . "templates/navigation.tmpl.php"; ?>
                </ul>
                <?php include STAFFINC_DIR . "templates/sub-navigation.tmpl.php"; ?>

                <div id="content">
                    <?php if (isset($errors['err'])) { ?>
                        <div id="msg_error"><?php echo $errors['err']; ?></div>
                    <?php } elseif ($msg) { ?>
                        <div id="msg_notice"><?php echo $msg; ?></div>
                    <?php } elseif ($warn) { ?>
                        <div id="msg_warning"><?php echo $warn; ?></div>
                    <?php }
                    foreach (Messages::getMessages() as $M) { ?>
                        <div class="<?php echo strtolower($M->getLevel()); ?>-banner"><?php
                                                                                        echo (string) $M; ?></div>
                    <?php   } ?>