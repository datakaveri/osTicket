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

$mahaCanSeeTickets = $thisclient && is_object($thisclient) && $thisclient->isValid();
?>

<style>
.mahadex-hero {
    position: relative;
    width: 100vw;
    left: 50%;
    margin-left: -50vw;
    min-height: 420px;
    overflow: hidden;
    background: #020410;
    color: #fff;
    font-family: 'IBM Plex Sans', sans-serif;
}
.mahadex-hero__media { position: absolute; inset: 0; }
.mahadex-hero__media img { width: 100%; height: 100%; object-fit: cover; display: block; }
.mahadex-hero__fade {
    position: absolute; inset: 0;
    background:
      linear-gradient(70deg, #030610 12.8%, rgba(29, 32, 41, 0) 37.3%),
      linear-gradient(90deg, #020410 0%, rgba(2, 4, 16, 0.55) 22%, transparent 48%);
}
.mahadex-hero__content {
    position: relative; z-index: 2; max-width: 640px;
    padding: 56px 48px; display: flex; flex-direction: column; gap: 16px;
}
.mahadex-hero__title { margin: 0; font-size: 48px; font-weight: 600; line-height: 1.15; color: #fff; }
.mahadex-hero__title span { display: block; color: #eb834c; }
.mahadex-hero__desc { margin: 0; max-width: 520px; font-size: 16px; font-weight: 500; line-height: 1.5; color: #fff; }
.mahadex-hero__actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 8px; }
.mahadex-btn-primary, .mahadex-btn-secondary {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 12px 28px; border-radius: 8px; font-size: 14px; font-weight: 600;
    text-decoration: none; cursor: pointer; font-family: inherit; border: 1px solid transparent;
}
.mahadex-btn-primary { background: #3730a3; color: #fff; border-color: #3730a3; }
.mahadex-btn-primary:hover { background: #4f46e5; border-color: #4f46e5; color: #fff; }
.mahadex-btn-secondary { background: transparent; color: #fff; border-color: rgba(255,255,255,0.7); }
.mahadex-btn-secondary:hover { background: rgba(255,255,255,0.1); color: #fff; }
@media (max-width: 768px) {
  .mahadex-hero__content { padding: 40px 20px; }
  .mahadex-hero__title { font-size: 32px; }
}
</style>

<section class="mahadex-hero" aria-label="MahaDeX Support Center">
    <div class="mahadex-hero__media" aria-hidden="true">
        <img src="<?php echo ROOT_PATH; ?>assets/default/images/mahadex/mahadex-banner-hero.png" alt="">
        <div class="mahadex-hero__fade"></div>
    </div>
    <div class="mahadex-hero__content">
        <h1 class="mahadex-hero__title">
            Connecting Data.
            <span>Empowering Maharashtra.</span>
        </h1>
        <p class="mahadex-hero__desc">
            Welcome to the MahaDeX Support Center. Open a ticket for help with datasets,
            models, access, and platform issues. Every request gets a unique ticket number
            you can track online.
        </p>
        <div class="mahadex-hero__actions">
            <a class="mahadex-btn-primary" href="<?php echo ROOT_PATH; ?>open.php"><?php echo __('Open a New Ticket'); ?></a>
            <?php if ($mahaCanSeeTickets) { ?>
            <a class="mahadex-btn-secondary" href="<?php echo ROOT_PATH; ?>tickets.php"><?php echo __('Check Ticket Status'); ?></a>
            <?php } else { ?>
            <button type="button" class="mahadex-btn-secondary maha-ticket-status-trigger"><?php echo __('Check Ticket Status'); ?></button>
            <?php } ?>
        </div>
    </div>
</section>

<?php if (!$mahaCanSeeTickets) { ?>
<div id="mahaTicketStatusModal" class="maha-login-modal" aria-hidden="true" role="dialog" aria-labelledby="mahaTicketStatusModalTitle" aria-modal="true">
    <div class="maha-login-modal__backdrop" data-maha-modal-close tabindex="-1"></div>
    <div class="maha-login-modal__dialog" role="document">
        <button type="button" class="maha-login-modal__close" data-maha-modal-close aria-label="<?php echo __('Close'); ?>">&times;</button>
        <div class="login-modal-content">
            <div class="login-modal--leaf-image" aria-hidden="true">
                <img src="<?php echo ROOT_PATH; ?>assets/default/images/mahaagx/assets-leaf.png" alt="" class="login-modal--leaf" width="200" height="200" />
            </div>
            <div class="login-modal--icon-wrapper">
                <svg class="login-modal--icon" width="64" height="64" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h3 id="mahaTicketStatusModalTitle" class="login-modal--title"><?php echo __('Please Login to Continue'); ?></h3>
            <p class="login-modal--message"><?php echo __('You need to be logged in to access this feature. Please login with your account to proceed.'); ?></p>
            <div class="login-modal--actions">
                <a href="<?php echo Format::htmlchars(MAHAAGX_KEYCLOAK_AUTH_URL); ?>" class="login-modal--btn login-modal--btn-login">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <polyline points="10 17 15 12 10 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="15" y1="12" x2="3" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span><?php echo __('Login'); ?></span>
                </a>
                <button type="button" class="login-modal--btn login-modal--btn-cancel" data-maha-modal-close><?php echo __('Cancel'); ?></button>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    var modal = document.getElementById('mahaTicketStatusModal');
    if (!modal) return;
    var openModal = function () {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('maha-modal-open');
    };
    var closeModal = function () {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('maha-modal-open');
    };
    document.querySelectorAll('.maha-ticket-status-trigger').forEach(function (el) {
        el.addEventListener('click', openModal);
    });
    modal.querySelectorAll('[data-maha-modal-close]').forEach(function (el) {
        el.addEventListener('click', closeModal);
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });
})();
</script>
<?php } ?>

<?php
include CLIENTINC_DIR . 'footer.inc.php';
?>
