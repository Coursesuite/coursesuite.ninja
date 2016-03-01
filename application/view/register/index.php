<div class="container">
    <h1>
        Register
    </h1>
    <div class="login-page-box">
        <div class="table-wrapper">
            <div class="login-box">
                <h2>
                    Please fill in your details
                </h2>
                <!-- register form -->
                <form method="post" action="<?php echo Config::get('URL'); ?>register/register_action">
                <?php $rego = Session::get("form_data"); ?>
                    <input type="text" pattern="[a-zA-Z0-9]{2,64}" name="user_name" placeholder="Username (letters/numbers, 2-64 chars)" required value="<?php Text::output($rego, 'user_name'); ?>" />
                    <input type="text" name="user_email" placeholder="email address (a real address)" required value="<?php Text::output($rego, 'user_email'); ?>" />
                    <input type="text" name="user_email_repeat" placeholder="repeat email address (to prevent typos)" required value="<?php Text::output($rego, 'user_email_repeat'); ?>" />
                    <input type="password" name="user_password_new" pattern=".{6,}" placeholder="Password (6+ characters)" required autocomplete="off" value="<?php Text::output($rego, 'user_password_new'); ?>" />
                    <input type="password" name="user_password_repeat" pattern=".{6,}" required placeholder="Repeat your password" autocomplete="off" value="<?php Text::output($rego, 'user_password_repeat'); ?>" />
                    <div class="g-recaptcha" data-sitekey="<?php echo Config::get('GOOGLE_CAPTCHA_SITEKEY'); ?>"></div>
                    <script type="text/javascript" src="//www.google.com/recaptcha/api.js?hl=en"></script>
                    <input type="submit" value="Register" />
                </form>
            </div>
            <div class="feedback">
                <?php $this->renderFeedbackMessages(); ?>
            </div>
        </div>
    </div>
</div>