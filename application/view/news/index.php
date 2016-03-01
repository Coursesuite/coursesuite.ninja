<div class="container">
    <h1>Latest News</h1>
    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <?php if ($this->news) { ?>
            <table class="news-table">
                <thead>
                <tr>
                    <td>Id</td>
                    <td>Title</td>
                    <td>News</td>
                    <td>EDIT</td>
                    <td>DELETE</td>
                </tr>
                </thead>
                <tbody>
                    <?php foreach($this->news as $key => $value) { ?>
                        <tr>
                            <td><?= $value->news_id; ?></td>
                            <td><?= htmlentities($value->news_title); ?></td>
                            <td><?= htmlentities($value->news_text); ?></td>
                            <td><a href="<?= Config::get('URL') . 'news/edit/' . $value->news_id; ?>">Edit</a></td>
                            <td><a href="<?= Config::get('URL') . 'news/delete/' . $value->news_id; ?>">Delete</a></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php } else { ?>
                <div>No news yet.</div>
            <?php } ?>

        <?php if (Session::get("user_account_type") == 7) : ?>
        <h3>Add news</h3>
        <p>
            <form method="post" action="<?php echo Config::get('URL');?>news/create">
                <p><label>Title of news:  <input type="text" name="news_title" /></label></p>
                <p><label>Text of news:  <textarea rows=5 cols=40 name="news_text"></textarea></label></p>
                <p><input type="submit" value='Create this news item' autocomplete="off" /></p>
            </form>
        </p>
        <?php endif; ?>

    </div>
</div>
