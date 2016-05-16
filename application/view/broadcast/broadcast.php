<div class="container">
    <h1>BroadcastController/index</h1>
    <div class="box">
        
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <h3>View Broadcasts</h3>
        
      <?php 
           
            if ($this->broadcasts) { ?>
            <table class="broadcast-table">
                <thead>
                <tr>
                    <td>Id</td>
                    <td>Broadcast Title</td>
                    <td>Broadcast Description</td>
                    <td>Date</td>
                    <td>Mark as Read</td>
                </tr>
                </thead>
                <tbody> 
                    <?php foreach($this->broadcasts as $key => $value) { ?>
                        <tr>
                            <td><?= $value->broadcast_id; ?></td>
                            <td><?= htmlentities($value->broadcast_name); ?></td>
                            <td><?= htmlentities($value->broadcast_desc); ?></td>
                            <td><?= htmlentities($value->broadcast_date); ?></td>
                            <td><a href="<?= Config::get('URL') . 'broadcast/mark/' . $value->broadcast_id; ?>">Mark as read</a></td>                   
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php } else { ?>
                <div>No Broadcasts Available</div>
            <?php } ?>
        
    </div>
</div>