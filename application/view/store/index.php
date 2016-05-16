<div class="container">
    <h1>CourseSuite Store</h1>
    <div class="box store">

<?php

use LightnCandy\LightnCandy;

$template = "
    {{#each section}}
    {{#if visible}}<section class='{{cssclass}}'>
        {{#if label}}<h3>{{label}}</h3>{{/if}}
        {{#if epiphet}}<h4>{{epiphet}}</h4>{{/if}}
        {{#if html}}{{{html}}}{{/if}}
        <nav class='app-section-names'>
            {{#each apps}}
            <div class='tile app'>
                <figure>
                    <img src='{{icon}}'>
                    <figcaption>{{name}}</figcaption>
                </figure>
                <div class='information'>
                    {{tagline}}
                </div>
                <div class='actions'>
                    <a href='{{storeurl}}'>More info</a>
                    {{#if ../../token}}<a href='{{launch}}?token={{../../token}}' target='_blank' class='launch'>Launch</a>{{/if}}
                </div>
            </div>
            {{/each}}
        </nav>
    </section>{{/if}}
    {{/each}}
";

/* $phpStr = LightnCandy::compile($template, array(
  "flags" => LightnCandy::FLAG_PARENT, //  | LightnCandy::FLAG_STANDALONEPHP | LightnCandy::FLAG_ERROR_LOG,
  "helpers" => array(
      "eq" => function ($arg1, $arg2) {
        return strcasecmp((string)$arg1, (string)$arg2);
      }
  ),
  "debug" => FALSE,
)); */

$section = array();
foreach ($this->sections as $sect) {
    $apps = array();
    foreach ($sect->apps as $app) {
        $apps[] = array(
            "icon" => $app->icon,
            "name" => $app->name,
            "tagline" => "(tagline)",
            "storeurl" => Config::get('URL') . "index/" . $app->app_key,
            "launch" => $app->launch,
        );
    }
    $section[] = array(
        "visible" => $sect->visible,
        "cssclass" => $sect->cssclass,
        "label" => $sect->label,
        "epiphet" => $sect->epiphet,
        "html" => $sect->html,
        "apps" => $apps,
    );
}

// file_put_contents('../precompiled/view-index.php', '<?php ' . $phpStr . '? >');
$renderer = include('../precompiled/view-index.php');

//$renderer = LightnCandy::prepare($phpStr);

echo $renderer(array(
    "section" => $section,
    "token" => isset($this->token) ? $this->token : false
));

    foreach ($this->sections as $section) {
        if ($section->visible == 1) {
            echo "<section class='$section->cssclass'>";
            if (!empty($section->label)) echo "<h3>$section->label</h3>";
            if (!empty($section->epiphet)) echo "<h4>$section->epiphet</h4>";
            if (!empty($section->html)) echo $section->html;
            if (count((array)$section->apps) > 0) {
                echo "<nav class='app-section-items'>";
                foreach ($section->apps as $app) {
                    echo "<figure>";
                        echo "<a href='" . Config::get('URL') . "index/" . $app->app_key . "'>";
                            echo "<img src='" . $app->icon . "'>";
                        echo "</a>";
                        echo "<figcaption>";
                            echo "<span class='name'>" . $app->name . "</span>";
                            if ($this->upgrade) {
                                echo "<a href='" . Config::get('URL') . "index/" . $app->app_key . "/upgrade/' class='upgrade-button'>Upgrade plan</a>";
                            }
                            if (isset($this->token)) {
                                echo "<a href='" . $app->launch . "?token=" . $this->token ."' target='_blank' class='launch-button'>Launch</a>";
                            }
                        echo "</figcaption>";
                    echo "</figure>";
                }
                echo "</nav>";
            }
            echo "</section>\n";
        }
    }
?>

    </div>
</div>
