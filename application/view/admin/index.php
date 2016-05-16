<div class="container">
    <h1>Admin/index</h1>

    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <h3>Wow, I really haven't done much with this page huh?</h3>
        
        <ul>
	        <li><a href="<?= Config::get('URL')?>admin/allUsers">List all users</a></li>
	        <li><a href="<?= Config::get('URL')?>admin/showLog">Show system log</a></li>
	        <li><a href="<?= Config::get('URL')?>admin/editSections">Edit store sections</a></li>
	        <li><a href="<?= Config::get('URL')?>admin/editApps">Edit apps</a></li>
        </ul>

    </div>
</div>
