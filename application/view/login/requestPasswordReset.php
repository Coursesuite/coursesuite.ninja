    <div class="login-page-container">

        <div class="login-reset-box">
	       	<header>Reset my password</header>
	       	<main>
                <?php $fields = Session::get("form_data"); ?>
                <form method="post" action="<?php echo Config::get('URL'); ?>login/requestPasswordReset_action">
                    <label for="user_name_or_email">Enter your username or email and you'll get a mail with instructions:</label>
                    <input type="text" name="user_name_or_email" required value="<?php Text::output($fields, 'user_name_or_email');  ?>" placeholder="Enter username or email " />

                    <div class="g-recaptcha" data-sitekey="<?php echo Config::get('GOOGLE_CAPTCHA_SITEKEY'); ?>"></div>
                    <script type="text/javascript" src="//www.google.com/recaptcha/api.js?hl=en"></script>
					<?php $this->renderFeedbackMessages(); ?>
                    <input type="submit" value="Send me a password-reset mail" />
                </form>
	            <div class="form-nav-link">
                <a href="<?php echo Config::get('URL'); ?>login/index">Back to Login Page</a>
	            </div>
            </main>
        </div>
    </div>