<article class="register-login">

	<header class='common-header margin-below'>
		<nav><a href="<?php echo Config::get('URL'); ?>"><i class='cs-home-outline'></i> Home</a> <i class='cs-arrow-right cs-small'></i> <a href="<?php echo Config::get('URL'); ?>login/">Login</a></nav>
	</header>

    <section class="login-page-container standard-width">

        <div class="login-box">
	       	<header>Login</header>
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
    </section>
</article>

