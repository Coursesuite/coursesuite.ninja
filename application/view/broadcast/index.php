<div class="container">
    <h1>BroadcastController/index</h1>
    <div class="box">
        
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <h3>Create/Update Broadcasts</h3>
        <p>
            <form method="post" action="<?php echo Config::get('URL');?>broadcast/create"> 
                <label>Broadcast Title: </label><input type="text" name="broad_name" />Must be between 8 - 128 characters long<br><br>
                <label>Broadcast Description: </label><br><textarea rows="10" cols="80" name="broad_desc"></textarea>
                Must be between 50 - 528 characters long<br>
                <input type="submit" value='Create this broadcast' autocomplete="off" />
            </form>
        </p>
    
        <?php
         if ($this->broadcasts) { ?>
            <table class="broadcast-table">
                <thead>
                <tr>
                    <td>Id</td>
                    <td>Broadcast Title</td>
                    <td>Broadcast Description</td>
                    <td>Date</td>
                    <td>EDIT</td>
                    <td>DELETE</td>
                </tr>
                </thead>
                <tbody>
                    <?php foreach($this->broadcasts as $key => $value) { ?>
                        <tr>
                            <td><?= $value->broadcast_id; ?></td>
                            <td><?= htmlentities($value->broadcast_name); ?></td>
                            <td><?= htmlentities($value->broadcast_desc); ?></td>
                            <td><?= htmlentities($value->broadcast_date); ?></td>
                            <td><a href="<?= Config::get('URL') . 'broadcast/edit/' . $value->broadcast_id; ?>">Edit</a></td>
                            <td><a href="<?= Config::get('URL') . 'broadcast/delete/' . $value->broadcast_id; ?>">Delete</a></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php } else { ?>
                <div>No Broadcasts created yet. Create some !</div>
            <?php } ?>
    </div>
</div>
