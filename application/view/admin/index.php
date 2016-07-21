<article class="system-index admin-tools">
    <h3>Admin tools</h3>

    <?php $this->renderFeedbackMessages(); ?>

    <p>I really haven't done much with the look of this page yet. Controllers will be linked as they are coded ...</p>

    <ul>
        <li><a href="<?= Config::get('URL')?>admin/staticPage">Edit static pages</a></li>
        <li><a href="<?= Config::get('URL')?>admin/allUsers">List users</a></li>
        <li><a href="<?= Config::get('URL')?>admin/editSections">Edit store sections</a></li>
        <li><a href="<?= Config::get('URL')?>admin/editApps">Edit apps</a></li>
        <li>Assign apps to store sections</li>
        <li><a href="<?= Config::get('URL')?>admin/editAllProducts">Edit all products</a></li>
        <li><a href="<?= Config::get('URL')?>admin/showLog">Show system log</a></li>
        <li><a href="<?= Config::get('URL')?>admin/editTiers">Edit tiers</a></li>
        <li>Manage app-tier feature matrix</li>
        <li>Manage subscriptions</li>
        <li><a href="<?= Config::get('URL')?>admin/manualSubscribe">Manually add a subscription</a></li>
        <li><a href="<?= Config::get('URL')?>admin/messages">Send and manage user notifications</a></li>
    </ul>

</article>
