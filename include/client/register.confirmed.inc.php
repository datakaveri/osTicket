<?php if ($content) {
    list($title, $body) = $ost->replaceTemplateVariables(
        array($content->getName(), $content->getBody())); ?>
<style>
  .maha-account-registration-title {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    width: 100%;
    margin-bottom: 1.25rem;
    font-weight: 600;
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
    height: 22px;
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
    width: 20px;
    height: 20px;
    color: #1a2e05;
    flex-shrink: 0;
  }
</style>

<div class="maha-account-registration-title">
  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    <circle cx="8.5" cy="7" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M20 8v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M23 11h-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>
  <?php echo Format::display($title); ?>
</div>
<p><?php
echo Format::display($body); ?>
</p>
<?php } else { ?>
<div class="maha-account-registration-title">
  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    <circle cx="8.5" cy="7" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M20 8v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M23 11h-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>
  <?php echo __('Account Registration'); ?>
</div>
<p>
<strong><?php echo __('Thanks for registering for an account.'); ?></strong>
</p>
<p><?php echo __(
"You've confirmed your email address and successfully activated your account.  You may proceed to check on previously opened tickets or open a new ticket."
); ?>
</p>
<p><em><?php echo __('Your friendly support center'); ?></em></p>
<?php } ?>
