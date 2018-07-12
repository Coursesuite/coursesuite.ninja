<?php
$start = microtime(true);
$meta_description = isset($this->meta_description) ? $this->meta_description : Config::get('DEFAULT_META_DESCRIPTION');
$meta_keywords = isset($this->meta_keywords) ? $this->meta_keywords : Config::get('DEFAULT_META_KEYWORDS');
$meta_title = isset($this->meta_title) ? $this->meta_title : Config::get('DEFAULT_META_TITLE');
$meta_image = "https://fonts.coursesuite.ninja/coursesuite-card-meta.jpg";

$baseurl = Config::get('URL');

if (isset($this->App)) {
    if (!empty($this->App->meta_description)) $meta_description = $this->App->meta_description;
    if (!empty($this->App->meta_keywords)) $meta_keywords = $this->App->meta_keywords;
    if (!empty($this->App->meta_title)) $meta_title = $this->App->meta_title;
    if (!empty($this->App->icon)) $meta_image = Image::get_external_url($this->App->icon);
}

$og_title = isset($this->card_title) ? $this->card_title : $meta_title;
$og_description = isset($this->card_description) ? $this->card_description : $meta_description;
$og_image = isset($this->card_icon) ? Image::get_external_url($this->card_icon) : $meta_image;

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

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="<?php echo $og_image; ?>">
    <meta name="twitter:description" content="<?php echo $og_description; ?>">
    <meta name="twitter:title" content="<?php echo $meta_title; ?>">

    <meta property="og:locale" content="en_AU">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo $meta_title; ?>">
    <meta property="og:url" content="<?php echo Config::get('URL'), substr($_SERVER['REQUEST_URI'],1); ?>">
    <meta property="og:title" content="<?php echo $og_title; ?>">
    <meta property="og:description" content="<?php echo $og_description; ?>">
    <meta property="og:image" content="<?php echo $og_image; ?>">

    <link rel="shortcut icon" href="/img/coursesuite_logo_discourse_square.png">
    <link rel="apple-touch-icon-precomposed" href="/img/coursesuite_logo_discourse_square.png">
<?php if (0) { ?>
    <link rel="preload" href="https://fonts.gstatic.com/s/montserrat/v12/JTUPjIg1_i6t8kCHKm459WxZYgzz_PZwjimrqw.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="https://fonts.gstatic.com/s/montserrat/v12/JTURjIg1_i6t8kCHKm45_aZA3gnD_vx3rCs.woff2" as="font" type="font/woff2" crossorigin>
<?php } ?>
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com/">
    <link rel="dns-prefetch" href="https://d1f8f9xcsvx3ha.cloudfront.net">
    <link rel="dns-prefetch" href="https://platform-api.sharethis.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
<?php

    $headerVideo = "/img/header_dark.mp4";
    if (isset($this->sheets)) {
        foreach ($this->sheets as $sheet) {
            echo "    <link rel='stylesheet' type='text/css' href='$sheet' />". PHP_EOL;
        }
    }
    if (Config::get("debug") === false) {
        echo "    <link rel='stylesheet' href='" . APP_CSS . "'>" . PHP_EOL;
        // $headerVideo = KeyStore::find("headerVideo")->get("");
    } else {
        // $headerVideo = "";
        echo "<link rel='stylesheet/less' type='text/css' href='/css/coursesuite.less' />" . PHP_EOL;
        echo "<script>less = { env: 'development', dumpLineNumbers: 'comments', poll: 99999999 }</script>" . PHP_EOL;
        echo "<script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/less.js/2.7.3/less.min.js'></script>" . PHP_EOL;
        echo "<script>less.watch()</script>" . PHP_EOL;
    }
    echo "<style>", Config::CustomCss(true), "</style>", PHP_EOL; // after other styles
    $blog_badge = "";
    $blog_recent = BlogModel::recent_entry_count();
    if ($blog_recent > 0) {
        $blog_badge = "<span class='cs-blog-badge'>$blog_recent</span>";
    }
    echo KeyStore::find("fastspring_sbl")->get(), PHP_EOL;
    echo KeyStore::find("head_javascript")->get(), PHP_EOL;
?>
    <script type='text/javascript' src='//platform-api.sharethis.com/js/sharethis.js#property=58ba5cc8535b950011d4059a&product=inline-share-buttons' async='async'></script>
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