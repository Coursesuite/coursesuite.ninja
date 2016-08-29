<article class="register-login">

    <header class='common-header margin-below'>
        <nav><a href="<?php echo Config::get('URL'); ?>"><i class='cs-home-outline'></i> Home</a> <i class='cs-arrow-right cs-small'></i> <a href="<?php echo Config::get('URL'); ?>login/">Register for free trial</a></nav>
    </header>

    <section class="login-page-container standard-width">
        <div class="free-trial-drescription">
            <header>What you get</header>
            <?php echo KeyStore::find("freeTrialDescription")->get(); ?>
        </div>

        <div class="register-box">
            <header>Create your trial account</header>
            <main>
                <form method="post" action="<?php echo Config::get('URL'); ?>register/freeTrial_action" autocomplete="off">
                <?php $rego = Session::get("form_data"); ?>
                    <input type="text" pattern="[a-zA-Z0-9]{2,64}" name="user_name" placeholder="Username (One word, letters/numbers, 2-64 chars)" required value="<?php Text::output($rego, 'user_name'); ?>" />
                    <input type="text" name="user_email" placeholder="Email address (a real address)" required value="<?php Text::output($rego, 'user_email'); ?>" />
                    <input type="text" name="user_email_repeat" placeholder="Repeat email address (to prevent typos)" required value="<?php Text::output($rego, 'user_email_repeat'); ?>" />
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