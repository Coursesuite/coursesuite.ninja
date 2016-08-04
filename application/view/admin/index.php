<?php
$tools = array(
    "staticPage" => array("label" => "Edit static pages", "icon" => "cs-static-pages", "active" => true),
    "allUsers" => array("label" => "List / Search users", "icon" => "cs-users", "active" => true),
    "editSections" => array("label" => "Edit store sections", "icon" => "cs-store-sections", "active" => true),
    "editApps" => array("label" => "Edit apps", "icon" => "cs-apps", "active" => true),
    "assignApps" => array("label" => "Assign apps to store sections", "icon" => "cs-flag", "active" => false),
    "editAllProducts" => array("label" => "Edit subscription products", "icon" => "cs-products", "active" => true),
    "editTiers" => array("label" => "Edit tiers", "icon" => "cs-tiers", "active" => true),
    "manualSubscribe" => array("label" => "Manually manage subscriptions", "icon" => "cs-switch", "active" => true),
    "messages" => array("label" => "Manage messages and notifications", "icon" => "cs-notifications", "active" => true),
    "editAppTierMatrix" => array("label" => "Edit app-tier matrix", "icon" => "cs-config", "active" => false),
    "manageHooks" => array("label" => "3rd party hooks / endpoints", "icon" => "cs-config", "active" => true),
);

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
    <h3>Admin tools</h3>
    <?php
        $this->renderFeedbackMessages();

        echo "<section class='admin-tiles'>";
        foreach ($tools as $route => $tile) {
            renderTile($route, $tile);
        }
        echo "</section>";

    ?>
</article>
