<?php
$start = microtime(true);
$meta_description = isset($this->meta_description) ? $this->meta_description : Config::get('DEFAULT_META_DESCRIPTION');
$meta_keywords = isset($this->meta_keywords) ? $this->meta_keywords : Config::get('DEFAULT_META_KEYWORDS');
$meta_title = isset($this->meta_title) ? $this->meta_title : Config::get('DEFAULT_META_TITLE');

$baseurl = Config::get('URL');

if (isset($this->App)) {
    if (!empty($this->App->meta_description)) $meta_description = $this->App->meta_description;
    if (!empty($this->App->meta_keywords)) $meta_keywords = $this->App->meta_keywords;
    if (!empty($this->App->meta_title)) $meta_title = $this->App->meta_title;
}

function CurrentMenu($page, $routes, $classnames = '') {
    $classes = [$classnames];
    if (strpos($routes,$page)!==false) $classes[] = "uk-active";
    echo " class='" . implode(' ', $classes) . "'";
}

$is_mobile_browser = ($this->MobileDetect->isMobile() && !$this->MobileDetect->isTablet());
$body_id = trim(substr(str_replace("/", "_", $_SERVER['REQUEST_URI']),1));
if (empty($body_id)) $body_id = "default";

?><!doctype html><?php include "art.txt" ?><html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $meta_title; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $meta_description; ?>">
    <meta name="keywords" content="<?php echo $meta_keywords; ?>">
    <meta name="author" content="Avide eLearning">

<?php if (false) { ?>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="data:;base64,=">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
<?php } ?>

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:site" content="">
    <meta name="twitter:image" content="https://fonts.coursesuite.ninja/coursesuite-card-meta.jpg">
    <meta property="og:locale" content="en_AU">
    <meta property="og:type" content="website">
    <meta property="og:title" content="CourseSuite">
    <meta property="og:description" content="<?php echo $meta_description; ?>">
    <meta property="og:url" content="https://www.coursesuite.ninja/">
    <meta property="og:site_name" content="<?php echo $meta_title; ?>">
    <meta property="og:image" content="https://fonts.coursesuite.ninja/coursesuite-card-meta.jpg">
    <link rel="shortcut icon" href="/img/coursesuite_logo_discourse_square.png">
    <link rel="apple-touch-icon-precomposed" href="/img/coursesuite_logo_discourse_square.png">
<?php if (0) { ?>
    <link rel="preload" href="https://fonts.gstatic.com/s/montserrat/v12/JTUPjIg1_i6t8kCHKm459WxZYgzz_PZwjimrqw.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="https://fonts.gstatic.com/s/montserrat/v12/JTURjIg1_i6t8kCHKm45_aZA3gnD_vx3rCs.woff2" as="font" type="font/woff2" crossorigin>
<?php } ?>
    <link rel="dns-prefetch" href="https://document.scormification.ninja/">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com/">
    <link rel="dns-prefetch" href="https://d1f8f9xcsvx3ha.cloudfront.net">

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
<?php

    $headerVideo = "/img/header_dark.mp4";
    echo "<style>", AppModel::apps_colours_css(), Config::CustomCss(), "</style>", PHP_EOL; // AppModel::apps_colours_css() also now in less
    if (isset($this->sheets)) {
        foreach ($this->sheets as $sheet) {
            echo "    <link rel='stylesheet' type='text/css' href='$sheet' />". PHP_EOL;
        }
    }
    if (Config::get("debug") === false) {
        echo "<link rel='stylesheet' href='" . APP_CSS . "'>" . PHP_EOL;
        // $headerVideo = KeyStore::find("headerVideo")->get("");
    } else {
        // $headerVideo = "";
        echo "<link rel='stylesheet/less' type='text/css' href='/css/coursesuite.less' />" . PHP_EOL;
        echo "<script>less = { env: 'development', dumpLineNumbers: 'comments', poll: 99999999 }</script>" . PHP_EOL;
        echo "<script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/less.js/2.7.3/less.min.js'></script>" . PHP_EOL;
        echo "<script>less.watch()</script>" . PHP_EOL;
    }
    $google_analytics_id = Config::get('GOOGLE_ANALYTICS_ID');
    if (isset($google_analytics_id) && (!empty($google_analytics_id))) {
        echo "<script>
      		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      		})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

          ga('create', '" . $google_analytics_id . "', 'auto');
          ga('send', 'pageview');
        </script>" . PHP_EOL;
    }

    $blog_badge = "";
    $blog_recent = BlogModel::recent_entry_count();
    if ($blog_recent > 0) {
        $blog_badge = "<span class='cs-blog-badge'>$blog_recent</span>";
    }
    if (false) {
?>
    <!-- Piwik -->
    <script type="text/javascript">
      var _paq = _paq || [];
      /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
      _paq.push(['trackPageView']);
      _paq.push(['enableLinkTracking']);
      (function() {
        var u="//stats.coursesuite.ninja/";
        _paq.push(['setTrackerUrl', u+'piwik.php']);
        _paq.push(['setSiteId', '1']);
        var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
        g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
      })();
    </script>
    <!-- End Piwik Code -->

<?php } ?>
    <script type='text/javascript' src='//platform-api.sharethis.com/js/sharethis.js#property=58ba5cc8535b950011d4059a&product=inline-share-buttons' async='async'></script>
