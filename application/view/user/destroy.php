<article class="my-profile">

	<header class='common-header margin-below'>
		<nav>
			<a href="<?php echo Config::get('URL'); ?>">Home</a> <i class='cs-arrow-right cs-small'></i> <a href="<?php echo Config::get('URL'); ?>user/">My Account</a> <i class='cs-arrow-right cs-small'></i> <a href="<?php echo Config::get('URL'); ?>user/destroy">Delete my account</a>
		</nav>
	</header>

	<section class="profile-feedback standard-width">
	    <?php $this->renderFeedbackMessages(); ?>
	</section>

	<section class="profile-display standard-width">

        <form action="<?php echo Config::get('URL'); ?>user/destroy_action" method="post">
            <label>
                To delete your account forever, type <i class="unselectable" unselectable="on">delete me forever</i>: <input type="text" name="confirm_destroy" required />
            </label>
			<!-- set CSRF token at the end of the form -->
			<input type="hidden" name="csrf_token" value="<?= Csrf::makeToken(); ?>" />
            <p><input type="submit" value="Submit" /> <a href="<?php echo Config::get('URL'); ?>user" class="cancel-link">Cancel</a></p>
        </form>

    </section>
</article>
