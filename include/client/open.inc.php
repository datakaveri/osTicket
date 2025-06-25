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

<h1 id="gradient-green-title"style="padding-left: 4rem !important;"><?php echo __('Submit a Request'); ?></h1>

<form id="ticketForm" method="post" action="open.php" enctype="multipart/form-data" style="font-family: Open Sans, Helvetica, Arial, sans-serif; padding: 1rem; padding-left: 4rem;">
    <?php csrf_token(); ?>
    <input type="hidden" name="a" value="open">

    <div style="display: flex; flex-wrap: wrap; gap: 2rem;">
        <div style="flex: 1; min-width: 300px; display:flex; flex-direction:column; gap:1rem">
            <!-- Name -->
            <div style="margin-bottom: 1rem; width: 100%;">

                <label for="name" style="display: block; font-weight: 600; margin-bottom: 5px;">
                    <?php echo __('Name'); ?> <span style="color:red">*</span>
                </label>

                <input type="text" name="name" id="name" value="<?php echo $info['name']; ?>" class="green-focus-input" placeholder="Enter Full Name"
                    style="width: 100%; max-width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 999px; box-sizing: border-box;">
            </div>


            <!-- Email -->
            <div style="margin-bottom: 1rem; width: 100%;">
                <label for="email" style="display: block; font-weight: 600; margin-bottom: 5px;">
                    <?php echo __('Email ID'); ?> <span style="color:red">*</span>
                </label>
                <input type="email" name="email" id="email" value="<?php echo $info['email']; ?>" placeholder="Enter Email ID"
                    style="width: 100%; max-width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 999px; box-sizing: border-box;">
            </div>


            <!-- Topic Dropdown -->
            <div>
                <label for="topicId" style="font-weight: 600;"><?php echo __('Reason'); ?> <span style="color:red">*</span></label>
                <select id="topicId" name="topicId" onchange="javascript:
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
                      });"
                    style="width: 100%; padding: 10px; border-radius: 999px; border: 1px solid #ccc; background: white;">
                    <option value="" selected><?php echo __('Select a Help Topic'); ?></option>
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
                <font class="error"><?php echo $errors['topicId']; ?></font>
            </div>

            <!-- Subject -->
            <!-- <div>
                <label for="subject" style="font-weight: 600;"><?php echo __('Subject'); ?></label>
                <input type="text" name="subject" id="subject" placeholder="Enter Subject"
                    style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 999px;">
            </div> -->
        </div> 

        <!-- RIGHT COLUMN -->
        <div style="flex: 1; min-width: 300px;">
            <!-- Dynamic Form (including message field) will render here -->
            <div id="dynamic-form">
                <?php
                $options = array('mode' => 'create');
                foreach ($forms as $form) {
                    include(CLIENTINC_DIR . 'templates/dynamic-form.tmpl.php');
                } ?>
            </div>
        </div>
    </div>

    <!-- CAPTCHA -->
    <?php
    if ($cfg && $cfg->isCaptchaEnabled() && (!$thisclient || !$thisclient->isValid())) {
        if ($_POST && $errors && !$errors['captcha'])
            $errors['captcha'] = __('Please re-enter the text again');
    ?>
        <div class="captchaRow" style="margin-top:2rem;">
            <label><?php echo __('CAPTCHA Text'); ?>:</label>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span class="captcha"><img src="captcha.php" border="0" align="left"></span>
                <input id="captcha" type="text" name="captcha" size="6" autocomplete="off"
                    style="padding: 10px; border-radius: 8px; border: 1px solid #ccc;">
            </div>
            <em><?php echo __('Enter the text shown on the image.'); ?></em>
            <font class="error"><?php echo $errors['captcha']; ?></font>
        </div>
    <?php } ?>

    <!-- Submit Button -->
    <div style="text-align: right; margin-top: 2rem; padding-right: 5rem">
        <button type="submit"
            style="background-color: #00a651; color: white; border: none; padding: 10px 30px; border-radius: 999px; font-weight: bold; cursor: pointer;">
            <?php echo __('Submit Request'); ?>
        </button>
    </div>
</form>