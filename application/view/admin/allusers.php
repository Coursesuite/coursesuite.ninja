<article class="system-users admin-tools">

    <h3>
        <a href="<?= Config::get('URL') ?>admin" class="back button">Back to admin tools</a>
        All users
    </h3>

    <?php $this->renderFeedbackMessages(); ?>

    <table>
        <thead>
        <tr>
            <td>Id</td>
            <td>Avatar</td>
            <td>Username</td>
            <td>User's email</td>
            <td>Activated ?</td>
            <td>Suspension time in days</td>
            <td>Action</td>
            <td>Submit</td>
        </tr>
        </thead>
        <?php foreach ($this->users as $user) { ?>
            <tr>
                <td><a href="<?= Config::get('URL') . 'profile/showProfile/' . $user->user_id; ?>"><?= $user->user_id; ?></a></td>
                <td class="avatar">
                    <?php if (isset($user->user_avatar_link)) { ?>
                        <img src="<?= $user->user_avatar_link; ?>"/>
                    <?php } ?>
                </td>
                <td><?= $user->user_name; ?></td>
                <td><?= $user->user_email; ?></td>
                <form action="<?= config::get("URL"); ?>admin/actionAccountSettings" method="post">
                    <td><?= ($user->user_active == 0) ? "No" : "Yes" ?></td>
                    <td><input type="number" name="suspension" size="3" min="0" max="365" value="0" /></td>
                    <td>
                        <label><input type="checkbox" name="softDelete" <?php if ($user->user_deleted) { ?> checked <?php } ?> /> Soft Delete</label>
                        <label><input type="checkbox" name="hardDelete" /> Full Delete</label>
                        <?php if ($user->user_active == 0) { ?><label><input type='checkbox' name='manualActivation' /> Activate</label><?php } ?>
                    </td>
                    <td>
                        <input type="hidden" name="user_id" value="<?= $user->user_id; ?>" />
                        <input type="submit" />
                    </td>
                </form>
            </tr>
        <?php } ?>
    </table>

 </article>