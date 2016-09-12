<div class="container">
    <h1>ProfileController/showProfile/:id</h1>
    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <h3>What happens here ?</h3>
        <div>This controller/action/view shows all public information about a certain user.</div>
        <br/>

        <?php if ($this->user) { ?>
            <div>
                <table class="overview-table">
                    <thead>
                    <tr>
                        <td>Id</td>
                        <td>Avatar</td>
                        <td>Account Type</td>
                        <td>Username</td>
                        <td>User's email</td>
                        <td>Activated?</td>
                        <td>Created</td>
                        <td>Last Login</td>
                        <td>Newsletter Subscription?</td>
                        <td>Free Trial Available?</td>
                    </tr>
                    </thead>
                    <tbody>
                        <tr class="<?= ($this->user->user_active == 0 ? 'inactive' : 'active'); ?>">
                            <td><?= $this->user->user_id; ?></td>
                            <td class="avatar">
                                <?php if (isset($this->user->user_avatar_link)) { ?>
                                    <img src="<?= $this->user->user_avatar_link; ?>" />
                                <?php } ?>
                            </td>
                            <td><?php switch($this->user->user_account_type) {
                                case 1: echo 'Basic';
                                        break;
                                case 3: echo 'Trial';
                                        break;
                                case 7: echo 'Admin';
                                        break;
                                default: echo $this->user->user_account_type;
                            } ?>
                            </td>
                            <td><?= $this->user->user_name; ?></td>
                            <td><?= $this->user->user_email; ?></td>
                            <td><?= ($this->user->user_active == 0 ? 'No' : 'Yes'); ?></td>
                            <td><?= $this->user->user_creation_timestamp; ?></td>
                            <td><?= $this->user->user_last_login_timestamp; ?></td>
                            <td><?= ($this->user->user_newsletter_subscribed == 1 ? 'Yes' : 'No'); ?></td>
                            <td><?= ($this->user->user_free_trial_available == 1 ? 'Yes' : 'No'); ?></td>
                        </tr>
                    </tbody>
                </table>
                <h3>Subscription Info</h3>
                <?php if($this->user->subscription != Null) : ?>
                <table class="overview-table">
                    <thead>
                    <tr>
                        <td>Tier Id</td>
                        <td>Reference Id</td>
                        <td>Status</td>
                        <td>Info</td>
                        <td>Added</td>
                        <td>Expires</td>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>  
                            <td><?= $this->user->subscription->tier_id; ?></td>
                            <td><a href='<?= $this->user->subscription->subscriptionUrl; ?>'><?= $this->user->subscription->referenceId; ?></a></td>
                            <td><?= $this->user->subscription->status; ?></td>
                            <td><?= $this->user->subscription->info; ?></td>
                            <td><?= $this->user->subscription->added; ?></td>
                            <td><?= $this->user->subscription->endDate; ?></td>
                        </tr>
                    </tbody>
                </table>
                <?php else : ?>
                <h4>No Subscription</h4>
                <?php endif; ?>
            </div>
        <?php } ?>

    </div>
</div>
