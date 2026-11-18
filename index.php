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
require(CLIENTINC_DIR . 'header.inc.php');
?>

<style>
.mahadex-hero {
    position: relative;
    width: 100vw;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    min-height: 420px;
    overflow: hidden;
    background: #020410;
    color: #ffffff;
    font-family: 'IBM Plex Sans', sans-serif;
}
.mahadex-hero__media {
    position: absolute;
    inset: 0;
}
.mahadex-hero__media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.mahadex-hero__fade {
    position: absolute;
    inset: 0;
    background:
        linear-gradient(70deg, #030610 12.8%, rgba(29, 32, 41, 0) 37.3%),
        linear-gradient(90deg, #020410 0%, rgba(2, 4, 16, 0.55) 22%, transparent 48%);
}
.mahadex-hero__content {
    position: relative;
    z-index: 2;
    max-width: 640px;
    padding: 56px 48px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.mahadex-hero__title {
    margin: 0;
    font-size: 48px;
    font-weight: 600;
    line-height: 1.15;
    color: #ffffff;
}
.mahadex-hero__title span {
    display: block;
    color: #eb834c;
}
.mahadex-hero__desc {
    margin: 0;
    max-width: 520px;
    font-size: 16px;
    font-weight: 500;
    line-height: 1.5;
    color: #ffffff;
}
.mahadex-hero__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 8px;
}
.mahadex-btn-primary,
.mahadex-btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 28px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
}
.mahadex-btn-primary {
    background: #3730a3;
    color: #ffffff;
    border: 1px solid #3730a3;
}
.mahadex-btn-primary:hover {
    background: #4f46e5;
    border-color: #4f46e5;
    color: #ffffff;
}
.mahadex-btn-secondary {
    background: transparent;
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.7);
}
.mahadex-btn-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
}
.mahadex-support {
    max-width: 960px;
    margin: 48px auto;
    padding: 0 24px 24px;
    font-family: 'IBM Plex Sans', sans-serif;
}
.mahadex-support h2 {
    margin: 0 0 12px;
    font-size: 24px;
    font-weight: 700;
    color: #0f172a;
}
.mahadex-support p {
    margin: 0 0 24px;
    font-size: 16px;
    line-height: 1.6;
    color: #64748b;
}
.mahadex-cta {
    margin: 0 24px 48px;
    padding: 32px;
    border-radius: 16px 16px 0 0;
    background: #eef2ff;
    border: 1px solid #4f46e5;
    text-align: center;
    font-family: 'IBM Plex Sans', sans-serif;
}
.mahadex-cta h2 {
    margin: 0 0 8px;
    font-size: 24px;
    font-weight: 700;
    color: #0f172a;
}
.mahadex-cta p {
    margin: 0 0 20px;
    color: #334155;
    font-size: 16px;
}
@media (max-width: 768px) {
    .mahadex-hero__content {
        padding: 40px 20px;
    }
    .mahadex-hero__title {
        font-size: 32px;
    }
    .mahadex-hero {
        min-height: 360px;
    }
}
</style>

<section class="mahadex-hero" aria-label="MahaDeX Support Center">
    <div class="mahadex-hero__media" aria-hidden="true">
        <img src="<?php echo ROOT_PATH; ?>assets/default/images/mahadex-banner-hero.png" alt="">
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
            <a class="mahadex-btn-primary" href="<?php echo ROOT_PATH; ?>open.php">Open a New Ticket</a>
            <a class="mahadex-btn-secondary" href="<?php echo ROOT_PATH; ?>tickets.php">Check Ticket Status</a>
        </div>
    </div>
</section>

<div class="mahadex-support">
    <h2>How support works</h2>
    <p>
        We use a ticket system so your requests stay organized. Submit with a valid email
        address, keep the ticket number for your records, and follow responses and history
        in one place.
    </p>
</div>

<div class="mahadex-cta">
    <h2>Ready to collaborate on Maharashtra’s data?</h2>
    <p>Join government, researchers, and startups building with MahaDeX.</p>
    <div class="mahadex-hero__actions" style="justify-content:center;">
        <a class="mahadex-btn-primary" href="https://v2.dev.iudx.io/datasets" target="_blank" rel="noopener">Explore Datasets</a>
        <a class="mahadex-btn-secondary" href="<?php echo ROOT_PATH; ?>open.php" style="color:#3730a3;border-color:#3730a3;background:#fff;">Get Support</a>
    </div>
</div>

<?php
include CLIENTINC_DIR . 'footer.inc.php';
?>
