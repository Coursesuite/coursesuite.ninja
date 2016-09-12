<article class="my-profile">

	<header class='common-header margin-below'>
		<nav>
			<a href="<?php echo Config::get('URL'); ?>"><i class='cs-home-outline'></i> Home</a> <i class='cs-arrow-right cs-small'></i> <a href="<?php echo Config::get('URL'); ?>user/">My Account</a> <i class='cs-arrow-right cs-small'></i> <a href="<?php echo Config::get('URL'); ?>user/changeNewsletterSubscription">Change newsletter subscription</a>
		</nav>
	</header>

	<section class="profile-display standard-width">
	<form class='newsletter-update' action="<?php echo Config::get('URL'); ?>/user/changeNewsletterSubscription_action" method="post">
		<h4>Newsletter Subscription</h4>
		<label><input type='checkbox' id='subscribed' <?php if(MailChimp::isUserSubscribed(Session::get('user_email'))) echo('checked="checked"'); ?> />General Subscription</label>
		<br/>
		<h4>Specific Interests</h4>
		<div id='interests'>
			<label><input type='checkbox' id="apps">Apps</label>
			<label><input type='checkbox' id="plugins">Plugins</label>
			<label><input type='checkbox' id="courseCatalouge">Course Catalouge</label>
		</div>
	<form>
	</section>

</article>