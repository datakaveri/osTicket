<?php
include_once(INCLUDE_DIR . 'staff/login.header.php');
$info = ($_POST && $errors) ? Format::htmlchars($_POST) : array();

if ($thisstaff && $thisstaff->is2FAPending())
    $msg = "2FA Pending";

$staffLoginBody = ($content) ? trim(Format::display($content->getLocalBody())) : '';
?>
<div class="maha-staff-login-page">
    <div class="maha-staff-login-shell">
        <section id="loginBox" class="maha-staff-login-card">
            <div class="maha-staff-login-card__inner">
                <div class="maha-staff-login-card__brand">
                    <img src="<?php echo ROOT_PATH; ?>assets/default/images/mahaagx/mahaagx-logo.svg"
                        alt="<?php echo __('MahaAgX'); ?>" />
                </div>
                <p class="maha-staff-login-card__eyebrow"><?php echo __('Secure Staff Access'); ?></p>
                <h2 class="maha-staff-login-card__title"><?php echo __('Sign in to continue'); ?></h2>
                <h3 id="login-message" class="maha-staff-login-card__message"><?php echo Format::htmlchars($msg); ?></h3>
                <?php if ($staffLoginBody !== '') { ?>
                    <div class="banner maha-staff-login-card__banner"><small><?php echo $staffLoginBody; ?></small></div>
                <?php } ?>

                <div id="loading" style="display:none;" class="dialog maha-staff-login-loading">
                    <h1><i class="icon-spinner icon-spin icon-large"></i>
                        <?php echo __('Verifying'); ?></h1>
                </div>

                <form action="login.php" method="post" id="login" class="maha-staff-login-form" onsubmit="attemptLoginAjax(event)">
                    <?php csrf_token();
                    if (
                        $thisstaff
                        &&  $thisstaff->is2FAPending()
                        && ($bk = $thisstaff->get2FABackend())
                        && ($form = $bk->getInputForm($_POST))
                    ) {
                        include STAFFINC_DIR . 'templates/dynamic-form-simple.tmpl.php';
                    ?>
                        <fieldset class="maha-staff-login-form__fieldset">
                            <input type="hidden" name="do" value="2fa">
                            <button class="submit button maha-staff-login-form__submit" type="submit"
                                name="submit"><i class="icon-signin"></i>
                                <?php echo __('Verify'); ?>
                            </button>
                        </fieldset>
                    <?php
                    } else { ?>
                        <input type="hidden" name="do" value="scplogin">
                        <fieldset class="maha-staff-login-form__fieldset">
                            <label class="maha-staff-login-form__label" for="name"><?php echo __('Email or Username'); ?></label>
                            <input type="text" name="userid" id="name" value="<?php
                                                                                echo $info['userid'] ?? null; ?>" placeholder="<?php echo __('Enter your email or username'); ?>"
                                autofocus autocorrect="off" autocapitalize="off">

                            <label class="maha-staff-login-form__label" for="pass"><?php echo __('Password'); ?></label>
                            <input type="password" name="passwd" id="pass" maxlength="128" placeholder="<?php echo __('Enter your password'); ?>" autocorrect="off" autocapitalize="off">

                            <div class="maha-staff-login-form__actions">
                                <a id="reset-link" class="maha-staff-login-form__reset <?php
                                                                                        if (!$show_reset || !$cfg->allowPasswordReset()) echo 'hidden';
                                                                                        ?>" href="pwreset.php"><?php echo __('Forgot My Password'); ?></a>
                                <button class="submit button maha-staff-login-form__submit" type="submit"
                                    name="submit"><i class="icon-signin"></i>
                                    <?php echo __('Log In'); ?>
                                </button>
                            </div>
                        </fieldset>
                    <?php
                    } ?>
                </form>

                <div id="company" class="maha-staff-login-card__company">
                    <div class="content">
                        <?php echo __('Copyright'); ?> &copy; <?php echo Format::htmlchars($ost->company) ?: date('Y'); ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div id="poweredBy" class="maha-staff-powered-by"><?php echo __('Powered by'); ?>
        <a href="http://www.osticket.com" target="_blank" rel="noopener">
            <img alt="osTicket" src="images/osticket-grey.png" class="osticket-logo">
        </a>
    </div>
</div>
<script>
    function attemptLoginAjax(e) {
        $('#loading').show();
        var objectifyForm = function(formArray) { //serialize data function
            var returnArray = {};
            for (var i = 0; i < formArray.length; i++) {
                returnArray[formArray[i]['name']] = formArray[i]['value'];
            }
            return returnArray;
        };
        if ($.fn.effect) {
            // For some reason, JQuery-UI shake does not considere an element's
            // padding when shaking. Looks like it might be fixed in 1.12.
            // Thanks, https://stackoverflow.com/a/22302374
            var oldEffect = $.fn.effect;
            $.fn.effect = function(effectName) {
                if (effectName === "shake") {
                    $('#loading').hide();
                    var old = $.effects.createWrapper;
                    $.effects.createWrapper = function(element) {
                        var result;
                        var oldCSS = $.fn.css;

                        $.fn.css = function(size) {
                            var _element = this;
                            var hasOwn = Object.prototype.hasOwnProperty;
                            return _element === element && hasOwn.call(size, "width") && hasOwn.call(size, "height") && _element || oldCSS.apply(this, arguments);
                        };

                        result = old.apply(this, arguments);

                        $.fn.css = oldCSS;
                        return result;
                    };
                }
                return oldEffect.apply(this, arguments);
            };
        }
        var form = $(e.target),
            data = objectifyForm(form.serializeArray())
        data.ajax = 1;
        $('button[type=submit]', form).attr('disabled', 'disabled');
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: data,
            cache: false,
            success: function(json) {
                $('button[type=submit]', form).removeAttr('disabled');
                if (!typeof(json) === 'object' || !json.status)
                    return;
                switch (json.status) {
                    case 401:
                        if (json && json.redirect)
                            document.location.href = json.redirect;
                        if (json && json.message)
                            $('#login-message').text(json.message)
                        if (json && json.show_reset)
                            $('#reset-link').show()
                        if ($.fn.effect) {
                            $('#loginBox').effect('shake')
                        }
                        // Clear the password field
                        $('#pass').val('').focus();
                        break
                    case 302:
                        if (json && json.redirect)
                            document.location.href = json.redirect;
                        break
                }
            },
        });
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        return false;
    }
</script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-ui-1.13.2.custom.min.js"></script>
</body>

</html>