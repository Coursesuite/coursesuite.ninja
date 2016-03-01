<div class="container">
    <h1>NewsController/edit/:news_id</h1>

    <div class="box">
        <h2>Edit news</h2>

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <?php if ($this->news) { ?>
            <form method="post" action="<?php echo Config::get('URL'); ?>news/editSave">
                <label>Change text of news: </label>
                <!-- we use htmlentities() here to prevent user input with " etc. break the HTML -->
                <input type="hidden" name="news_id" value="<?php echo htmlentities($this->news->news_id); ?>" />
                <input type="text" size="40" name="news_title" value="<?php echo htmlentities($this->news->news_title); ?>" />
                <textarea rows=5 cols=40 name="news_text" value="<?php echo htmlentities($this->news->news_text); ?>" />
                <input type="submit" value='Change' />
            </form>
        <?php } else { ?>
            <p>This item does not exist.</p>
        <?php } ?>
    </div>
</div>
