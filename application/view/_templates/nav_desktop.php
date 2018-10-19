<?php if (false) { ?>
<header class="uk-position-relative">
        <div class="uk-cover-container cs-nav-height cs-nav-bg">
        <video autoplay loop muted playsinline uk-cover>
            <source src="<?php echo $headerVideo; ?>" type="video/mp4">
            <img src="/img/header-preview.jpg">
        </video>
        <img src="/img/header-preview.jpg" uk-cover>
        <canvas id="zodiac" width="100%" height="118"></canvas>
    </div>
    <nav class="uk-navbar-container uk-navbar-transparent cs-navbar-container uk-position-top uk-light" uk-navbar>
<?php } ?>
<header>
    <nav class="uk-navbar-container uk-navbar-transparent" uk-navbar>
        <div class="uk-navbar-left">
            <a href="<?php echo $baseurl; ?>" class="uk-navbar-item uk-logo">
                <img src="<?php echo $baseurl; ?>img/coursesuite.svg" uk-svg width="300" height="53">
            </a>
        </div>
        <div class="uk-navbar-right">
            <ul class="uk-navbar-nav">
                <?php if ($this->page() !== "root") { ?>
                <li<?php CurrentMenu($this->page(),"root", "uk-visible@m"); ?>><a href="<?php echo $baseurl; ?>"><span uk-icon="home" class='uk-margin-xsmall-right'></span>Home</a></li>
                <?php } ?>
                <li<?php CurrentMenu($this->page(),"products"); ?>>
                    <a href="<?php echo $baseurl; ?>products/"><span uk-icon="thumbnails" class='uk-margin-xsmall-right'></span>Products</a>
<?php
// do some caching of the rendered html to avoid these db lookups, which are also cached
if (!is_null($cached_html = NavModel::products_dropdown_get())) {
    echo $cached_html;
} else {
    ob_start();
?>
                    <div class="uk-width-xlarge" uk-dropdown="offset: -20; delay-show: 200; mode: hover; animation: uk-animation-slide-top-small; duration: 200">
                        <?php $sections = SectionsModel::getAllStoreSections(false,true); ?>
                        <div class="uk-dropdown-grid uk-child-width-1-2 cs-count-<?php echo count($sections); ?>@m" uk-grid>
                            <?php foreach ($sections as $section) { ?>
                            <div>
                                <ul class="uk-nav uk-dropdown-nav">
                                    <li class="uk-nav-header"><a href="<?php echo $baseurl; ?>products/<?php echo $section->route; ?>" class="uk-link-reset"><span uk-icon="album" class="uk-margin-small-right"></span><?php echo $section->label; ?></a></li>
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
<?php
    $cached_html = ob_get_clean();
    NavModel::products_dropdown_set($cached_html);
    echo $cached_html;
}
?>
                </li>
                <li<?php CurrentMenu($this->page(),"pricing"); ?>><a href="<?php echo $baseurl; ?>pricing/"><span uk-icon="cart" class='uk-margin-xsmall-right'></span>Pricing</a></li>
                <li<?php CurrentMenu($this->page(),"support"); ?>>
                    <a href='<?php echo (Session::userIsLoggedIn()) ? "/me/support/" : "#"; ?>'><span uk-icon="lifesaver" class='uk-margin-xsmall-right'></span>Support</a>
                    <div class="uk-navbar-dropdown" uk-dropdown="offset: -20; delay-show: 200; mode: click, hover; animation: uk-animation-slide-top-small; duration: 200">
                        <ul class="uk-nav uk-navbar-dropdown-nav">
                            <li><a href="https://help.coursesuite.ninja/" target="_blank">Helpdesk</a></li>
                            <li><a href="https://guide.coursesuite.ninja/" target="_blank">User Guides</a></li>
                            <li><a href="https://www.youtube.com/channel/UCxjmLClwzsyhaBshrZ1FYyA" target="_blank">YouTube channel</a></li>
                            <?php if (Config::get("API_VISIBLE")) { ?><li><a href="/apidoc" target="_blank">API Documentation</a></li><?php } ?>
                        </ul>
                    </div>
                </li>
                <li<?php CurrentMenu($this->page(),"blog"); ?>><a href="<?php echo $baseurl; ?>blog"><span uk-icon="comments" class='uk-margin-xsmall-right'></span>Blog<?php echo $blog_badge; ?></a></li>
                <li<?php CurrentMenu($this->page(),"about,testimonials,services"); ?>>
                    <a href="#"><span uk-icon="world" class='uk-margin-xsmall-right'></span>Company</a>
                    <div class="uk-navbar-dropdown" uk-dropdown="offset: -20; delay-show: 200; mode: click, hover; animation: uk-animation-slide-top-small; duration: 200">
                        <ul class="uk-nav uk-navbar-dropdown-nav">
                            <li><a href="<?php echo $baseurl; ?>content/about">About CourseSuite</a></li>
                            <li><a href="<?php echo $baseurl; ?>content/contact">Contact Us</a></li>
                            <li><a href="https://www.avide.com.au" target="_blank">Avide eLearning</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
            <div class="uk-navbar-item">
                <?php
                // if (Session::userIsAdmin()) {
                //     echo "<li", CurrentMenu($this->page(),"admin", '', true), "><a href='", $baseurl, "admin' class='admin-link' title='Site administration'><i class='cs-spanner'></i></a></li>";
                // }
                if (Session::userIsLoggedIn()) {
                    echo "<a class='uk-button uk-button-primary uk-button-small' href='{$baseurl}me'><span uk-icon='user' class='uk-margin-xsmall-right'></span>my account</a>";
                } else {
                    echo "<a class='uk-button uk-button-primary uk-button-small' href='#login-required' uk-toggle><span uk-icon='lock' class='uk-margin-xsmall-right'></span>login here</a>";
                }
                ?>
            </div>
        </div>
    </nav>
</header>
<?php if ($this->page() !== "root") { ?>
<nav class='uk-section uk-section-xsmall cs-section-breadcrumb<?php echo ($this->page() === "products" && isset($this->App)) ? " cs-bgcolour-{$this->App->app_key} uk-light" : " uk-section-muted"; ?>'><div class='uk-container'>
<div class='uk-clearfix'>
    <div class='uk-float-right'>
<?php

if (Session::userIsLoggedIn()) {
    if ($this->page() === "products" && isset($this->App) && intval($this->App->auth_type,10) === 1 ) {
        echo "<a class='uk-button uk-button-primary uk-button-small' href='{$this->App->launch}'>launch app</a> ";
    } else if ($this->page() === "products" && !empty($this->Subscriptions)) {
        echo "<a class='uk-button uk-button-primary uk-button-small' href='{$baseurl}launch/{$this->App->app_key}' target='{$this->App->app_key}'><span uk-icon='bolt' class='uk-margin-xsmall-right'></span>launch app</a> ";
    }
//    echo "<a class='uk-button uk-button-default uk-button-small' href='{$baseurl}me'><span uk-icon='user' class='uk-margin-xsmall-right'></span>my account</a>";
//} else if (Config::get("FASTSPRING_CONTEXTUAL_STORE")) {
//    echo "<a class='uk-button uk-button-primary uk-button-small uk-visible@s' href='#login-required' uk-toggle><span uk-icon='lock' class='uk-margin-xsmall-right'></span>login here</a>";
//} else {
//    echo "<a class='uk-button uk-button-primary uk-button-small uk-visible@s' href='#login-required' uk-toggle><span uk-icon='lock' class='uk-margin-xsmall-right'></span>register / login</a>";
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