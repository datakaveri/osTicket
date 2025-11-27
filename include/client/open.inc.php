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
    padding: 2rem 4rem;
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.form-parent-container {
    background: #ffffff;
    border-radius: 16px;
    padding: 2.5rem;
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
    font-size: 18px;
    font-weight: 600;
    color: #1a2e05;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-height: 28px;
    height: 28px;
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
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    font-size: 14px;
    color: #1a2e05;
    margin-bottom: 0.5rem;
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
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.2s;
    box-sizing: border-box;
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
    border-color: #65a30d;
    box-shadow: 0 0 0 3px rgba(101, 163, 13, 0.1);
}

.form-input::placeholder {
    color: #9ca3af;
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

.btn-submit {
    background: #65a30d;
    color: #fff;
    padding: 12px 32px;
    border: none;
    border-radius: 30px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-submit:hover {
    background: #4d7c0a;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.btn-cancel {
    background: #fff;
    color: #65a30d;
    padding: 12px 32px;
    border: 2px solid #65a30d;
    border-radius: 30px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-cancel:hover {
    background: #f0fdf4;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
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

<div class="new-ticket-container">
    <div class="new-ticket-header">
        <h1><?php echo __('Submit a Support Request'); ?></h1>
        <p><?php echo __('Fill in the form below and our support team will get back to you as soon as possible. We typically respond within 24 hours.'); ?></p>
    </div>

    <?php if ($errors && $errors['err']): ?>
    <div style="background: #fef2f2; border-left: 4px solid #dc2626; padding: 1rem 1.5rem; border-radius: 10px; margin-bottom: 2rem;">
        <p style="color: #dc2626; margin: 0; font-weight: 500;"><?php echo $errors['err']; ?></p>
    </div>
    <?php endif; ?>


    <form id="ticketForm" method="post" action="open.php" enctype="multipart/form-data">
        <?php csrf_token(); ?>
        <input type="hidden" name="a" value="open">

        <div class="form-parent-container">
            <div class="form-columns">
                <!-- LEFT COLUMN: Contact Information -->
                <div class="form-card">
                    <div class="form-section-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="#1a2e05"/>
                        </svg>
                        <?php echo __('Contact Information'); ?>
                    </div>

                    <div class="form-field">
                        <label class="form-label" for="name">
                            <?php echo __('Full Name'); ?> <span class="required">*</span>
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
                            <?php echo __('Email Address'); ?> <span class="required">*</span>
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
                            <?php echo __('Help Topic'); ?> <span class="required">*</span>
                        </label>
                        <select id="topicId" 
                                name="topicId" 
                                class="form-select"
                                onchange="javascript:
                                    if (this.value) {
                                        $('#request-details-card').fadeIn(300);
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
                                        $('#request-details-card').fadeOut(300);
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

                <!-- RIGHT COLUMN: Ticket Details -->
                <div class="form-card" id="request-details-card" style="display: <?php echo ($info['topicId'] ? 'block' : 'none'); ?>;">
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