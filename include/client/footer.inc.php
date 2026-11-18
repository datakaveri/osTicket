</div><!-- content -->

<footer class="maha-footer">
    <div class="maha-footer__inner">
        <div class="maha-footer__content">
            <div class="maha-footer__about">
                <h2 class="maha-footer__title">Maharashtra Data Exchange (MahaDeX)</h2>
                <p class="maha-footer__description">
                    MahaDeX is Maharashtra’s unified digital platform for secure data sharing
                    and seamless collaboration enabling data-driven governance and
                    smarter public services.
                </p>
                <a class="maha-footer__site" href="https://mahadex.iudx.io/" target="_blank" rel="noopener noreferrer">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"></circle>
                        <path d="M3 12h18M12 3c2.4 2.7 3.6 5.7 3.6 9s-1.2 6.3-3.6 9c-2.4-2.7-3.6-5.7-3.6-9s1.2-6.3 3.6-9Z" stroke="currentColor" stroke-width="1.6"></path>
                    </svg>
                    <span>mahadex.iudx.io</span>
                </a>
            </div>

            <div class="maha-footer__column">
                <h3 class="maha-footer__heading">Important links</h3>
                <ul class="maha-footer__links">
                    <li><a href="https://mahadex.iudx.io/">MahaDeX Home</a></li>
                    <li><a href="https://mahadex.iudx.io/datasets">Datasets</a></li>
                    <li><a href="https://mahadex.iudx.io/models">Models</a></li>
                    <li><a href="https://mahadex.iudx.io/usecases">Use Cases</a></li>
                    <li><a href="https://mahadex.iudx.io/user-manual">User Manual</a></li>
                    <li><a href="<?php echo ROOT_PATH; ?>index.php">Help and Support</a></li>
                </ul>
            </div>

            <div class="maha-footer__column">
                <h3 class="maha-footer__heading">For developers</h3>
                <ul class="maha-footer__links">
                    <li><a href="https://mahadex.iudx.io/controlplane/apis" target="_blank" rel="noopener noreferrer">Control Plane API docs</a></li>
                    <li><a href="https://mahadex.iudx.io/files-connect-api/apis" target="_blank" rel="noopener noreferrer">File Server API docs</a></li>
                    <li><a href="https://mahadex.iudx.io/dataplane/apis" target="_blank" rel="noopener noreferrer">Resource Server API docs</a></li>
                </ul>
            </div>

            <div class="maha-footer__marks">
                <span class="maha-footer__mark">
                    <img src="<?php echo ROOT_PATH; ?>assets/default/images/mahadex/mahadex-logo-mark.svg" alt="MahaDeX">
                </span>
                <img class="maha-footer__dept" src="<?php echo ROOT_PATH; ?>assets/default/images/mahadex/mahadex-dept-it.svg" alt="Department of IT">
            </div>
        </div>

        <div class="maha-footer__bottom">
            <p class="maha-footer__copyright">© <?php echo date('Y'); ?> by MahaDeX</p>
            <p class="maha-footer__legal">
                <a href="https://mahadex.iudx.io/privacy-policy" target="_blank" rel="noopener noreferrer">Privacy Policy</a>
                <span aria-hidden="true">|</span>
                <a href="https://mahadex.iudx.io/terms-of-service" target="_blank" rel="noopener noreferrer">Terms of Use</a>
            </p>
        </div>
    </div>
</footer>

<style>
.maha-footer {
  position: relative;
  left: 50%;
  right: 50%;
  margin-left: -50vw;
  margin-right: -50vw;
  width: 100vw;
  box-sizing: border-box;
  background: #1e1b4b;
  color: #ffffff;
  font-family: 'IBM Plex Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

.maha-footer__inner {
  width: 100%;
  max-width: none;
  margin: 0 auto;
  padding: 64px 48px 0;
  box-sizing: border-box;
}

.maha-footer__content {
  display: grid;
  grid-template-columns: minmax(280px, 479px) minmax(120px, 1fr) minmax(150px, 1fr) auto;
  gap: 58px;
  align-items: start;
}

.maha-footer__about {
  display: flex;
  flex-direction: column;
  gap: 12px;
  max-width: 479px;
}

.maha-footer__title {
  margin: 0;
  font-size: 24px;
  font-weight: 700;
  line-height: 32px;
  color: #ffffff;
}

.maha-footer__description {
  margin: 0;
  max-width: 479px;
  font-size: 14px;
  line-height: 1.7;
  color: #eef2ff;
}

.maha-footer__site {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin-top: 4px;
  color: rgba(255, 255, 255, 0.78);
  text-decoration: none;
  font-size: 16px;
  line-height: 1.4;
}

.maha-footer__site:hover {
  color: #ffffff;
}

.maha-footer__heading {
  margin: 0 0 12px;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  color: #eef2ff;
}

.maha-footer__links {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.maha-footer__links a {
  color: rgba(255, 255, 255, 0.88);
  text-decoration: none;
  font-size: 14px;
  line-height: 1.5;
}

.maha-footer__links a:hover {
  color: #ffffff;
}

.maha-footer__marks {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  align-self: end;
  gap: 24px;
  margin: 0;
}

.maha-footer__mark {
  display: grid;
  place-items: center;
  width: 48px;
  height: 48px;
  overflow: hidden;
  border-radius: 9999px;
  background: transparent;
}

.maha-footer__mark img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.maha-footer__dept {
  display: block;
  height: 64px;
  width: auto;
  max-width: 180px;
}

.maha-footer__bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-top: 40px;
  padding: 16px 0 24px;
  border-top: 1px solid rgba(255, 255, 255, 0.14);
}

.maha-footer__copyright,
.maha-footer__legal {
  margin: 0;
  font-size: 14px;
  color: rgba(255, 255, 255, 0.62);
}

.maha-footer__legal {
  display: inline-flex;
  align-items: center;
  gap: 32px;
}

.maha-footer__legal a {
  color: inherit;
  text-decoration: none;
}

.maha-footer__legal a:hover {
  color: #ffffff;
}

@media (max-width: 991px) {
  .maha-footer__inner {
    padding: 48px 24px 0;
  }
}

@media (max-width: 900px) {
  .maha-footer__content {
    grid-template-columns: 1fr 1fr;
    gap: 36px;
  }

  .maha-footer__about {
    grid-column: 1 / -1;
    max-width: none;
  }

  .maha-footer__marks {
    grid-column: 1 / -1;
    flex-direction: row;
    align-items: center;
    justify-content: flex-start;
  }
}

@media (max-width: 640px) {
  .maha-footer__inner {
    padding: 36px 16px 0;
  }

  .maha-footer__content {
    grid-template-columns: 1fr;
    gap: 28px;
  }

  .maha-footer__bottom {
    flex-direction: column;
    align-items: flex-start;
    margin-top: 28px;
    padding-bottom: 20px;
  }

  .maha-footer__legal {
    gap: 16px;
  }
}
</style>

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
  (function () {
    var links = document.querySelectorAll('.maha-footer a');
    for (var i = 0; i < links.length; i++) {
      var a = links[i];
      var href = a.getAttribute('href') || '';
      if (href.indexOf('http') === 0) {
        a.setAttribute('target', '_blank');
        a.setAttribute('rel', 'noopener noreferrer');
      }
    }
  })();
</script>
<script type="text/javascript">
    getConfig().resolve(<?php
                        include INCLUDE_DIR . 'ajax.config.php';
                        $api = new ConfigAjaxAPI();
                        print $api->client(false);
                        ?>);
</script>
</body>
</html>
