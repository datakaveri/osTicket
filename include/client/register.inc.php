<?php
$info = $_POST;
if (!isset($info['timezone']))
    $info += array(
        'backend' => null,
    );
if (isset($user) && $user instanceof ClientCreateRequest) {
    $bk = $user->getBackend();
    $info = array_merge($info, array(
        'backend' => $bk->getBkId(),
        'username' => $user->getUsername(),
    ));
}
$info = Format::htmlchars(($errors && $_POST)?$_POST:$info);

?>
<style>
  /* Full width of #content — same horizontal inset as header (77px) / footer (77px) */
  .maha-register {
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    max-width: none;
    width: 100%;
    margin: 0;
    box-sizing: border-box;
    --maha-register-phone-col-gap: 0.5rem;
  }

  .maha-register__card {
    background: #ffffff;
    border: 1px solid rgba(15, 23, 42, 0.06);
    border-radius: 16px;
    padding: 2rem 2.25rem 2.25rem;
    box-shadow: none;
  }

  .maha-account-registration-title {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    width: 100%;
    margin-bottom: 0.75rem;
    font-weight: 700;
    font-size: 1.5rem;
    color: #1a2e05;
    position: relative;
    padding-left: 14px;
  }

  .maha-account-registration-title::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 26px;
    border-radius: 999px;
    background: #00d084;
  }

  .maha-account-registration-title::after {
    content: '';
    flex: 1;
    height: 2px;
    margin-left: 12px;
    border-radius: 999px;
    background: rgba(0, 208, 132, 0.55);
  }

  .maha-account-registration-title svg {
    width: 22px;
    height: 22px;
    color: #1a2e05;
    flex-shrink: 0;
  }

  .maha-register__intro {
    margin: 0 0 1.75rem 0;
    color: #64748b;
    font-size: 0.9375rem;
    line-height: 1.6;
  }

  .maha-register__table {
    width: 100% !important;
    max-width: 100%;
    border-collapse: separate;
    border-spacing: 0;
  }

  .maha-register__table > tbody > tr > td {
    padding: 0.5rem 0;
    vertical-align: top;
    font-size: 0.9375rem;
    color: #111827;
  }

  /* Exclude grid cells so contact fields can be 50% / 50% */
  .maha-register__table > tbody > tr > td:first-child:not([colspan]):not(.maha-register__grid-cell) {
    width: 200px;
    max-width: 40%;
    padding-right: 1rem;
    font-weight: 600;
    color: #0f172a;
    white-space: nowrap;
  }

  .maha-register__table > tbody > tr.maha-register__grid-row > td.maha-register__grid-cell {
    width: 50%;
    max-width: 50%;
    box-sizing: border-box;
    vertical-align: top;
    font-weight: 400;
    color: #111827;
    white-space: normal;
  }

  .maha-register__table > tbody > tr.maha-register__grid-row > td.maha-register__grid-cell:first-child {
    padding-right: 0.75rem;
  }

  .maha-register__table > tbody > tr.maha-register__grid-row > td.maha-register__grid-cell:last-child:not(:first-child) {
    padding-left: 0.75rem;
  }

  .maha-register__grid-cell--full {
    width: 100%;
    max-width: 100%;
  }

  /* Nested dynamic form (grid) */
  .maha-register table.grid.form {
    width: 100% !important;
    border-collapse: separate;
    border-spacing: 0;
    margin: 0;
  }

  .maha-register table.grid.form caption {
    display: none;
  }

  .maha-register table.grid.form > tbody > tr:first-child {
    display: none;
  }

  .maha-register table.grid.form td.cell {
    padding: 0 0 1.25rem 0 !important;
    vertical-align: top;
    border: none;
  }

  .maha-register table.grid.form fieldset.field {
    border: none;
    margin: 0;
    padding: 0;
    min-width: 0;
  }

  .maha-register table.grid.form label {
    display: block;
    font-weight: 600;
    font-size: 0.9375rem;
    color: #0f172a;
    margin-bottom: 0.5rem;
  }

  .maha-register table.grid.form label .error {
    color: #dc2626;
    font-weight: 700;
  }

  .maha-register table.grid.form .field-hint-text {
    font-size: 0.8125rem;
    color: #64748b;
    margin-bottom: 0.35rem;
  }

  /* Match “Create a password”: light blue fill, subtle radius, minimal border.
     Include table.maha-register__table — client UserForm uses dynamic-form rows here,
     not table.grid.form, so without this theme.css input { border-radius: 999px } wins. */
  .maha-register table.grid.form input[type="text"],
  .maha-register table.grid.form input[type="email"],
  .maha-register table.grid.form input[type="tel"],
  .maha-register table.grid.form input[type="password"],
  .maha-register table.grid.form input[type="number"],
  .maha-register table.grid.form textarea,
  .maha-register table.grid.form select,
  .maha-register table.maha-register__table input[type="text"],
  .maha-register table.maha-register__table input[type="email"],
  .maha-register table.maha-register__table input[type="tel"],
  .maha-register table.maha-register__table input[type="password"],
  .maha-register table.maha-register__table input[type="number"],
  .maha-register table.maha-register__table textarea,
  .maha-register table.maha-register__table select,
  .maha-register__table input[type="password"] {
    width: 100% !important;
    max-width: 100%;
    box-sizing: border-box !important;
    padding: 0.9rem 1.1rem !important;
    border-radius: 10px !important;
    border: 1px solid #dbeafe !important;
    background: #e8f0fe !important;
    font-size: 1rem !important;
    font-family: inherit !important;
    color: #0f172a !important;
    transition: border-color 150ms ease, box-shadow 150ms ease, background 150ms ease;
  }

  .maha-register table.grid.form textarea {
    min-height: 6rem;
    resize: vertical;
  }

  .maha-register table.grid.form input:focus,
  .maha-register table.grid.form textarea:focus,
  .maha-register table.grid.form select:focus,
  .maha-register table.maha-register__table input:focus,
  .maha-register table.maha-register__table textarea:focus,
  .maha-register table.maha-register__table select:focus,
  .maha-register__table input[type="password"]:focus {
    outline: none !important;
    border-color: #93c5fd !important;
    box-shadow: 0 0 0 3px rgba(0, 208, 132, 0.12) !important;
    background: #eff6ff !important;
  }

  /*
   * PhoneNumberWidget (see class.forms.php).
   * Default: tel + optional ext label + ext input.
   * maha_register_phone_grid: 2×2 grid — same label/input rhythm as Email | Full Name.
   */
  .maha-register label:has(.ost-inline-phone-row) {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 0.5rem;
  }

  /* Hide legacy <br> after field title; keep <br> before hints on maha phone grid */
  .maha-register label:has(.ost-inline-phone-row) > br {
    display: none;
  }

  .maha-register label:has(.ost-inline-phone-row--maha-grid) > br {
    display: block;
  }

  .maha-register label:has(.ost-inline-phone-row) > span:not(.ost-inline-phone-row),
  .maha-register label:has(.ost-inline-phone-row) > em {
    width: 100%;
  }

  .maha-register .ost-inline-phone-row--maha-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto auto;
    column-gap: 1.5rem;
    row-gap: var(--maha-register-phone-col-gap);
    width: 100%;
    align-items: start;
  }

  .maha-register .ost-inline-phone-row--maha-grid .ost-inline-phone-grid-label {
    grid-column: 1;
    grid-row: 1;
  }

  .maha-register .ost-inline-phone-row--maha-grid .ost-inline-phone-grid-label--ext {
    grid-column: 2;
    grid-row: 1;
  }

  .maha-register .ost-inline-phone-row--maha-grid .ost-inline-phone-main {
    grid-column: 1;
    grid-row: 2;
    min-width: 0;
  }

  .maha-register .ost-inline-phone-row--maha-grid .ost-inline-phone-ext {
    grid-column: 2;
    grid-row: 2;
    display: block;
    min-width: 0;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
  }

  .maha-register .ost-inline-phone-grid-label,
  .maha-register .ost-inline-phone-grid-label--ext {
    display: block;
    font-weight: 600;
    font-size: 0.9375rem;
    color: #0f172a;
    line-height: 1.3;
  }

  .maha-register .ost-inline-phone-row--maha-grid .ost-inline-phone-main input[type="tel"],
  .maha-register .ost-inline-phone-row--maha-grid .ost-inline-phone-ext input[type="text"] {
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
  }

  /* Non-maha phone widget: extension block — label above, full-width input */
  .maha-register .ost-inline-phone-ext {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: var(--maha-register-phone-col-gap);
    min-width: 0;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
  }

  .maha-register .ost-inline-phone-ext-label {
    display: block;
    font-weight: 600;
    font-size: 0.9375rem;
    color: #0f172a;
    line-height: 1.3;
  }

  .maha-register .ost-inline-phone-ext input[type="text"] {
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    box-sizing: border-box !important;
  }

  /* Half cell (paired fields): flex phone + ext within that column */
  .maha-register table.maha-register__table td.maha-register__grid-cell:not(.maha-register__grid-cell--full) .ost-inline-phone-row,
  .maha-register table.grid.form td.cell .ost-inline-phone-row {
    display: flex;
    flex-wrap: nowrap;
    align-items: flex-start;
    gap: 0.5rem;
    width: 100%;
    min-width: 0;
  }

  .maha-register table.maha-register__table td.maha-register__grid-cell:not(.maha-register__grid-cell--full) .ost-inline-phone-main,
  .maha-register table.grid.form td.cell .ost-inline-phone-main {
    flex: 1 1 auto;
    min-width: 0;
  }

  .maha-register table.maha-register__table td.maha-register__grid-cell:not(.maha-register__grid-cell--full) .ost-inline-phone-main input[type="tel"],
  .maha-register table.grid.form td.cell .ost-inline-phone-main input[type="tel"] {
    width: 100% !important;
  }

  .maha-register table.maha-register__table td.maha-register__grid-cell:not(.maha-register__grid-cell--full) .ost-inline-phone-ext,
  .maha-register table.grid.form td.cell .ost-inline-phone-ext {
    flex: 1 1 auto;
    min-width: 0;
  }

  .maha-register table.maha-register__table td.maha-register__grid-cell--full .ost-inline-phone-row:not(.ost-inline-phone-row--maha-grid) {
    display: block;
    width: 100%;
  }

  .maha-register table.maha-register__table td.maha-register__grid-cell--full .ost-inline-phone-row:not(.ost-inline-phone-row--maha-grid) .ost-inline-phone-main input[type="tel"] {
    width: 100% !important;
    box-sizing: border-box !important;
  }

  .maha-register__section-row td {
    padding-top: 1.5rem !important;
    padding-bottom: 0.75rem !important;
  }

  .maha-register__section-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: #1a2e05;
    margin: 0;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e2e8f0;
  }

  /* Full-width stacked field (matches dynamic-form label-above-input layout) */
  .maha-register__cell-full {
    padding-top: 0.75rem !important;
    padding-bottom: 0.25rem !important;
  }

  .maha-register__field-label {
    display: block;
    font-weight: 600;
    font-size: 0.9375rem;
    color: #0f172a;
    margin-bottom: 0.5rem;
  }

  .maha-register__timezone-wrap {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 0.75rem;
    max-width: 100%;
  }

  .maha-register__timezone-wrap .action-button {
    margin-left: 0 !important;
    align-self: flex-start;
  }

  .maha-register .select2-container {
    width: 100% !important;
    max-width: 100% !important;
  }

  .maha-register .select2-container .select2-choice,
  .maha-register .select2-container--default .select2-selection--single {
    min-height: 48px !important;
    padding: 0.5rem 0.75rem !important;
    border-radius: 10px !important;
    border: 1px solid #dbeafe !important;
    background: #e8f0fe !important;
  }

  .maha-register .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #93c5fd !important;
    box-shadow: 0 0 0 3px rgba(0, 208, 132, 0.12) !important;
    background: #eff6ff !important;
  }

  .maha-register .action-button {
    display: inline-flex !important;
    align-items: center;
    gap: 0.35rem;
    margin-left: 0.75rem;
    vertical-align: middle !important;
    background: #00d084 !important;
    color: #0a0a0a !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 0.65rem 1.1rem !important;
    font-size: 0.875rem !important;
    font-weight: 600 !important;
    font-family: inherit !important;
    cursor: pointer;
    transition: background 150ms ease;
  }

  .maha-register .action-button:hover {
    background: #00b371 !important;
  }

  .maha-register .error {
    color: #dc2626;
    font-size: 0.875rem;
    margin-top: 0.35rem;
  }

  .maha-register__actions {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
  }

  .maha-register__btn-primary {
    font-family: inherit;
    padding: 0.75rem 2rem;
    border: none;
    border-radius: 8px;
    background: #00d084;
    color: #0a0a0a;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: background 150ms ease, transform 150ms ease;
  }

  .maha-register__btn-primary:hover {
    background: #00b371;
    transform: translateY(-1px);
  }

  .maha-register__btn-secondary {
    font-family: inherit;
    padding: 0.75rem 2rem;
    border: 1px solid rgba(0, 208, 132, 0.55);
    border-radius: 8px;
    background: transparent;
    color: #00d084;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 150ms ease, color 150ms ease;
  }

  .maha-register__btn-secondary:hover {
    background: rgba(0, 208, 132, 0.08);
    color: #00b371;
  }

  @media (max-width: 640px) {
    .maha-register__card {
      padding: 1.5rem 1.25rem;
    }
    .maha-register__table tr.maha-register__grid-row,
    .maha-register__table tr.maha-register__grid-row > td.maha-register__grid-cell {
      display: block;
      width: 100% !important;
      max-width: 100% !important;
    }
    .maha-register__table tr.maha-register__grid-row > td.maha-register__grid-cell:first-child,
    .maha-register__table tr.maha-register__grid-row > td.maha-register__grid-cell:last-child {
      padding-left: 0 !important;
      padding-right: 0 !important;
    }
    .maha-register__table td.maha-register__grid-cell--full .ost-inline-phone-row--maha-grid {
      grid-template-columns: 1fr !important;
    }
    .maha-register__table > tbody > tr > td:first-child:not([colspan]):not(.maha-register__grid-cell) {
      display: block;
      width: 100%;
      max-width: none;
      padding-right: 0;
      padding-bottom: 0.25rem;
    }
    .maha-register .action-button {
      margin-left: 0;
      margin-top: 0.5rem;
    }
  }
