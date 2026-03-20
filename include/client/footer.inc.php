<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forest Stack Footer</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
        }

        .main-content {
            min-height: 50vh;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
        }
    </style>
</head>

<body>
</div>
    <!-- MahaAgX Footer -->
     <div style="
  position: relative;left: 50%;right: 50%;margin-left: -50vw;margin-right: -50vw;width: 100vw;background: #ffffff;">
  <!-- Flower band on white, like MahaAgX -->
  <img style="width: 100vw;margin-left: calc(-50vw + 50%);display:block;" src="<?php echo ROOT_PATH ?>assets/default/images/mahaagx/footer-flowers.svg" alt="MahaAgX footer pattern" />
    <footer style="background: linear-gradient(135deg, #0a3d32 0%, #0f4a3d 50%, #0a3d32 100%); padding: 3.5rem 0 2.5rem; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
        <!-- Row 1: 4 Columns (matches MahaAgX footer layout) -->
        <div style="width: 100%; max-width: 100%; margin: 0; padding: 0 77px; display: grid; grid-template-columns: 1.7fr 1fr 1fr 1fr; column-gap: 3rem; row-gap: 1.75rem; align-items: flex-start; margin-bottom: 1.75rem;">

            <!-- Column 1: About MahaAgX -->
            <div>
                <h3 style="color: #ffffff; font-size: 1.125rem; font-weight: 700; margin: 0 0 1rem 0; line-height: 1.3;">Maharashtra Agriculture Exchange</h3>
                <h3 style="color: #ffffff; font-size: 1.125rem; font-weight: 700; margin: 0 0 1rem 0; line-height: 1.3;">(MahaAgX)</h3>
                <p style="color: rgba(255,255,255,0.9); font-size: 0.9375rem; line-height: 1.6; margin: 0 0 1rem 0;">
                    MahaAgX is a platform that helps revolutionise agriculture through a secure data exchange network for researchers, innovators, and policymakers.
                </p>
                <div style="display:flex; align-items:center; gap:0.5rem; font-size:0.9375rem;">
                    <img src="<?php echo ROOT_PATH ?>assets/default/images/mahaagx/global.svg" alt="Website" style="width:20px;height:20px;flex-shrink:0;">
                    <a href="https://mahaagx.maharashtra.gov.in/" target="_blank" rel="noopener noreferrer" style="color:rgba(255,255,255,0.9); text-decoration:none;">
                        https://mahaagx.maharashtra.gov.in/
                    </a>
                </div>
            </div>

            <!-- Column 2: Important links -->
            <div>
                <h3 style="color: #ffffff; font-size: 1rem; font-weight: 600; margin: 0 0 1rem 0;">Important links</h3>
                <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.9375rem; line-height: 1.6;">
                    <li style="margin-bottom: 0.75rem;"><a href="https://mahaagx.maharashtra.gov.in/datasets" style="color:rgba(255,255,255,0.9); text-decoration:none;">Datasets catalogue</a></li>
                    <li style="margin-bottom: 0.75rem;"><a href="https://mahaagx.maharashtra.gov.in/dashboard" style="color:rgba(255,255,255,0.9); text-decoration:none;">Dashboard</a></li>
                    <li style="margin-bottom: 0.75rem;"><a href="https://mahaagx.maharashtra.gov.in/discussions" style="color:rgba(255,255,255,0.9); text-decoration:none;">Discussion</a></li>
                    <li style="margin-bottom: 0.75rem;"><a href="https://mahaagx.maharashtra.gov.in/challenges" style="color:rgba(255,255,255,0.9); text-decoration:none;">Challenge</a></li>
                    <li><a href="https://data-readiness-prod.s3.cyfuture.cloud/user-manual/User%20Manual%20-%20MahaAgX%20v1.0.pdf" style="color:rgba(255,255,255,0.9); text-decoration:none;">User Manual</a></li>
                </ul>
            </div>

            <!-- Column 3: For Developers -->
            <div>
                <h3 style="color: #ffffff; font-size: 1rem; font-weight: 600; margin: 0 0 1rem 0;">For Developers</h3>
                <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.9375rem; line-height: 1.6;">
                    <li style="margin-bottom: 0.75rem;"><a href="https://mahaagx.maharashtra.gov.in/controlplane/apis" style="color:rgba(255,255,255,0.9); text-decoration:none;">Control Plane API docs</a></li>
                    <li style="margin-bottom: 0.75rem;"><a href="https://mahaagx.maharashtra.gov.in/files-connect-api/v1/docs" style="color:rgba(255,255,255,0.9); text-decoration:none;">File Server API docs</a></li>
                    <li><a href="https://mahaagx.maharashtra.gov.in/dataplane/apis" style="color:rgba(255,255,255,0.9); text-decoration:none;">Resource Server API docs</a></li>
                </ul>
            </div>

            <!-- Column 4: Partners -->
            <div>
                <h3 style="color: #ffffff; font-size: 1rem; font-weight: 600; margin: 0 0 1rem 0;">Partners</h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                        <img src="<?php echo ROOT_PATH; ?>assets/default/images/iisc-logo-white.svg" alt="IISc" style="height: 40px; max-width: 100px; object-fit: contain;">
                        <img src="<?php echo ROOT_PATH; ?>assets/default/images/jica-logo.svg" alt="JICA" style="height: 40px; max-width: 100px; object-fit: contain;">
                        <img src="<?php echo ROOT_PATH; ?>assets/default/images/doit-logo.svg" alt="DoIT" style="height: 40px; max-width: 100px; object-fit: contain;">
                    </div>
                    <div style="display: flex; gap: 0.75rem; align-items: center;">
                        <img src="<?php echo ROOT_PATH; ?>assets/default/images/emblem-logo.svg" alt="Government of Maharashtra" style="height: 48px;">
                        <div>
                            <p style="color: rgba(255,255,255,0.9); font-size: 0.8125rem; font-weight: 600; margin: 0; line-height: 1.4;">Government of Maharashtra</p>
                            <p style="color: rgba(255,255,255,0.9); font-size: 0.75rem; font-weight: 400; margin: 0; line-height: 1.4;">Agriculture Department</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Footer Bottom -->
        <div style="width: 100%; max-width: 100%; margin: 0; padding: 1.25rem 77px 0; border-top: 1px solid rgba(255,255,255,0.2); display: flex; justify-content: space-between; align-items: center;">
            <p style="color: rgba(255,255,255,0.9); font-size: 0.8125rem; margin: 0; line-height: 1.5;">
                © <?php echo date('Y'); ?> by MahaAgX
                <span style="margin: 0 0.5rem;">|</span>
                <a href="https://mahaagx.maharashtra.gov.in/privacy-policy" style="color: rgba(255,255,255,0.9); text-decoration: none;">Privacy Policy</a>
                <span style="margin: 0 0.5rem;">|</span>
                <a href="https://mahaagx.maharashtra.gov.in/terms-of-service" style="color: rgba(255,255,255,0.9); text-decoration: none;">Terms of Service</a>
            </p>
        </div>

        <!-- Responsive Design for Mobile -->
        <style>
            @media (max-width: 768px) {
                footer>div {
                    grid-template-columns: 1fr !important;
                    gap: 2rem !important;
                    text-align: center;
                }

                footer>div>div:first-child {
                    text-align: left;
                }

                .bottom-powered-section {
                    flex-direction: column !important;
                    gap: 1.5rem !important;
                    text-align: center !important;
                    padding: 1.5rem 1rem !important;
                    align-items: center !important;
                }

                .powered-by-container {
                    flex-direction: column !important;
                    align-items: center !important;
                    gap: 0.5rem !important;
                }

                .powered-by-container span {
                    font-size: 12px !important;
                    margin-right: 0 !important;
                }

                .powered-by-container img {
                    height: 60px !important;
                    padding: 0.5rem 0 !important;
                }

                .bottom-links {
                    justify-content: center !important;
                    flex-wrap: wrap !important;
                    gap: 0.5rem !important;
                }

                .bottom-links a {
                    font-size: 12px !important;
                }

                .copyright-section {
                    padding: 1rem !important;
                    text-align: center !important;
                }

                .copyright-section span {
                    font-size: 12px !important;
                }
            }
        </style>
        </div>

        <!-- Bottom Section -->
        <!-- <div style="border-top: 1px solid #e5e7eb;">
            // Light section with Powered by and links
            <div class="bottom-powered-section" style="display: flex; justify-content: space-between; align-items: center; background:rgb(255, 255, 255); padding-left: 4rem; padding-right: 4rem;">
                <div class="powered-by-container" style="display: flex; align-items: center;">
                    <span style="color: #6b7280; font-size: 14px; margin-right: 1rem;">Powered by :</span>
                    <!-- <a href="https://your-site.com" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none;"> 
                    <img src="assets/default/images/et-logo.png" alt="Your Logo" style="height:75px; vertical-align: middle; padding: 1rem 0rem 1rem 0rem;">
                    </a>
                </div>
                <div class="bottom-links" style="display: flex; align-items: center; gap: 1rem;">
                    <a href="https://forest-stack.digivan.forest.rajasthan.gov.in/privacy-policy" style="color: #374151; text-decoration: none; font-size: 14px; font-weight: 400;">Privacy Policy</a>
                    <span style="color: #9ca3af;">|</span>
                    <a href="https://forest-stack.digivan.forest.rajasthan.gov.in/terms-of-service" style="color: #374151; text-decoration: none; font-size: 14px; font-weight: 400;">Terms of Service</a>
                </div>
            </div>

            <!-- Dark copyright section 
            <div class="copyright-section" style="background:rgb(33, 45, 63); color: #ffffff; padding: 0.5rem 0; padding-left: 2rem; padding-right: 2rem; text-align: left;">
                <span style="font-size: 13px;">© Government of Telangana</span>
            </div>
        </div> -->

    </footer>
    </div>
</body>

</html>
<!-- Custom Footer End -->


</div>

<div id="overlay"></div>
<div id="loading">
    <h4><?php echo __('Please Wait!'); ?></h4>
    <p><?php echo __('Please wait... it will take a second!'); ?></p>
</div>
<?php
if (($lang = Internationalization::getCurrentLanguage()) && $lang != 'en_US') { ?>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>ajax.php/i18n/<?php
                                                                                echo $lang; ?>/js"></script>
<?php } ?>
<script type="text/javascript">
    getConfig().resolve(<?php
                        include INCLUDE_DIR . 'ajax.config.php';
                        $api = new ConfigAjaxAPI();
                        print $api->client(false);
                        ?>);
</script>
</body>

</html>