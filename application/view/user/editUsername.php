<article class="my-profile">

	<header class='common-header margin-below'>
		<nav>
			<a href="<?php echo Config::get('URL'); ?>"><i class='cs-home-outline'></i> Home</a> <i class='cs-arrow-right cs-small'></i> <a href="<?php echo Config::get('URL'); ?>user/">My Account</a> <i class='cs-arrow-right cs-small'></i> <a href="<?php echo Config::get('URL'); ?>user/editUsername">Edit username</a>
		</nav>
	</header>

	<section class="profile-feedback standard-width">
	    <?php $this->renderFeedbackMessages(); ?>
	</section>

	<section class="profile-display standard-width">

        <form action="<?php echo Config::get('URL'); ?>user/editUserName_action" method="post">
            <label>
                New username: <input type="text" name="user_name" required />
            </label>
			<!-- set CSRF token at the end of the form -->
			<input type="hidden" name="csrf_token" value="<?= Csrf::makeToken(); ?>" />
            <p><input type="submit" value="Submit" /> <a href="<?php echo Config::get('URL'); ?>user" class="cancel-link">Cancel</a></p>
        </form>

    </section>
</article>
