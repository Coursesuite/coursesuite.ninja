<article class="system-users admin-tools">

    <h3>
        <a href="<?= Config::get('URL') ?>admin" class="back button">Back to admin tools</a>
        All users
    </h3>

    <?php $this->renderFeedbackMessages(); ?>

    <form method="post" action="<?= Config::get('URL') ?>admin/allUsers/search">
        <div><label for="q">Search for a user:</label> <input type="text" id="q" name="q" value="<?php $q= Request::get("q"); echo isset($q) ? $q : ""; ?>"> <input type="submit" value="search"></div>
        <div><span>- or -</span><a href="<?= Config::get('URL') ?>admin/allUsers/">Just show most recent 25 users</a></div>
    </form>

    <table>
        <thead>
        <tr>
            <td>Id</td>
            <td>Avatar</td>
            <td>Account Type</td>
            <td>Username</td>
            <td>User's email</td>
            <td>Activated ?</td>
            <td>Logons/Cap</td>
            <td>Last Login</td>
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
                <td><?php switch($user->user_account_type) {
                    case 1: echo 'Basic';
                            break;
                    case 3: echo 'Trial';
                            break;
                    case 7: echo 'Admin';
                            break;
                    default: echo $user->user_account_type;
                } ?></td>
                <td><?= $user->user_name; ?></td>
                <td><?= $user->user_email; ?></td>
                <form action="<?= config::get("URL"); ?>admin/actionAccountSettings" method="post">
                    <td><?= ($user->user_active == 0) ? "No" : "Yes" ?></td>
                    <td><?php echo (isset($user->logon_count) ? $user->logon_count : "?"); ?> / <input type="number" name="logonCap" size="3" min="-1" max="65534" value="<?php echo (isset($user->logon_cap) ? $user->logon_cap : '-1'); ?>"></td>
                    <td><?php echo $user->last_login; ?></td>
                    <td><input type="number" name="suspension" size="3" min="0" max="365" value="0" /></td>
                    <td>
                        <label><input type="checkbox" name="softDelete" <?php if ($user->user_deleted) { ?> checked <?php } ?> /> Mark as Deleted (soft) </label><br>
                        <label><input type="checkbox" name="hardDelete" /> Delete from database (hard)</label>
                        <?php if ($user->user_active == 0) { ?><label><input type='checkbox' name='manualActivation' /> Manually activate user</label><?php } ?>
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