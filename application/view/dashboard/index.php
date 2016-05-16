<div class="container">
    <h1>My Apps</h1>
    <div class="my-apps">
    <?php foreach ($this->subscription->tier->apps as $app) { ?>
        <div class="app-tile">
            <figure<?php if ($this->selected === $app->app_key) { echo " class='selected'"; } ?>>
                <a href="<?php echo Config::get('URL'); ?>dashboard/app/<?= $app->app_key ?>"><img src="<?= $app->icon ?>"></a>
                <figcaption>
                    <span class="name"><?= $app->name ?></span>
                    <a href="<?= $app->launch . "?token=" . $this->token ?>" target="_blank" class="launch-button">Launch</a>
                </figcaption>
            </figure>
        </div>
    <?php } ?>
    </div>
    <div class="box">
        <?php $this->renderFeedbackMessages(); ?>
        <?php if (strlen($this->feed) == 0) { ?>
        <p>Click the app above to select it, then after a postback it shows the details here.</p>
        <?php
                if ($this->apikey) {
                    echo "<p><a href='http://document.scormification.ninja.dev/app/?apikey=" . $this->apikey . "' target='_blank'>launch docninja using apikey</a></p>";
                }
            } else {
                echo "<p>" . $this->feed . "</p>";
            }
        ?>
    </div>
</div>