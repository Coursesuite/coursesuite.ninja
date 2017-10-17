<?php


function renderTile($route, $data) {
    if ($data["active"] !== true) return;
    $stub = Config::get('URL');
    echo "<figure class='admin-tile'>";
    echo "<a href='$stub"."admin/$route'>";
    echo "<div class='icon'><i class='" . $data["icon"] . "'></i></div>";
    echo "<figcaption>".$data["label"]."</figcaption>";
    echo "</a>";
    echo "</figure>";
}

?>
<article class="system-index admin-tools">
    <section class='section standard-width'>
    <header class="admin-header">
        <h1>Admin tools</h1>
    </header>
    <?php
        $this->renderFeedbackMessages();

        echo "<section class='admin-tiles'>";
        foreach ($tools as $route => $tile) {
            renderTile($route, $tile);
        }
        echo "</section>";

    ?>
    </section>
</article>