<?php echo KeyStore::find("fastspring_sbl")->get(); ?>
    </head>
<body id="<?php echo $body_id; ?>" class="<?php echo ($is_mobile_browser) ? 'mobile' : 'desktop'; ?>">

<?php
if ($is_mobile_browser) {
include "nav_mobile.php";
} else {
include "nav_desktop.php";
}
?>

    <main><?php
    // if ($this->class_controller_name !== "BlogController") {
    //     echo "<nav id='nav-sections'>";
    // //    echo "<div class='primary-nav'>";
    //     $sections = SectionsModel::getAllStoreSections(true);
    //     foreach ($sections as $section) {
    //         $thisRoute = $section->route;
    //         $is_store = false;
    //         if ($this->class_controller_name === ucwords($thisRoute) . "Controller") {
    //             $is_store = false; // true && (Config::get("debug") === true);
    //             echo "<div class='selected'>";
    //         } else {
    //             echo "<div>";
    //         }
    //         echo "<a href='" . $baseurl . $thisRoute . "'>" . $section->routeLabel . "</a>";
    //         echo "</div>";
    //     }
    //     echo "<div";
    //     if ($this->class_controller_name === "MeController" || $this->class_controller_name === "LoginController") echo " class='selected'";
    //     echo "><a href='${baseurl}me/' class='my-account-link'>My Account</a></div>";
    //     echo "</nav>";
    //  }

    // if the user has any messages that they haven't acknowledged, render them here using some kind of template
//     // this is an example only, it needs to know about the kind of message so it can add a class to the acknowledgement-item box
//     if (isset($this->SystemMessages)) {


// //        echo "<section class='user-acknowledgements'>";
//         foreach ($this->SystemMessages as $message) {
//             switch (intval($message["level"],10)) {
//                 case 0: $message_level = "danger"; break;
//                 case 1: $message_level = "warning"; break;
//                 case 2: $message_level = "success"; break;
//                 case 3: $message_level = "primary"; break;
//             }
//             echo "<div class='uk-alert-$message_level' uk-alert>";
//             echo "<a class='uk-alert-close' uk-close'";
//             if (!isset($message["dismissable"])) echo " data-action='dismiss-message' data-action-id='" . $message["message_id"] . "' title='Dismiss this message'";
//             echo "></a>";
//             echo  Text::toHtml($message["text"]);
//             echo "</div>";
//     	    // echo "<div class='acknowledgement-item level-" . $message["level"] . "'>" .
//     	    // 		"<div class='content-container flex-1'>" . Text::toHtml($message["text"]). "</div>";
//     	    // echo "</div>";
//         }
// //        echo "</section>";
//         // some way of registering a startup script event or handler file
//         // e.g. $this->scripts .= 'acknowledge-ajax.js';
//     }