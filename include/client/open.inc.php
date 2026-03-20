<?php
if (!defined('OSTCLIENTINC')) die('Access Denied!');
$info = array();
if ($thisclient && $thisclient->isValid()) {
    $info = array(
        'name' => $thisclient->getName(),
        'email' => $thisclient->getEmail(),
        'phone' => $thisclient->getPhoneNumber()
    );
}

$info = ($_POST && $errors) ? Format::htmlchars($_POST) : $info;

$form = null;
if (!$info['topicId']) {
    if (array_key_exists('topicId', $_GET) && preg_match('/^\d+$/', $_GET['topicId']) && Topic::lookup($_GET['topicId']))
        $info['topicId'] = intval($_GET['topicId']);
    else
        $info['topicId'] = $cfg->getDefaultTopicId();
}

$forms = array();
if ($info['topicId'] && ($topic = Topic::lookup($info['topicId']))) {
    foreach ($topic->getForms() as $F) {
        if (!$F->hasAnyVisibleFields())
            continue;
        if ($_POST) {
            $F = $F->instanciate();
            $F->isValidForClient();
        }
        $forms[] = $F->getForm();
    }
}
?>

<style>
.new-ticket-container {
    width: 100%;
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.form-parent-container {
    background: #ffffff;
    border-radius: 16px;
    /* Reduce horizontal space around Contact Information */
    padding: 2rem 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin-bottom: 2rem;
}

.form-columns {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    align-items: stretch;
}

.form-card {
    position: relative;
    transition: opacity 0.3s ease-in-out;
}

.form-card--contact {
    background: #ffffff;
    border-radius: 16px;
    padding: 1.25rem;
}

.contact-fields {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.form-card:first-child::after {
    content: '';
    position: absolute;
    right: -1.5rem;
    top: 0;
    bottom: 0;
    width: 1px;
    background: linear-gradient(to bottom, transparent 0%, #e5e7eb 10%, #e5e7eb 90%, transparent 100%);
}

.new-ticket-header {
    text-align: center;
    margin-bottom: 3rem;
}

.new-ticket-header h1 {
    font-size: 36px;
    font-weight: 700;
    color: #1a2e05;
    margin-bottom: 1rem;
    letter-spacing: -0.5px;
}

.new-ticket-header p {
    font-size: 16px;
    color: #666;
    line-height: 1.6;
    max-width: 600px;
    margin: 0 auto;
}

#request-details-card {
    animation: fadeInDown 0.3s ease-in-out;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-section-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-height: 28px;
    height: 28px;
}

.form-section-title--lined {
    position: relative;
    padding-left: 14px; /* room for left accent bar */
}

.form-section-title--lined::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 24px;
    border-radius: 999px;
    background: #00d084;
}

.form-section-title--lined::after {
    content: '';
    flex: 1;
    height: 2px;
    margin-left: 12px;
    border-radius: 999px;
    background: rgba(0, 208, 132, 0.55);
}

.form-row-title {
    margin-bottom: 1.25rem;
}

.form-section-title svg {
    width: 20px;
    height: 20px;
}

/* Hide form instructions and duplicate title */
#dynamic-form .form-header {
    display: none !important;
}

#dynamic-form .form-header > div {
    display: none !important;
}

#dynamic-form .form-header > h3 {
    display: none !important;
}

/* Align first field in right column with first field in left column */
#request-details-card .form-section-title {
    min-height: 28px;
    height: 28px;
    margin-bottom: 1.5rem;
}

/* Ensure first field in dynamic form aligns with Full Name */
#dynamic-form tr:first-child {
    display: none;
}

/* Remove top padding from first visible field row to align with Full Name */
#dynamic-form tr:nth-child(2) td {
    padding-top: 0 !important;
    padding-bottom: 0 !important;
}

/* Ensure Issue Summary field aligns with Full Name field */
#dynamic-form tr:nth-child(2) {
    margin-top: 0 !important;
}

/* Ensure all inputs in dynamic form are full width */
#dynamic-form input[type="text"],
#dynamic-form input[type="email"],
#dynamic-form select,
#dynamic-form textarea {
    width: 100% !important;
}

/* Add spacing between labels and inputs in dynamic form */
#dynamic-form td {
    padding-top: 10px !important;
}

