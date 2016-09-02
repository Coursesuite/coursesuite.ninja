<article class="register-login">
    <header class="common-header margin-below">
        <nav>
            <a href="<?php echo Config::get('URL'); ?>"><i class="cs-home-outline"></i> Home</a>
            <i class="cs-arrow-right cs-small"></i>
            <a href="<?php echo Config::get('URL'); ?>login/">Login or register</a>
        </nav>
    </header>
    <section class="login-page-container standard-width">
        <div class="register-box">
            <header>New users</header>
            <main>
                <form action="<?php echo Config::get('URL'); ?>register/register_action" autocomplete="off" method="post">
                    <?php $rego = Session::get("form_data"); ?>
                    <input name="user_name" pattern="[a-zA-Z0-9]{2,64}" placeholder="Username (letters/numbers, 2-64 chars)" required="" type="text" value="<?php Text::output($rego, 'user_name'); ?>"/>
                    <input name="user_email" placeholder="email address (a real address)" required="" type="text" value="<?php Text::output($rego, 'user_email'); ?>"/>
                    <input name="user_email_repeat" placeholder="repeat email address (to prevent typos)" required="" type="text" value="<?php Text::output($rego, 'user_email_repeat'); ?>"/>
                    <input autocomplete="off" name="user_password_new" pattern=".{6,}" placeholder="Password (6+ characters)" required="" type="password" value="<?php Text::output($rego, 'user_password_new'); ?>"/>
                    <input autocomplete="off" name="user_password_repeat" pattern=".{6,}" placeholder="Repeat your password" required="" type="password" value="<?php Text::output($rego, 'user_password_repeat'); ?>"/>
                    <label>
                        <input checked="checked" name="user_newsletter_subscribed" type="checkbox" value="true"/>
                        Signup for CourseSuite newsletter
                    </label>
                    <div class="g-recaptcha" data-sitekey="<?php echo Config::get('GOOGLE_CAPTCHA_SITEKEY'); ?>">
                    </div>
                    <script src="//www.google.com/recaptcha/api.js?hl=en" type="text/javascript">
                    </script>
                    <?php $this->
                    renderFeedbackMessages('registration'); ?>
                    <input type="submit" value="Register"/>
                </form>
            </main>
        </div>
        <div class="login-box">
            <header>Existing users</header>
            <main>
                <form action="<?php echo Config::get('URL'); ?>login/login" method="post">
                    <input name="user_name" placeholder="Username or email" required="" type="text"/>
                    <input name="user_password" placeholder="Password" required="" type="password"/>
                    <label class="remember-me-label" for="set_remember_me_cookie">
                        <input class="remember-me-checkbox" name="set_remember_me_cookie" type="checkbox"/>
                        Remember me for 2 weeks
                    </label>
                    <?php if (!empty($this->
                    redirect)) { ?>
                    <input name="redirect" type="hidden" value="<?= $this->encodeHTML($this->redirect); ?>"/>
                    <?php } ?>
                    <input name="csrf_token" type="hidden" value="<?= Csrf::makeToken(); ?>"/>
                    <?php $this->
                    renderFeedbackMessages('login'); ?>
                    <input class="login-submit-button" type="submit" value="Log in"/>
                </form>
                <div class="form-nav-link">
                    <a href="<?php echo Config::get('URL'); ?>login/requestPasswordReset">
                        I forgot my password
                    </a>
                </div>
            </main>
        </div>
    </section>
</article>