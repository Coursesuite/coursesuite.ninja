<div class="container">
    <h1>My Apps</h1>
    <div class="my-apps">
    <?php foreach ($this->subscription->tier->apps as $app) { ?>
        <div class="app-tile">
            <figure>
                <a href="<?= $app->launch . "?token=" . $this->token ?>" target="_blank"><img src="<?= $app->icon ?>"></a>
                <figcaption><?= $app->name ?></figcaption>
            </figure>
        </div>
    <?php } ?>
    </div>
    <div class="box">
        <?php $this->renderFeedbackMessages(); ?>
        <p>I think what we do is have a selection process so you can pick an app, then after a postback it shows the details and launch url here.</p>
        <h3>What data do we have access to right now ?</h3>
        <pre>
        <?php print_r($this) ?>
        </pre>
    </div>
</div>