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
 * *********************************************************************/
require('client.inc.php');

// Keycloak OIDC: if "Valid redirect URI" is the site root, ?code= lands on index.php
if (!empty($_GET['code']) && isset($_GET['state'])) {
    require_once INCLUDE_DIR . 'client/iudx-keycloak-callback.inc.php';
}

require_once INCLUDE_DIR . 'class.page.php';

$section = 'home';
require(CLIENTINC_DIR . 'header.inc.php');
?>


<div style="
    width: 100%;
    background: #ffffff;
    border-bottom: 1px solid #e0e0e0;
    /* padding: 0 20px 20px 0 !important; */
    box-sizing: border-box;
    position: relative;
    z-index: 999;
    clear: both;
    overflow: hidden;
" class="header_wrapper">
    <style>
        @media screen and (max-width: 768px) {
            .top_icons_wrapper {
                flex-direction: row !important;
                justify-content: space-between !important;
                flex-wrap: wrap !important;
                gap: 10px !important;
                position: absolute;
                right: 0px;
                left: -15px;
            }

            .icons_group_left {
                display: flex !important;
                flex-direction: row !important;
                align-items: center;
                gap: 10px !important;
                flex-wrap: wrap;
                justify-content: center;
            }

            .minister_card {
                padding: 0 !important;
                border: none !important;
                border-radius: 50% !important;
                background: transparent !important;
            }

            .minister_text {
                display: none !important;
            }

            .tgdex_wrapper {
                flex-direction: column !important;
                align-items: center !important;
                margin-top: 10px !important;
                text-align: center;
                margin-top: 0   rem !important;
            }

            .tgdex_text {
                display: none;
            }

            .tgdex_wrapper img {
                height: 328px !important;
                width:auto;
                
            }

            .seal_wrapper,
            .minister_img_wrapper,
            .ai_city_wrapper {
                width: 48px !important;
                height: 48px !important;
            }

            .seal_wrapper img,
            .minister_img_wrapper img {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover;
                border-radius: 50%;
                border: 1px solid #d8b167;
                padding: 2px;
            }

            #landing_page {
                flex-direction: column-reverse;
            }

            .main-content_index {
                width: auto;
            }

            .blue.button {}


            .banner-container {
                background-size: cover;
                background-position: center;
                height: 1187px;
            }

            .banner-content {
                max-width: 688px;
                display:flex;
                flex-direction: column;
            }

            .banner-content-left-section {
                /* text-align: center !important; */

                padding: 20px !important;
        width: 100%;
        margin-right: 0;
            }

            .banner-content-left-section h1 {
                font-size: 30px !important;
                letter-spacing: .32px;
                
            }
            .agricultural-text{
                font-size: 30px !important;
        letter-spacing: .32px;
            }
            .description{
                font-size: 16px !important;
        font-weight: 500;
                }
                .banner-content-right-section{
                    background-size: cover !important;
        background-position: center !important;
        min-height: 334px !important;
        /* margin-top: 32px !important; */
        margin-right:0;
        /* padding-bottom: 72px !important; */
        width:100%;
                }
        }
    </style>

    <div style="max-width: 100%; display: flex; flex-direction: column; gap: 15px;">

        <!-- <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;" class="top_icons_wrapper"> -->

        <!-- Chief Minister Card - Left Side -->
        <!-- <div style="display: flex; align-items: center; gap: 10px; background: #ffffff; padding: 0.25rem 0.5rem; padding-right: 1.5rem; border-radius: 40px; border: 1px solid #d8b167; white-space: nowrap; margin-left: 1rem" class="minister_card cm_card">
                <div style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; flex-shrink: 0;" class="minister_img_wrapper">
                    <img src="../assets/default/images/honcm.svg" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="minister_text" style="padding-left: 0.5rem;">
                    <div style="font-size: 13px; color: #000; font-weight: 700; margin: 0 0 2px 0; line-height: 1; padding-bottom: 0.25rem;font-family: sans-serif">Sri Anumula Revanth Reddy</div>
                    <div style="font-size: 13px; color: #333; margin: 0; font-weight: 400; line-height: 1.2;">Hon'ble Chief Minister</div>
                </div>
            </div> -->

        <!-- IT Minister Card - Right Side -->
        <!-- <div style="display: flex; align-items: center; gap: 10px; background: #ffffff; padding: 0.25rem 0.5rem; padding-right: 0.5rem; border-radius: 40px; border: 1px solid #d8b167;" class="minister_card it_card">
                <div style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; flex-shrink: 0;" class="minister_img_wrapper">
                    <img src="../assets/default/images/itmin.svg" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="minister_text" style="padding-left: 0.5rem;">
                    <div style="font-size: 13px; color: #000; font-weight: 700; margin: 0 0 2px 0; line-height: 1; padding-bottom: 2px;font-family: sans-serif ">Sri Duddilla Sridhar Babu</div>
                    <div style="font-size: 13px; color: #333; margin: 0; font-weight: 400; line-height: 1.6; width: 195px">Hon'ble Minister ITE&C,
                        <p>
                            I&C and LA
                        </p>
                    </div>
                </div>
            </div> -->

        <!-- </div> -->

        <!-- <div style="display: flex; align-items: center; justify-content: center; gap: 12px; min-width: 300px; margin-top: 2rem; padding-top: 1rem;" class="tgdex_wrapper"> -->
            <!-- <img src="../assets/default/images/logo.png" style="height: 36px;"> -->
            <!-- <a class="pull-left" id="logo" href="<?php echo ROOT_PATH; ?>" title="<?php echo __('Support Center'); ?>">
                            <span class="valign-helper"></span>
                            <img style="height: 4rem !important;"src="<?php echo ROOT_PATH ?>assets/default/images/mahaagx/mahaagx-logo.svg" class="tg_govt_header" >
                        </a> -->
            <!-- <div class="logo_title">
                            <p style="font-weight:700" class="logo_title_name">Forest Stack</p>
                            <p style="font-weight:500" class="logo_title_department">Rajasthan</p>
                        </div> -->
            <!-- <div style="font-size: 48px; color: #1a2e05;font-family: 'Caladea, serif !important';font-weight: 700; margin-left: 5px; padding-bottom: 0.6rem;" class="tgdex_text">Forest Stack | Rajasthan</div> -->
                    <div style="display: flex; align-items: center; justify-content: center; gap: 12px; min-width: 300px; " class="tgdex_wrapper">

            <div class="banner-content">
                <div class="banner-content-left-section"style="background-image:url(<?php echo ROOT_PATH; ?>assets/default/images/background.png)">
                    <!-- <div class="notification">
                    <span class="notification-content">
                        <span>Forest Stack open Innnovation Challenge is live!</span>

                    </span>
                </div> -->
                <!-- <img src="<?php echo ROOT_PATH; ?>assets/default/images/background.png" alt="background" style="position:absolute;width:auto;height:auto;z-index:-1;"> -->
                    <h1 style="margin-bottom:16px !important;font-weight: 700;color: #1a2e05;letter-spacing:0.32px;">Welcome to the Support Center</h1>
                    <p style="font-weight: 500; margin-bottom: 24px;" class="description">
                        In order to streamline support requests and better serve you, we utilize a support ticket system. Every support request is assigned a unique ticket number which you can use to track the progress and responses online. For your reference we provide complete archives and history of all your support requests. A valid email address is required to submit a ticket.
                    </p>
                    
                    <!-- Action Buttons -->
                    <div style="display: flex; gap: 16px; margin-top: 24px;">
                        <a href="<?php echo ROOT_PATH; ?>open.php" class="front-page-button front-page-button--primary" style="display: inline-block; padding: 12px 32px; background-color: #65a30d; color: white; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(101, 163, 13, 0.2);" 
                           onmouseover="this.style.backgroundColor='#4d7a0a'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(101, 163, 13, 0.3)';" 
                           onmouseout="this.style.backgroundColor='#65a30d'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(101, 163, 13, 0.2)';">
                            Open a New Ticket
                        </a>
                        <a href="<?php echo ROOT_PATH; ?>tickets.php" class="front-page-button front-page-button--secondary" style="display: inline-block; padding: 12px 32px; background-color: white; color: #65a30d; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 16px; border: 2px solid #65a30d; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" 
                           onmouseover="this.style.backgroundColor='#f0fdf4'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(0, 0, 0, 0.15)';" 
                           onmouseout="this.style.backgroundColor='white'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0, 0, 0, 0.1)';">
                            Check Ticket Status
                        </a>
                    </div>

                </div>
                <div class="banner-content-right-section">
                    <video autoplay muted loop playsinline preload="auto" poster="<?php echo ROOT_PATH ?>assets/default/images/mahaagx/login.png" aria-hidden="true">
                        <source src="<?php echo ROOT_PATH ?>assets/default/images/mahaagx/login.mp4" type="video/mp4">
                    </video>
                </div>

            </div>
        </div>

    </div>
</div>

<?php
include CLIENTINC_DIR . 'footer.inc.php';
?>