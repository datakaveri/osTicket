<?php
if (!defined('OSTCLIENTINC')) die('Access Denied');

require_once INCLUDE_DIR . 'client/mahaagx-keycloak-url.inc.php';

$email = Format::input($_POST['luser'] ?: $_GET['e']);
$passwd = Format::input($_POST['lpasswd'] ?: $_GET['t']);

$content = Page::lookupByType('banner-client');

if ($content) {
    list($title, $body) = $ost->replaceTemplateVariables(
        array($content->getLocalName(), $content->getLocalBody())
    );
} else {
    $title = __('Sign In');
}

?>
<style>
/* Elegant Login Page - MahaAgX */
.page-login #msg_error,
.page-login #msg_notice,
.page-login #msg_warning {
  display: none !important; /* Shown inside login card */
}

/* Override theme's #clientLogin background - form is in its own column */
.page-login #clientLogin {
  background: transparent !important;
}
.page-login #content {
  padding: 2.5rem 1.5rem 4rem;
  min-height: 60vh;
  height: auto !important;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  background: #ffffff;
}

.maha-login {
  font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  width: 100%;
  max-width: 920px;
  margin: 0 auto;
}

.maha-login__title {
  font-size: 2rem;
  font-weight: 800;
  letter-spacing: -0.03em;
  margin: 0 0 0.5rem 0;
  color: #0f172a;
  text-align: center;
}

.maha-login__title--in-card {
  text-align: left;
  margin: 0 0 1.4rem 0;
  font-size: 1.65rem;
}

.maha-login__title-accent {
  color: #059669;
}

.maha-login__subtitle {
  margin: 0 0 2rem 0;
  color: #64748b;
  font-size: 0.9375rem;
  line-height: 1.6;
  text-align: center;
}

.maha-login__subtitle--in-card {
  text-align: left;
  margin: 0 0 1.5rem 0;
  font-size: 0.875rem;
  line-height: 1.55;
}

.maha-login__card {
  background: #ffffff;
  border: 1px solid rgba(15, 23, 42, 0.06);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04);
  display: grid;
  grid-template-columns: 56% 44%;
  min-height: 420px;
}

.maha-login__card-inner {
  padding: 2.5rem 2rem;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  border-right: 1px solid #e2e8f0;
  min-width: 0;
  width: 100%;
  height: 100%;
}

.maha-login__form-top {
  flex: 1 1 auto;
  display: flex;
  flex-direction: column;
  margin-bottom: 1.5rem;
  justify-content: center;
}

.maha-login__form-bottom {
  margin-top: auto;
  padding-top: 0.75rem;
}

.maha-login__card-decor {
  min-height: 200px;
  overflow: hidden;
  background: #f0f4f0;
}

.maha-login__card-decor img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  display: block;
}

.maha-login__error {
  display: block;
  margin-bottom: 1rem;
  padding: 0.75rem 1rem;
  background: #fef2f2;
  border-radius: 10px;
  color: #b91c1c;
  font-weight: 600;
  font-size: 0.9rem;
}

.maha-login__label {
  display: block;
  font-weight: 600;
  color: #0f172a;
  margin-bottom: 0.5rem;
  font-size: 0.95rem;
}

.maha-login__label .required::after {
  content: " *";
  color: #dc2626;
  font-weight: 700;
}

.maha-login__input {
  width: 100%;
  box-sizing: border-box;
  padding: 0.95rem 1.25rem;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  font-size: 1rem;
  color: #0f172a;
  outline: none;
  transition: border-color 150ms ease, box-shadow 150ms ease;
}

.maha-login__input::placeholder {
  color: #94a3b8;
}

.maha-login__input:focus {
  border-color: #059669;
  box-shadow: 0 0 0 3px rgba(0, 208, 132, 0.12);
  background: #ffffff;
}

.maha-login__meta {
  display: flex;
  justify-content: flex-end;
  margin: 0 0 1.65rem;
}

.maha-login__link {
  color: #059669;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.9rem;
  transition: color 150ms ease;
}

.maha-login__link:hover {
  color: #00b371;
  text-decoration: underline;
}

.maha-login__btn {
  width: 100%;
  padding: 1rem 1.5rem;
  border: none;
  border-radius: 12px;
  background: #059669;
  color: #0a0a0a;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  transition: background 150ms ease, transform 150ms ease;
}

.maha-login__btn:hover {
  background: #00b371;
  transform: translateY(-1px);
}

.maha-login__btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px rgba(0, 208, 132, 0.3);
}

.maha-login__footer {
  margin-top: 1.05rem;
  text-align: center;
  color: #64748b;
  font-size: 0.8rem;
}

.maha-login__footer a {
  color: #059669;
  font-weight: 600;
  text-decoration: none;
}

.maha-login__footer a:hover {
  text-decoration: underline;
}

.maha-login__extras {
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid rgba(15, 23, 42, 0.08);
  text-align: center;
  color: #64748b;
  font-size: 0.9rem;
  line-height: 1.7;
}

.maha-login__extras a {
  color: #059669;
  font-weight: 600;
  text-decoration: none;
}