#dynamic-form td label {
    display: block !important;
    margin-bottom: 0.5rem !important;
}

/* Ensure spacing after label before input */
#dynamic-form td label + input,
#dynamic-form td label + select,
#dynamic-form td label + textarea,
#dynamic-form td input,
#dynamic-form td select,
#dynamic-form td textarea {
    margin-top: 0.5rem !important;
}

.form-field {
    margin-bottom: 0;
}

.form-label {
    display: block;
    font-weight: 600;
    font-size: 0.875rem;
    color: #111827;
    margin-bottom: 0.4rem;
}

.form-label .required {
    color: #dc2626;
    margin-left: 2px;
}

.form-input,
.form-select,
.form-textarea,
input[type="text"],
input[type="email"],
input[type="tel"],
select,
textarea {
    width: 100% !important;
    padding: 0.75rem 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    font-size: 1rem;
    font-family: inherit;
    transition: all 0.2s;
    box-sizing: border-box;
    background: #ffffff;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus,
input[type="text"]:focus,
input[type="email"]:focus,
input[type="tel"]:focus,
select:focus,
textarea:focus {
    outline: none;
    border-color: #00d084;
    box-shadow: 0 0 0 4px rgba(0, 208, 132, 0.14);
}

.form-input::placeholder {
    color: #9ca3af;
}

/* Grey placeholder for required <select> (e.g., "Select a help topic") */
.new-ticket-container #ticketForm select:required:invalid {
    color: #9ca3af !important;
}

.new-ticket-container #ticketForm select option {
    color: #111827;
}

.new-ticket-container #ticketForm select option[value=""] {
    color: #9ca3af;
}

/* MahaAGX-like field styling for Open Ticket (override global #ticketForm rules) */
.new-ticket-container #ticketForm .form-input,
.new-ticket-container #ticketForm .form-select,
.new-ticket-container #ticketForm textarea.form-textarea,
.new-ticket-container #ticketForm input[type="text"],
.new-ticket-container #ticketForm input[type="email"],
.new-ticket-container #ticketForm input[type="tel"],
.new-ticket-container #ticketForm select,
.new-ticket-container #ticketForm textarea {
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
    color: #111827 !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 10px !important;
    padding: 14px 16px !important;
    font-size: 1rem !important;
    background: #ffffff !important;
}

.new-ticket-container #ticketForm .form-input:focus,
.new-ticket-container #ticketForm .form-select:focus,
.new-ticket-container #ticketForm input[type="text"]:focus,
.new-ticket-container #ticketForm input[type="email"]:focus,
.new-ticket-container #ticketForm input[type="tel"]:focus,
.new-ticket-container #ticketForm select:focus,
.new-ticket-container #ticketForm textarea:focus {
    border-color: #00d084 !important;
    box-shadow: 0 0 0 4px rgba(0, 208, 132, 0.14) !important;
}

.new-ticket-container #ticketForm .form-label {
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
    font-size: 0.95rem;
}

.ticket-details-empty {
    padding: 0.5rem;
    height: 100%;
}

.ticket-details-empty__img {
    width: 100%;
    height: 20rem;
    object-fit: cover;
    border-radius: 14px;
    border: 1px solid rgba(15, 23, 42, 0.06);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    display: block;
}

.ticket-details-empty__hint {
    margin-top: 0.9rem;
    color: #4b5563;
    font-size: 0.95rem;
    line-height: 1.5;
    text-align: center;
}

/* Match right card styling with left card */
#request-details-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 1.25rem;
}

.form-select {
    cursor: pointer;
    background-color: #fff;
}

.error-message {
    color: #dc2626;
    font-size: 13px;
    margin-top: 0.5rem;
    display: block;
}

.form-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 0;
}

