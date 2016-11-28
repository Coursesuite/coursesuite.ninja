<article class="my-profile">

	<header class='common-header margin-below'>
		<nav>
			<a href="<?php echo Config::get('URL'); ?>"><i class='cs-home-outline'></i> Home</a> <i class='cs-arrow-right cs-small'></i> <a href="<?php echo Config::get('URL'); ?>user/">My Account</a> <i class='cs-arrow-right cs-small'></i> <a href="<?php echo Config::get('URL'); ?>user/changePassword">Change password</a>
		</nav>
	</header>

	<section class="profile-feedback standard-width">
	    <?php $this->renderFeedbackMessages(); ?>
	</section>

	<section class="profile-display standard-width">

        <form action="<?php echo Config::get('URL'); ?>user/changePassword_action" method="post" name="new_password_form">
            <label>Enter current password:<label>
            <input id="change_input_password_current" class="reset_input" type='password'
                   name='user_password_current' pattern=".{6,}" required autocomplete="off"  />
            </label>
            <label>New password (min. 6 characters):</label>
            <input id="change_input_password_new" class="reset_input" type="password"
                   name="user_password_new" pattern=".{6,}" required autocomplete="off" />
            <label>Repeat new password:</label>
            <input id="change_input_password_repeat" class="reset_input" type="password"
                   name="user_password_repeat" pattern=".{6,}" required autocomplete="off" />

			<!-- set CSRF token at the end of the form -->
			<input type="hidden" name="csrf_token" value="<?= Csrf::makeToken(); ?>" />
            <p><input type="submit" name="submit_new_password" value="Submit" /> <a href="<?php echo Config::get('URL'); ?>user" class="cancel-link">Cancel</a></p>
        </form>
    </section>
</article>