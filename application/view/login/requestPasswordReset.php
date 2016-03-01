<div class="container">
    <h1>
        Forgotten password
    </h1>
    <div class="login-page-box">
        <div class="table-wrapper">
            <div class="login-box">
                <h2>
                    Request a new password
                </h2>
                <?php $fields = Session::get("form_data"); ?>
                <form method="post" action="<?php echo Config::get('URL'); ?>login/requestPasswordReset_action">
                    <label for="user_name_or_email">
                        Enter your username or email and you'll get a mail with instructions:
                        <input type="text" name="user_name_or_email" required value="<?php Text::output($fields, 'user_name_or_email');  ?>" />
                    </label>
                    <div class="g-recaptcha" data-sitekey="<?php echo Config::get('GOOGLE_CAPTCHA_SITEKEY'); ?>"></div>
                    <script type="text/javascript" src="//www.google.com/recaptcha/api.js?hl=en"></script>
                    <input type="submit" value="Send me a password-reset mail" />
                </form>
            </div>
            <div class="feedback">
                <?php $this->renderFeedbackMessages(); ?>
            </div>
        </div>
    </div>
</div>

</div>