</style>

<div class="maha-register">
  <div class="maha-register__card">
    <div class="maha-account-registration-title">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <circle cx="8.5" cy="7" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M20 8v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M23 11h-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <?php echo __('Account Registration'); ?>
    </div>
    <p class="maha-register__intro"><?php echo __(
        'Use the forms below to create or update the information we have on file for your account'
    ); ?></p>

    <form action="account.php" method="post">
      <?php csrf_token(); ?>
      <input type="hidden" name="do" value="<?php echo Format::htmlchars($_REQUEST['do']
          ?: ($info['backend'] ? 'import' :'create')); ?>" />
      <table class="padded maha-register__table">
        <tbody>
        <?php
            $cf = $user_form ?: UserForm::getInstance();
            $cf->render(array(
                'staff' => false,
                'mode' => 'create',
                'template' => 'dynamic-form-register.tmpl.php',
            ));
        ?>
        <tr class="maha-register__section-row">
          <td colspan="2">
            <h3 class="maha-register__section-title"><?php echo __('Preferences'); ?></h3>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="maha-register__cell-full">
            <label class="maha-register__field-label" for="timezone-dropdown"><?php echo __('Time Zone'); ?>:</label>
            <div class="maha-register__timezone-wrap">
            <?php
            $TZ_NAME = 'timezone';
            $TZ_TIMEZONE = $info['timezone'];
            include INCLUDE_DIR.'staff/templates/timezone.tmpl.php'; ?>
            </div>
            <div class="error"><?php echo $errors['timezone']; ?></div>
          </td>
        </tr>
        <tr class="maha-register__section-row">
          <td colspan="2">
            <h3 class="maha-register__section-title"><?php echo __('Access Credentials'); ?></h3>
          </td>
        </tr>
        <?php if ($info['backend']) { ?>
        <tr>
          <td colspan="2" class="maha-register__cell-full">
            <span class="maha-register__field-label"><?php echo __('Login With'); ?>:</span>
            <div>
            <input type="hidden" name="backend" value="<?php echo $info['backend']; ?>"/>
            <input type="hidden" name="username" value="<?php echo $info['username']; ?>"/>
<?php foreach (UserAuthenticationBackend::allRegistered() as $bk) {
    if ($bk->getBkId() == $info['backend']) {
        echo $bk->getName();
        break;
    }
} ?>
            </div>
          </td>
        </tr>
        <?php } else { ?>
        <tr>
          <td colspan="2" class="maha-register__cell-full">
            <label class="maha-register__field-label" for="passwd1_reg"><?php echo __('Create a Password'); ?>:</label>
            <input id="passwd1_reg" type="password" name="passwd1" maxlength="128" value="<?php echo $info['passwd1']; ?>">
            <span class="error"><?php echo $errors['passwd1']; ?></span>
          </td>
        </tr>

        <tr>
          <td colspan="2" class="maha-register__cell-full">
            <label class="maha-register__field-label" for="passwd2_reg"><?php echo __('Confirm New Password'); ?>:</label>
            <input id="passwd2_reg" type="password" name="passwd2" maxlength="128" value="<?php echo $info['passwd2']; ?>">
            <span class="error"><?php echo $errors['passwd2']; ?></span>
          </td>
        </tr>
        <?php } ?>
        </tbody>
      </table>

      <div class="maha-register__actions">
        <button type="submit" class="maha-register__btn-primary"><?php echo __('Register'); ?></button>
        <button type="button" class="maha-register__btn-secondary" onclick="window.location.href='index.php';"><?php echo __('Cancel'); ?></button>
      </div>
    </form>
  </div>
</div>
<?php if (!isset($info['timezone'])) { ?>
<!-- Auto detect client's timezone where possible -->
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jstz.min.js"></script>
<script type="text/javascript">
$(function() {
    var zone = jstz.determine();
    $('#timezone-dropdown').val(zone.name()).trigger('change');
});
</script>
<?php }
