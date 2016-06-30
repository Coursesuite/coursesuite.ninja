<div class="container">
    <h1>UserController/showProfile</h1>

    <div class="box">
        <h2>Your profile</h2>

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <div>Your username: <?= $this->user_name; ?> <a href="<?= Config::get('URL') . 'user/editUsername'; ?>">Edit</a></div>
        <div>Your email: <?= $this->user_email; ?> <a href="<?= Config::get('URL') . 'user/editUserEmail'; ?>">Edit</a></div>
        <div>Your avatar image:
            <?php if (Config::get('USE_GRAVATAR')) { ?>
                Your gravatar pic (on gravatar.com): <img src='<?= $this->user_gravatar_image_url; ?>' />
            <?php } else { ?>
                Your avatar pic (saved locally): <img src='<?= $this->user_avatar_file; ?>?<?= rand(1000,99999); ?>' />
            <?php } ?>
         <a href="<?= Config::get('URL') . 'user/editAvatar'; ?>">Edit</a></div>
        <div>Your account type is: <?= $this->user_account_type; ?>  <a href="<?= Config::get('URL') . 'user/changeUserRole'; ?>">Edit</a></div>
        <div> <a href="<?= Config::get('URL') . 'user/changePassword'; ?>">Change password</a> </div>
        <div> <a href="<?= Config::get('URL') . 'user/changeNewsletterSubscription'; ?>">Update newsletter subscription</a></div>
    </div>
</div>