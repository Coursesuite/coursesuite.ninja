    <?php foreach ($this->apps as $app) { ?>
        <div class="app-tile">
            <figure>
                <a href="<?= $app->launch . "?token=" . $this->token ?>" target="_blank"><img src="<?= $app->icon ?>"></a>
                <figcaption><?= $app->name ?></figcaption>
            </figure>
        </div>
    <?php } ?>