.new-ticket-container .btn-submit {
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: #00d084 !important;
    color: #0a0a0a !important;
    padding: 0.75rem 1.5rem;
    border: none !important;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: 150ms ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.new-ticket-container .btn-submit:hover {
    background: #00b371 !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
}

.new-ticket-container .btn-cancel {
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: transparent !important;
    color: #00d084 !important;
    padding: 0.75rem 1.5rem;
    border: 1px solid rgba(0, 208, 132, 0.55) !important;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: 150ms ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: none;
}

.new-ticket-container .btn-cancel:hover {
    background: rgba(0, 208, 132, 0.08) !important;
    border-color: #00b371 !important;
    color: #00b371 !important;
    transform: translateY(-1px);
}

.new-ticket-container .btn-submit:focus-visible,
.new-ticket-container .btn-cancel:focus-visible {
    outline: none;
    box-shadow: 0 0 0 4px rgba(0, 208, 132, 0.14);
}

.info-banner {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border-left: 4px solid #65a30d;
    padding: 1.25rem 1.5rem;
    border-radius: 10px;
    margin-bottom: 2rem;
    display: flex;
    align-items: start;
    gap: 1rem;
}

.info-banner svg {
    flex-shrink: 0;
    margin-top: 2px;
}

.info-banner-content h3 {
    font-size: 15px;
    font-weight: 600;
    color: #1a2e05;
    margin: 0 0 0.5rem 0;
}

.info-banner-content p {
    font-size: 14px;
    color: #4d7c0a;
    margin: 0;
    line-height: 1.6;
}

.captcha-section {
    background: #f9fafb;
    padding: 1.5rem;
    border-radius: 10px;
    margin-top: 2rem;
}

.captcha-section label {
    display: block;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #1a2e05;
}

.captcha-content {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.captcha-content img {
    border-radius: 8px;
    border: 2px solid #e5e7eb;
}

@media (max-width: 1024px) {
    .form-columns {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .form-card:first-child::after {
        display: none;
    }
}

@media (max-width: 768px) {
    .new-ticket-container {
        padding: 1.5rem;
    }
    
    .form-parent-container {
        padding: 1.5rem;
    }
    
    .new-ticket-header h1 {
        font-size: 28px;
    }
}
</style>

<div class="maha-asset-header">
    <div class="maha-asset-header__container">
        <div class="maha-asset-header__leaf">
            <img src="<?php echo ROOT_PATH; ?>assets/default/images/mahaagx/assets-leaf.png" alt="" class="maha-asset-header__leaf-img" />
        </div>

        <div class="maha-asset-header__content">
            <nav class="maha-asset-header__breadcrumbs" aria-label="<?php echo __('Breadcrumb'); ?>">
                <ol class="maha-breadcrumbs">
                    <li class="maha-breadcrumbs__item">
                        <a class="maha-breadcrumbs__link" href="<?php echo ROOT_PATH; ?>index.php"><?php echo __('Home'); ?></a>
                    </li>
                    <li class="maha-breadcrumbs__item maha-breadcrumbs__current" aria-current="page">
                        <?php echo __('New Ticket'); ?>
                    </li>
                </ol>
            </nav>

            <div class="maha-asset-header__title-wrap">
                <h3 class="maha-asset-header__title"><?php echo __('Submit a Support Request'); ?></h3>
            </div>

            <p class="maha-asset-header__desc">
                <?php echo __('Fill in the form below and our support team will get back to you as soon as possible. We typically respond within 24 hours.'); ?>
            </p>
        </div>
    </div>
</div>

<div class="new-ticket-container">
    <?php if ($errors && $errors['err']): ?>
    <div style="background: #fef2f2; border-left: 4px solid #dc2626; padding: 1rem 1.5rem; border-radius: 10px; margin-bottom: 2rem;">
        <p style="color: #dc2626; margin: 0; font-weight: 500;"><?php echo $errors['err']; ?></p>
    </div>
    <?php endif; ?>


    <form id="ticketForm" method="post" action="open.php" enctype="multipart/form-data">
        <?php csrf_token(); ?>
        <input type="hidden" name="a" value="open">

        <div class="form-parent-container">
            <div class="form-section-title form-section-title--lined form-row-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="#00d084"/>
                </svg>
                <?php echo __('Contact Information'); ?>
            </div>

            <div class="form-columns">
                <!-- LEFT COLUMN: Contact Information -->
                <div class="form-card form-card--contact">
                    <div class="contact-fields">
                        <div class="form-field">
                            <label class="form-label" for="name">
                                <?php echo __('Full Name'); ?> <span class="required" aria-hidden="true"></span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   class="form-input" 
                                   value="<?php echo $info['name']; ?>" 
                                   placeholder="<?php echo __('Enter your full name'); ?>"
                                   required>
                            <?php if ($errors['name']): ?>
                                <span class="error-message"><?php echo $errors['name']; ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-field">
                            <label class="form-label" for="email">
                                <?php echo __('Email Address'); ?> <span class="required" aria-hidden="true"></span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   class="form-input" 
                                   value="<?php echo $info['email']; ?>" 
                                   placeholder="<?php echo __('your.email@example.com'); ?>"
                                   required>
                            <?php if ($errors['email']): ?>
                                <span class="error-message"><?php echo $errors['email']; ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-field">
                            <label class="form-label" for="topicId">
                                <?php echo __('Help Topic'); ?> <span class="required" aria-hidden="true"></span>
                            </label>
                            <select id="topicId" 
                                    name="topicId" 
                                    class="form-select"
                                    onchange="javascript:
                                        if (this.value) {
                                            $('#request-details-card').fadeIn(300);
                                            $('#request-details-card .ticket-details-empty').hide();
                                            $('#request-details-card .ticket-details-form').fadeIn(200);
                                            var data = $(':input[name]', '#dynamic-form').serialize();
                                            $.ajax(
                                              'ajax.php/form/help-topic/' + this.value,
                                              {
                                                data: data,
                                                dataType: 'json',
                                                success: function(json) {
                                                  $('#dynamic-form').empty().append(json.html);
                                                  $(document.head).append(json.media);
                                                }
                                              });
                                        } else {
                                            $('#request-details-card .ticket-details-form').hide();
                                            $('#request-details-card .ticket-details-empty').fadeIn(200);
                                        }"
                                    required>
                                <option value=""><?php echo __('Select a help topic'); ?></option>
                                <?php
                                if ($topics = Topic::getPublicHelpTopics()) {
                                    foreach ($topics as $id => $name) {
                                        echo sprintf(
                                            '<option value="%d" %s>%s</option>',
                                            $id,
                                            ($info['topicId'] == $id) ? 'selected="selected"' : '',
                                            $name
                                        );
                                    }
                                } ?>
                            </select>
                            <?php if ($errors['topicId']): ?>
                                <span class="error-message"><?php echo $errors['topicId']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Ticket Details -->
                <div class="form-card" id="request-details-card">
                    <div class="ticket-details-empty" style="display: <?php echo ($info['topicId'] ? 'none' : 'block'); ?>;">
                        <img class="ticket-details-empty__img" src="<?php echo ROOT_PATH; ?>assets/default/images/mahaagx/contact.jpg" alt="<?php echo __('Contact'); ?>" />
                    </div>

                    <div class="ticket-details-form" style="display: <?php echo ($info['topicId'] ? 'block' : 'none'); ?>;">
                        <div class="form-section-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z" fill="#1a2e05"/>
                            </svg>
                            <?php echo __('Ticket Details'); ?>
                        </div>

                        <div id="dynamic-form">
                            <?php
                            $options = array('mode' => 'create');
                            foreach ($forms as $form) {
                                include(CLIENTINC_DIR . 'templates/dynamic-form.tmpl.php');
                            } ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            if ($cfg && $cfg->isCaptchaEnabled() && (!$thisclient || !$thisclient->isValid())) {
                if ($_POST && $errors && !$errors['captcha'])
                    $errors['captcha'] = __('Please re-enter the text again');
            ?>
                <div class="captcha-section">
                    <label><?php echo __('Security Verification'); ?></label>
                    <div class="captcha-content">
                        <span class="captcha"><img src="captcha.php" border="0"></span>
                        <input id="captcha" 
                               type="text" 
                               name="captcha" 
                               class="form-input" 
                               placeholder="<?php echo __('Enter the text shown'); ?>"
                               style="max-width: 200px;"
                               autocomplete="off">
                    </div>
                    <?php if ($errors['captcha']): ?>
                        <span class="error-message"><?php echo $errors['captcha']; ?></span>
                    <?php endif; ?>
                </div>
            <?php } ?>

            <div class="form-actions">
                <a href="<?php echo ROOT_PATH; ?>index.php" class="btn-cancel"><?php echo __('Cancel'); ?></a>
                <button type="submit" class="btn-submit">
                    <?php echo __('Submit Request'); ?>
                </button>
            </div>
        </div>
    </form>
</div>