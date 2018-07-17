    <div id="offcanvas-usage" uk-offcanvas="overlay: true; mode: reveal;">
        <div class="uk-offcanvas-bar cs-mobile-nav">

            <button class="uk-offcanvas-close" type="button" uk-close></button>

            <?php if (!Session::userIsLoggedIn()) { ?>
            <p><a class='uk-button uk-button-default' href='#login-required' uk-toggle>login here</a></p>
            <?php } ?>

            <ul class="uk-nav uk-nav-default uk-margin-small-top">
                <li<?php CurrentMenu($this->page(),"home"); ?>>
                    <a href="<?php echo $baseurl; ?>"><span class="uk-margin-small-right" uk-icon="icon: home"></span> Home</a>
                </li>
                <?php if (Session::userIsLoggedIn()) { ?><li<?php CurrentMenu($this->page(),"me"); ?>
                    <a href="<?php echo $baseurl; ?>me"><span class="uk-margin-small-right" uk-icon="icon: user"></span> My Account</a>
                </li><?php } ?>
                <li<?php CurrentMenu($this->page(),"products", "uk-parent"); ?>>
                    <a href="#"><span class="uk-margin-small-right" uk-icon="icon: cart"></span> Products</a>
                    <ul class="uk-sub-nav uk-margin-left">
                        <?php foreach (SectionsModel::getAllStoreSections(false,true) as $section) { ?>
                         <li class="uk-nav-header"><a href="<?php echo $baseurl; ?>products/<?php echo $section->route; ?>"><?php echo $section->label; ?></a></li>
                            <?php
                            foreach (NavModel::products_nav($section->route) as $item) {
                                echo "<li><a href='{$baseurl}products/{$section->route}/{$item->app_key}' class='uk-text-truncate'>{$item->name}</a></li>" . PHP_EOL;
                            }
                            ?>
                        <?php } ?>
                    </ul>
                </li>
                <li<?php CurrentMenu($this->page(),"pricing"); ?>>
                    <a href="<?php echo $baseurl; ?>pricing/" class='uk-link-reset'><span class="uk-margin-small-right" uk-icon="icon: credit-card"></span> Pricing</a>
                </li>
                <li<?php CurrentMenu($this->page(),"support"); ?>>
                    <a href='<?php echo (Session::userIsLoggedIn()) ? "/me/support/" : "#"; ?>' class='uk-link-reset'><span uk-icon="lifesaver"></span>Support</a>
                    <ul class="uk-sub-nav uk-margin-left">
                        <li><a href="https://help.coursesuite.ninja/">Helpdesk</a></li>
                        <li><a href="https://guide.coursesuite.ninja/">User Guides</a></li>
                        <li><a href="https://www.youtube.com/channel/UCxjmLClwzsyhaBshrZ1FYyA" target="_blank">YouTube channel</a></li>
                        <?php if (Config::get("API_VISIBLE")) { ?><li><a href="/apidoc" target="_blank">API Documentation</a></li><?php } ?>
                    </ul>
                </li>
                <li<?php CurrentMenu($this->page(),"blog,about,testimonials,services"); ?>>
                    <a href="#" class='uk-link-reset'><span class="uk-margin-small-right" uk-icon="icon: world"></span> Company</a>
                    <ul class="uk-sub-nav uk-margin-left">
                        <li><a href="<?php echo $baseurl; ?>blog">Blog<?php echo $blog_badge; ?></a></li>
                        <li><a href="<?php echo $baseurl; ?>content/about">About CourseSuite</a></li>
                        <li><a href="<?php echo $baseurl; ?>content/contact">Contact Us</a></li>
                        <li><a href="https://www.avide.com.au">Avide eLearning</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>