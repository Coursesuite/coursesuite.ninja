<article class="register-login">
    
    <header class='common-header margin-below'>
        <nav><a href="<?php echo Config::get('URL'); ?>">Home</a> <i class='cs-arrow-right cs-small'></i> <a href="<?php echo Config::get('URL'); ?>login/">Login or register</a></nav>
    </header>
    
    <section class="login-page-container standard-width">

        <div class="login-box">
            <header>Existing users</header>
            <main>
                <form action="<?php echo Config::get('URL'); ?>login/login" method="post">
                    <input type="text" name="user_name" placeholder="Username or email" required />
                    <input type="password" name="user_password" placeholder="Password" required />
                    <label for="set_remember_me_cookie" class="remember-me-label">
                        <input type="checkbox" name="set_remember_me_cookie" class="remember-me-checkbox" />
                        Remember me for 2 weeks
                    </label>
                    <?php if (!empty($this->redirect)) { ?>
                    <input type="hidden" name="redirect" value="<?= $this->encodeHTML($this->redirect); ?>" />
                    <?php } ?>
                    <input type="hidden" name="csrf_token" value="<?= Csrf::makeToken(); ?>" />
                    <?php $this->renderFeedbackMessages('login'); ?>
                    <input type="submit" class="login-submit-button" value="Log in"/>
                </form>
                <div class="form-nav-link">
                    <a href="<?php echo Config::get('URL'); ?>login/requestPasswordReset">I forgot my password</a>
                </div>
            </main>
        </div>

        <div class="register-box">
            <header>New users</header>
            <main>
                <form method="post" action="<?php echo Config::get('URL'); ?>register/register_action" autocomplete="off">
                <?php $rego = Session::get("form_data"); ?>
                    <input type="text" pattern="[a-zA-Z0-9]{2,64}" name="user_name" placeholder="Username (letters/numbers, 2-64 chars)" required value="<?php Text::output($rego, 'user_name'); ?>" />
                    <input type="text" name="user_email" placeholder="email address (a real address)" required value="<?php Text::output($rego, 'user_email'); ?>" />
                    <input type="text" name="user_email_repeat" placeholder="repeat email address (to prevent typos)" required value="<?php Text::output($rego, 'user_email_repeat'); ?>" />
                    <input type="password" name="user_password_new" pattern=".{6,}" placeholder="Password (6+ characters)" required autocomplete="off" value="<?php Text::output($rego, 'user_password_new'); ?>" />
                    <input type="password" name="user_password_repeat" pattern=".{6,}" required placeholder="Repeat your password" autocomplete="off" value="<?php Text::output($rego, 'user_password_repeat'); ?>" />
                    <label><input type="checkbox" name="user_newsletter_subscribed" checked="checked" value="true"/>Signup for CourseSuite newsletter</label>
                    <div class="g-recaptcha" data-sitekey="<?php echo Config::get('GOOGLE_CAPTCHA_SITEKEY'); ?>"></div>
                    <script type="text/javascript" src="//www.google.com/recaptcha/api.js?hl=en"></script>
                    <?php $this->renderFeedbackMessages('registration'); ?>
                    <input type="submit" value="Register" />
                </form>
            </main>
        </div>

    </section>
    
</article>