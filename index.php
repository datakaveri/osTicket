<?php
/*********************************************************************
    index.php

    Helpdesk landing page. Please customize it to fit your needs.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('client.inc.php');

require_once INCLUDE_DIR . 'class.page.php';

$section = 'home';
require(CLIENTINC_DIR.'header.inc.php');
?>


<div style="
    width: 100%;
    background: #ffffff;
    border-bottom: 1px solid #e0e0e0;
    padding: 15px 20px;
    box-sizing: border-box;
    position: relative;
    z-index: 1000;
    clear: both;
    overflow: hidden;
">
    <div style="
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 15px;
    ">

        <!-- Top Row: Seal, Ministers, AI City -->
        <div style="
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        ">
            <!-- Left: Seal + Ministers -->
            <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                <!-- Government Seal -->
                <div style="width: 50px; height: 50px; flex-shrink: 0;">
                    <img src="/osTicket/assets/default/images/TelenganaGovt-logo.svg" style="width: 100%; height: 100%;">
                </div>

                <!-- Chief Minister Card -->
                <div style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    background: #ffffff;
                    padding: 10px 20px;
                    border-radius: 35px;
                    border: 1px solid #d8b167;
                    white-space: nowrap;
                ">
                    <div style="
                        width: 60px;
                        height: 60px;
                        border-radius: 50%;
                        overflow: hidden;
                        border: 2px solid #d8b167;
                        flex-shrink: 0;
                    ">
                        <img src="/osTicket/assets/default/images/honcm.svg" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div>
                        <div style="
                            font-size: 14px;
                            color: #000;
                            font-weight: 700;
                            margin: 0 0 2px 0;
                            line-height: 1;
                        ">Hon'ble Chief Minister</div>
                        <div style="
                            font-size: 13px;
                            color: #333;
                            margin: 0;
                            font-weight: 400;
                            line-height: 1.2;
                        ">Sri Anumula Revanth Reddy</div>
                    </div>
                </div>

                <!-- IT Minister Card -->
                <div style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    background: #ffffff;
                    padding: 10px 20px;
                    border-radius: 35px;
                    border: 1px solid #d8b167;
                    white-space: nowrap;
                ">
                    <div style="
                        width: 60px;
                        height: 60px;
                        border-radius: 50%;
                        overflow: hidden;
                        border: 2px solid #d8b167;
                        flex-shrink: 0;
                    ">
                        <img src="/osTicket/assets/default/images/itmin.svg" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div>
                        <div style="
                            font-size: 14px;
                            color: #000;
                            font-weight: 700;
                            margin: 0 0 2px 0;
                            line-height: 1;
                        ">Hon'ble IT Minister</div>
                        <div style="
                            font-size: 13px;
                            color: #333;
                            margin: 0;
                            font-weight: 400;
                            line-height: 1.2;
                        ">Sri D. Sridhar Babu</div>
                    </div>
                </div>
            </div>

            <!-- Right: AI City -->
            <div style="
                width: 70px;
                height: 55px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            ">
                <img src="/osTicket/assets/default/images/AI-CITY-Logo.svg">
            </div>
        </div>

        <!-- Center Row: TGDeX and Title -->
        <div style="
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            min-width: 300px;
            margin-top: 5px;
            padding-top: 1rem;
        ">
            <img src="/osTicket/assets/default/images/logo.png" style="height: 46px;">
            <div style="
                font-size: 36px;
                color: #333;
                font-weight: 500;
                margin-left: 5px;
            ">Telangana Data Exchange</div>
        </div>

    </div>
</div>



<div id="landing_page">
<?php include CLIENTINC_DIR.'templates/sidebar.tmpl.php'; ?>
<div class="main-content">
    
<?php
if ($cfg && $cfg->isKnowledgebaseEnabled()) { ?>
<div class="search-form">
    <form method="get" action="kb/faq.php">
    <input type="hidden" name="a" value="search"/>
    <input type="text" name="q" class="search" placeholder="<?php echo __('Search our knowledge base'); ?>"/>
    <button type="submit" class="green button"><?php echo __('Search'); ?></button>
    </form>
</div>
<?php } ?>
<div class="thread-body">
<?php
    if($cfg && ($page = $cfg->getLandingPage()))
        echo $page->getBodyWithImages();
    else
        echo  '<h1>'.__('Welcome to the Support Center').'</h1>';
    ?>
    </div>
</div>
<!-- <div class="clear"></div> -->

<div style="display:none" >
<?php
if($cfg && $cfg->isKnowledgebaseEnabled()){
    //FIXME: provide ability to feature or select random FAQs ??
?>
<br/><br/>
<?php
$cats = Category::getFeatured();
if ($cats->all()) { ?>
<h1><?php echo __('Featured Knowledge Base Articles'); ?></h1>
<?php
}

    foreach ($cats as $C) { ?>
    <div class="featured-category front-page">
        <i class="icon-folder-open icon-2x"></i>
        <div class="category-name">
            <?php echo $C->getName(); ?>
        </div>
<?php foreach ($C->getTopArticles() as $F) { ?>
        <div class="article-headline">
            <div class="article-title"><a href="<?php echo ROOT_PATH;
                ?>kb/faq.php?id=<?php echo $F->getId(); ?>"><?php
                echo $F->getQuestion(); ?></a></div>
            <div class="article-teaser"><?php echo $F->getTeaser(); ?></div>
        </div>
<?php } ?>
    </div>
<?php
    }
}

?>

</div>
</div>

<?php
include CLIENTINC_DIR . 'footer.inc.php';
?>


