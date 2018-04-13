<header class="uk-position-relative">
    <div class="uk-cover-container cs-nav-height">
        <video autoplay loop muted playsinline uk-cover>
            <source src="<?php echo $headerVideo; ?>" type="video/mp4">
            <img src="/img/header-preview.jpg">
        </video>
    </div>
    <nav class="uk-navbar-container uk-navbar-transparent cs-navbar-container uk-position-top uk-light" uk-navbar>
        <div class="uk-navbar-left uk-visible@m">
            <a href="<?php echo $baseurl; ?>" class="uk-navbar-item uk-logo">
                <img src="<?php echo $baseurl; ?>img/coursesuite.svg" uk-svg width="300" height="53">
            </a>
        </div>
        <div class="uk-navbar-right">
            <ul class="uk-navbar-nav">
                <li<?php CurrentMenu($this->page(),"home", "uk-visible@s"); ?>><a href="<?php echo $baseurl; ?>">Home</a></li>
                <li<?php CurrentMenu($this->page(),"products"); ?>>
                    <a href="<?php echo $baseurl; ?>products/">Products</a>
                    <div class="uk-width-xlarge" uk-dropdown="offset: -20; delay-show: 200; mode: hover; animation: uk-animation-slide-top-small; duration: 200">
                        <?php $sections = SectionsModel::getAllStoreSections(false,true); ?>
                        <div class="uk-dropdown-grid uk-child-width-1-<?php echo count($sections); ?>@m" uk-grid>
                            <?php foreach ($sections as $section) { ?>
                            <div>
                                <ul class="uk-nav uk-dropdown-nav">
                                    <li class="uk-nav-header"><a href="<?php echo $baseurl; ?>products/<?php echo $section->route; ?>" class="uk-link-reset"><?php echo $section->label; ?></a></li>
                                    <li class="uk-nav-divider"></li>
                                    <?php
                                    foreach (NavModel::products_nav($section->route) as $item) {
                                        echo "<li title='{$item->tagline}'>";
                                        echo   "<a href='{$baseurl}products/{$section->route}/{$item->app_key}'>";
                                        echo    "<div class='cs-bgcolour-{$item->app_key} cs-nav-icon uk-margin-small-right'>";
                                        echo     "<img src='data:image/svg+xml," . rawurlencode($item->glyph) . "' width='32' height='32' uk-svg>";
                                        echo    "</div>";
                                        echo    "<b>{$item->name}</b>";
                                        echo    "<div class='uk-text-truncate'>{$item->tagline}</div>";
                                        echo   "</a>";
                                        echo "</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </li>
                <li<?php CurrentMenu($this->page(),"pricing"); ?>><a href="<?php echo $baseurl; ?>pricing/">Pricing</a></li>
                <li<?php CurrentMenu($this->page(),"support"); ?>>
                    <a href='<?php echo (Session::userIsLoggedIn()) ? "/me/support/" : "#"; ?>'>Support</a>
                    <div class="uk-navbar-dropdown" uk-dropdown="offset: -20; delay-show: 200; mode: click, hover; animation: uk-animation-slide-top-small; duration: 200">
                        <ul class="uk-nav uk-navbar-dropdown-nav">
                            <li><a href="https://help.coursesuite.ninja/">Helpdesk</a></li>
                            <li><a href="https://guide.coursesuite.ninja/">User Guides</a></li>
                            <li><a href="https://www.youtube.com/channel/UCxjmLClwzsyhaBshrZ1FYyA">YouTube channel</a></li>
                        </ul>
                    </div>
                </li>
                <li<?php CurrentMenu($this->page(),"blog"); ?>><a href="<?php echo $baseurl; ?>blog">Blog<?php echo $blog_badge; ?></a></li>
                <li<?php CurrentMenu($this->page(),"about,testimonials,services"); ?>>
                    <a href="#">Company</a>
                    <div class="uk-navbar-dropdown" uk-dropdown="offset: -20; delay-show: 200; mode: click, hover; animation: uk-animation-slide-top-small; duration: 200">
                        <ul class="uk-nav uk-navbar-dropdown-nav">
                            <li><a href="<?php echo $baseurl; ?>content/about">About CourseSuite</a></li>
                            <li><a href="<?php echo $baseurl; ?>services">Contact Us</a></li>
                            <li><a href="https://www.avide.com.au">Avide eLearning</a></li>
                        </ul>
                    </div>
                </li>
                <?php if (Session::userIsAdmin()) { ?>
                    <li<?php CurrentMenu($this->page(),"admin"); ?>><a href="<?php echo $baseurl; ?>admin" class="admin-link" title="Site Admin"><i class='cs-spanner'></i></a></li>
                <?php } ?>
            </ul>
        </div>
    </nav>
</header>
<?php if ($this->page() !== "home") { ?>
<nav class='uk-section uk-section-xsmall cs-section-breadcrumb<?php echo ($this->page() === "products" && isset($this->App)) ? " cs-bgcolour-{$this->App->app_key} uk-light" : " uk-section-muted"; ?>'><div class='uk-container'>
<div class='uk-clearfix'>
    <div class='uk-float-right'>
<?php
if (Session::userIsLoggedIn()) {
    if ($this->page() === "products" && isset($this->App) && $this->App->auth_type === '1' ) {
        echo "<a class='uk-button uk-button-primary uk-button-small' href='{$this->App->launch}'>launch app</a> ";
    } else if ($this->page() === "products" && !empty($this->Subscriptions)) {
        echo "<a class='uk-button uk-button-primary uk-button-small' href='{$baseurl}launch/{$this->App->app_key}' target='{$this->App->app_key}'>launch app</a> ";
    }
    echo "<a class='uk-button uk-button-default uk-button-small' href='{$baseurl}me'>my account</a>";
} else {
    echo "<a class='uk-button uk-button-primary uk-button-small uk-visible@s' href='#login-required' uk-toggle>register / login</a>";
    // echo "<a class='uk-button uk-button-primary uk-button-small' href='{$baseurl}login'>register / login</a>";
}
?>
    </div>
    <div class='uk-float-left'>
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
</div></div></div></nav>
<?php } ?>