.maha-login__extras a:hover {
  text-decoration: underline;
}

.maha-login__extras .external-auth {
  margin-bottom: 0.75rem;
}

.maha-login__extras hr {
  border: 0;
  height: 1px;
  background: rgba(15, 23, 42, 0.08);
  margin: 1rem 0;
}

.maha-login__hint {
  margin-top: 1.5rem;
  text-align: center;
  color: #64748b;
  font-size: 0.875rem;
  line-height: 1.6;
}

.maha-login__hint a {
  color: #059669;
  font-weight: 600;
  text-decoration: none;
}

.maha-login__hint a:hover {
  text-decoration: underline;
}

@media (max-width: 640px) {
  .maha-login__card {
    grid-template-columns: 1fr;
    min-height: auto;
  }
  .maha-login__card-inner {
    padding: 1.75rem 1.5rem;
  }
  .maha-login__card-decor {
    min-height: 180px;
    order: -1;
  }
  .maha-login__title--in-card {
    font-size: 1.4rem;
  }
}
</style>

<div class="maha-login">
  <div class="maha-login__card">
    <form action="login.php" method="post" id="clientLogin" class="maha-login__card-inner">
      <?php csrf_token(); ?>

      <div class="maha-login__form-top">
        <h1 class="maha-login__title maha-login__title--in-card">
          <span class="maha-login__title-accent"><?php echo __('Login'); ?></span>
          <?php echo __('to MahaAgX'); ?>
        </h1>
        <!-- Subtitle removed as requested -->

        <?php if ($errors['err']) { ?>
          <strong class="maha-login__error"><?php echo Format::htmlchars($errors['err']); ?></strong>
        <?php } ?>

        <div class="maha-login__field">
          <label class="maha-login__label" for="username">
            <?php echo __('Email ID'); ?><span class="required" aria-hidden="true"></span>
          </label>
          <input
            id="username"
            class="maha-login__input nowarn"
            placeholder="<?php echo __('Enter your email'); ?>"
            type="text"
            name="luser"
            value="<?php echo $email; ?>"
            autocomplete="username"
            required>
        </div>

        <div class="maha-login__field">
          <label class="maha-login__label" for="passwd">
            <?php echo __('Password'); ?><span class="required" aria-hidden="true"></span>
          </label>
          <input
            id="passwd"
            class="maha-login__input nowarn"
            placeholder="<?php echo __('Enter your password'); ?>"
            type="password"
            name="lpasswd"
            maxlength="128"
            value="<?php echo $passwd; ?>"
            autocomplete="current-password"
            required>
        </div>

        <?php if ($suggest_pwreset) { ?>
          <div class="maha-login__meta">
            <a class="maha-login__link" href="pwreset.php"><?php echo __('Forgot Password?'); ?></a>
          </div>
        <?php } ?>
      </div>

      <div class="maha-login__form-bottom">
        <button class="maha-login__btn" type="submit"><?php echo __('Log In'); ?></button>

        <?php if ($cfg && $cfg->isClientRegistrationEnabled()) { ?>
          <div class="maha-login__footer">
            <?php echo __('New to MahaAgX?'); ?> <a href="<?php echo Format::htmlchars(MAHAAGX_KEYCLOAK_REGISTER_URL); ?>"><?php echo __('Register'); ?></a>
          </div>
        <?php } ?>
      </div>
    </form>

    <div class="maha-login__card-decor">
      <img src="<?php echo ROOT_PATH; ?>assets/default/images/mahaagx/login.png" alt="" aria-hidden="true" />
    </div>
  </div>

  <div class="maha-login__extras">
    <?php
    $ext_bks = array();
    foreach (UserAuthenticationBackend::allRegistered() as $bk)
      if ($bk instanceof ExternalAuthentication)
        $ext_bks[] = $bk;

    if (count($ext_bks)) {
      foreach ($ext_bks as $bk) { ?>
        <div class="external-auth"><?php $bk->renderExternalLink(); ?></div><?php
      }
    }

    if ($cfg && $cfg->isClientRegistrationEnabled() && count($ext_bks)) { ?>
      <hr />
    <?php } ?>

    <?php if ($cfg && $cfg->isClientRegistrationEnabled()) { ?>
      <div>
        <?php echo __('Not yet registered?'); ?> <a href="<?php echo Format::htmlchars(MAHAAGX_KEYCLOAK_REGISTER_URL); ?>"><?php echo __('Create an account'); ?></a>
      </div>
    <?php } ?>

    <div style="margin-top: 0.75rem;">
      <strong><?php echo __("I'm an agent"); ?></strong> — <a href="<?php echo ROOT_PATH; ?>scp/"><?php echo __('sign in here'); ?></a>
    </div>
  </div>

  <?php if ($cfg->getClientRegistrationMode() != 'disabled' || !$cfg->isClientLoginRequired()) { ?>
    <p class="maha-login__hint">
      <?php echo sprintf(
        __('If this is your first time contacting us or you\'ve lost the ticket number, please %s open a new ticket %s'),
        '<a href="open.php">',
        '</a>'
      ); ?>
    </p>
  <?php } ?>
</div>
