<div class="container">
    <h1>BroadcastController/edit/:broadcast_id</h1>

    <div class="box">
        <h2>Edit a broadcast</h2>

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <?php if ($this->broadcast) { ?>
            <form method="post" action="<?php echo Config::get('URL'); ?>broadcast/editSave">
                 <input type="hidden" name="broadcast_id" value="<?php echo htmlentities($this->broadcast->broadcast_id); ?>" />
                
                <label>Change name of Broadcast: </label><input type="text" name="broad_name"
                    value="<?php echo htmlentities($this->broadcast->broadcast_name); ?>" /><br>
                
                <label>Change description of Broadcast: </label><br><textarea rows="10" cols="80" 
                    name="broad_desc"><?php echo htmlentities($this->broadcast->broadcast_desc); ?></textarea><br>
                <input type="submit" value='Change' />
            </form>
        <?php } else { ?>
            <p>This note does not exist.</p>
        <?php } ?>
    </div>
</div>
