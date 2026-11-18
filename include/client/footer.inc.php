</div><!-- content -->

<footer class="mahadex-footer" style="
  position: relative;
  left: 50%;
  right: 50%;
  margin-left: -50vw;
  margin-right: -50vw;
  width: 100vw;
  background: #1e1b4b;
  color: #ffffff;
  font-family: 'IBM Plex Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  padding: 2rem clamp(1rem, 4vw, 3rem) 0;
  box-sizing: border-box;
">
    <div style="max-width: 1400px; margin: 0 auto; display: grid; grid-template-columns: 1.6fr 1fr 1fr auto; gap: 2.5rem; align-items: start;">
        <div>
            <h2 style="margin: 0 0 0.75rem; font-size: 24px; font-weight: 700; line-height: 1.3; color: #ffffff;">
                Maharashtra Data Exchange (MahaDeX)
            </h2>
            <p style="margin: 0 0 1rem; font-size: 14px; line-height: 1.7; color: #eef2ff;">
                MahaDeX is Maharashtra's unified digital platform for secure data sharing
                and seamless collaboration enabling data-driven governance and smarter
                public services.
            </p>
            <a href="https://v2.dev.iudx.io/" target="_blank" rel="noopener noreferrer"
               style="display: inline-flex; align-items: center; gap: 0.5rem; color: rgba(255,255,255,0.85); text-decoration: none; font-size: 14px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/>
                    <path d="M3 12h18M12 3c2.4 2.7 3.6 5.7 3.6 9s-1.2 6.3-3.6 9c-2.4-2.7-3.6-5.7-3.6-9s1.2-6.3 3.6-9Z" stroke="currentColor" stroke-width="1.6"/>
                </svg>
                <span>v2.dev.iudx.io</span>
            </a>
        </div>

        <div>
            <h3 style="margin: 0 0 0.75rem; font-size: 12px; font-weight: 600; letter-spacing: 0.04em; text-transform: uppercase; color: #eef2ff;">
                Important links
            </h3>
            <ul style="list-style: none; margin: 0; padding: 0;">
                <li style="margin-bottom: 0.5rem;"><a href="https://v2.dev.iudx.io/" style="color: rgba(255,255,255,0.9); text-decoration: none; font-size: 14px;">MahaDeX Home</a></li>
                <li style="margin-bottom: 0.5rem;"><a href="https://v2.dev.iudx.io/datasets" style="color: rgba(255,255,255,0.9); text-decoration: none; font-size: 14px;">Datasets</a></li>
                <li style="margin-bottom: 0.5rem;"><a href="https://v2.dev.iudx.io/models" style="color: rgba(255,255,255,0.9); text-decoration: none; font-size: 14px;">Models</a></li>
                <li style="margin-bottom: 0.5rem;"><a href="https://v2.dev.iudx.io/usecases" style="color: rgba(255,255,255,0.9); text-decoration: none; font-size: 14px;">Use Cases</a></li>
                <li style="margin-bottom: 0.5rem;"><a href="https://v2.dev.iudx.io/user-manual" style="color: rgba(255,255,255,0.9); text-decoration: none; font-size: 14px;">User Manual</a></li>
                <li style="margin-bottom: 0.5rem;"><a href="<?php echo ROOT_PATH; ?>index.php" style="color: rgba(255,255,255,0.9); text-decoration: none; font-size: 14px;">Help and Support</a></li>
            </ul>
        </div>

        <div>
            <h3 style="margin: 0 0 0.75rem; font-size: 12px; font-weight: 600; letter-spacing: 0.04em; text-transform: uppercase; color: #eef2ff;">
                For developers
            </h3>
            <ul style="list-style: none; margin: 0; padding: 0;">
                <li style="margin-bottom: 0.5rem;"><a href="https://v2.dev.iudx.io/" style="color: rgba(255,255,255,0.9); text-decoration: none; font-size: 14px;">Control Plane API docs</a></li>
                <li style="margin-bottom: 0.5rem;"><a href="https://v2.dev.iudx.io/" style="color: rgba(255,255,255,0.9); text-decoration: none; font-size: 14px;">File Server API docs</a></li>
                <li style="margin-bottom: 0.5rem;"><a href="https://v2.dev.iudx.io/" style="color: rgba(255,255,255,0.9); text-decoration: none; font-size: 14px;">Resource Server API docs</a></li>
            </ul>
        </div>

        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 1rem;">
            <img src="<?php echo ROOT_PATH; ?>assets/default/images/mahadex-logo-mark.svg" alt="MahaDeX" style="height: 48px; width: auto;">
            <img src="<?php echo ROOT_PATH; ?>assets/default/images/mahadex-dept-it.svg" alt="Department of IT" style="height: 40px; width: auto; max-width: 180px;">
        </div>
    </div>

    <div style="max-width: 1400px; margin: 1.5rem auto 0; padding: 1rem 0; border-top: 1px solid rgba(255,255,255,0.12); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
        <p style="margin: 0; font-size: 14px; color: rgba(255,255,255,0.85);">© <?php echo date('Y'); ?> by MahaDeX</p>
        <p style="margin: 0; font-size: 14px; color: rgba(255,255,255,0.85);">
            <a href="https://v2.dev.iudx.io/privacy-policy" style="color: inherit; text-decoration: none;">Privacy Policy</a>
            <span aria-hidden="true"> | </span>
            <a href="https://v2.dev.iudx.io/terms-of-service" style="color: inherit; text-decoration: none;">Terms of Use</a>
        </p>
    </div>

    <style>
        @media (max-width: 900px) {
            .mahadex-footer > div:first-child {
                grid-template-columns: 1fr !important;
                gap: 1.5rem !important;
            }
            .mahadex-footer > div:first-child > div:last-child {
                align-items: flex-start !important;
            }
        }
    </style>
</footer>

</div><!-- container -->

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
