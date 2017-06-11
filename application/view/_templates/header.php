<?php
$start = microtime(true);
$meta_description = Config::get('DEFAULT_META_DESCRIPTION');
$meta_keywords = Config::get('DEFAULT_META_KEYWORDS');
$meta_title = Config::get('DEFAULT_META_TITLE');

$baseurl = Config::get('URL');

if (isset($this->App->meta_description) && !empty($this->App->meta_description)) { $meta_description = $this->App->meta_description; }
if (isset($this->App->meta_keywords) && !empty($this->App->meta_keywords)) { $meta_keywords = $this->App->meta_keywords; }
if (isset($this->App->meta_title) && !empty($this->App->meta_title)) { $meta_title = $this->App->meta_title; }
?><!doctype html>
<html class="no-touch">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $meta_description; ?>">
    <meta name="keywords" content="<?php echo $meta_keywords; ?>">
    <meta name="author" content="Avide eLearning">
    <title><?php echo $meta_title; ?></title>
    <link rel="icon" href="data:;base64,=">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300|Open+Sans|Open+Sans+Condensed:300" rel="stylesheet">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mb.YTPlayer/3.0.12/jquery.mb.YTPlayer.min.js"></script>
    <link rel="stylesheet" href="<?php echo $baseurl; ?>css/jquery.mb.YTPlayer/jquery.mb.YTPlayer.min.css">
<?php if (Config::get("debug") === false) { ?>
    <link rel="stylesheet" href="<?php echo $baseurl; ?>css/compiled.css">
<?php } else { ?>
    <link rel="stylesheet/less" type="text/css" href="<?php echo $baseurl; ?>css/less/styles.less">
    <script src="//cdnjs.cloudflare.com/ajax/libs/less.js/2.7.2/less.min.js"></script>
    <script>window.less || document.write('<script src="<?=Config::get('URL')?>js/less.min.js"><\/script>')</script>
<?php } ?>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon"><?php
if (class_exists("AdminController")) {
    echo "<link rel='stylesheet' href='" . $baseurl . "css/admin.css' />";
}
if (isset($this->sheets)) {
    foreach ($this->sheets as $sheet) {
	    if (strpos($sheet, "//") === false) {
		    echo "    <link rel='stylesheet' type='text/css' href='" . $baseurl . "css/$sheet' />" . PHP_EOL;
	    } else {
		    echo "    <link rel='stylesheet' type='text/css' href='$sheet' />". PHP_EOL;
		}
    }
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
$headerVideo = KeyStore::find("headerVideo")->get("");
if (class_exists("BlogController")) {
    $headerVideo = "https://www.youtube.com/watch?v=9cheCJhoa_A";
}
// if (Config::get("debug") == true) {
//     $headerVideo = "https://www.youtube.com/watch?v=L_5pV4PJV4c";
// }

$headerVideo = "";

$blog_badge = "";
$blog_recent = BlogModel::recent_entry_count();
if ($blog_recent > 0) {
    $blog_badge = "<span class='badge'>$blog_recent</span>";
}
?>
    <script type='text/javascript' src='//platform-api.sharethis.com/js/sharethis.js#property=58ba5cc8535b950011d4059a&product=inline-share-buttons' async='async'></script>
    </head>
<body id="<?php echo str_replace("/", "_", $filename); ?>">
    <header>
        <div><a href="<?php echo $baseurl; ?>" class="logo"><img src="<?php echo $baseurl; ?>img/coursesuite.svg" width="300" height="53"></a></div>
        <div><nav>
        	<a href="<?php echo $baseurl; ?>" class="home">Home</a>
        	<a href="https://forum.coursesuite.ninja/" target="_blank">Forum</a>
            <a href="https://help.coursesuite.ninja/" target="_blank">Helpdesk</a>
            <a href="https://guide.coursesuite.ninja/" target="_blank">Guides</a>
            <a href="<?php echo $baseurl; ?>blog">Blog<?php echo $blog_badge; ?></a>
            <a href="<?php echo $baseurl; ?>me/"<?php if (View::checkForActiveController($filename, "account")) { echo ' class="active" '; } ?>>My Account</a>
            <?php if (Session::userIsLoggedIn()) { ?>
            <?php if (Session::get("user_account_type") == 7) : ?>
            <a href="<?php echo $baseurl; ?>admin/" class="admin-link <?php if (View::checkForActiveController($filename, "admin")) { echo 'active'; } ?>"><i class='cs-spanner'></i> Admin</a>
            <?php endif; ?>
            <a href="<?php echo $baseurl; ?>login/logout">Logout</a>
            <?php } ?>
        </nav></div>
    </header>
<?php if (!empty($headerVideo)) { ?>
    <!-- <?php echo KeyStore::find("headerVideoAttribution")->get(); ?> -->
    <div id="bgndVideo" class="player" data-property= "{videoURL:'<?php echo $headerVideo; ?>',containment:'body>header', showControls:false, autoPlay:true, loop:true,vol:1, mute:true, startAt:0, opacity:1, addRaster:false, quality:'default', showYTLogo: false, stopMovieOnBlur: true, opacity: 1}"></div>
<?php } ?>


    <main><?php

    $cc_logout = Session::get("concurrency_logout");
    if (isset($cc_logout) && !empty($cc_logout)) {
        Session::remove("concurrency_logout");
        if (!isset($this->SystemMessages)) $this->SystemMessages = array();
        $this->SystemMessages[] = array(
    	    "level" => 0,
    	    "text" => $cc_logout,
    	    "dismissable" => false
        );
    }

    // if the user has any messages that they haven't acknowledged, render them here using some kind of template
    // this is an example only, it needs to know about the kind of message so it can add a class to the acknowledgement-item box
    if (isset($this->SystemMessages)) {
        echo "<section class='user-acknowledgements'>";
        foreach ($this->SystemMessages as $message) {
    	    echo "<div class='acknowledgement-item level-" . $message["level"] . "'>" .
    	    		"<div class='content-container flex-1'>" . Text::toHtml($message["text"]). "</div>";
    	    if (!isset($message["dismissable"])) echo "<a href='javascript:;' data-action='dismiss-message' data-action-id='" . $message["message_id"] . "' title='Dismiss this message'><i class='cs-cross'></i></a>";
    	    echo "</div>";
        }
        echo "</section>";
        // some way of registering a startup script event or handler file
        // e.g. $this->scripts .= 'acknowledge-ajax.js';
    }
?>