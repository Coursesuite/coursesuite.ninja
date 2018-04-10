<div class="uk-offcanvas-content">

<header style="background-image: url(/img/header-preview.jpg)" class="uk-background-cover">
    <nav class="uk-navbar-container uk-navbar-transparent">
        <div class='uk-navbar-center'>
            <a class='uk-navbar-toggle' href='#' uk-toggle='target: #offcanvas-usage'>
                <span uk-navbar-toggle-icon></span> <span class='uk-margin-small-left'>
                    <img src="<?php echo $baseurl; ?>img/coursesuite-white.svg">
                </span>
            </a>
        </div>
    </nav>
</header>
<?php if ($this->page() !== "home") { ?>
<nav class='uk-section uk-section-xsmall cs-section-breadcrumb<?php echo ($this->page() === "products" && isset($this->App)) ? " cs-bgcolour-{$this->App->app_key} uk-light" : " uk-section-muted"; ?>'><div class='uk-container'>
    <ul class='uk-breadcrumb'>
        <?php foreach ($this->breadcrumb() as $item) {
            echo "<li>";
            if ($item['route']===false) {
                echo "<span>{$item['label']}</span>";
            } else {
                echo "<a href='{$baseurl}{$item['route']}'>{$item['label']}</a>";
            }
            echo "</li>" . PHP_EOL;
        } ?>
    </ul>
</div></nav>
<?php } ?>