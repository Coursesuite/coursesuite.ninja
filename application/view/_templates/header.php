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
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $meta_description; ?>">
    <meta name="keywords" content="<?php echo $meta_keywords; ?>">
    <meta name="author" content="Avide eLearning">
    <title><?php echo $meta_title; ?></title>
    <link rel="icon" href="data:;base64,=">
    <!-- link href='//fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css' -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">
    <link href='//r.coursesuite.ninja/mycoursesuite/style.css' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="<?php echo $baseurl; ?>css/style.css" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
<?php
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
?>
    </head>
<body id="<?php echo str_replace("/", "_", $filename); ?>">
    <header>
        <div><a href="<?php echo $baseurl; ?>" class="logo"><img src="http://r.coursesuite.ninja/logo/cs_logo_70px_colour.png"></a></div>
        <div><nav>
        	<a href="<?php echo $baseurl; ?>"><i class='cs-home-filled'></i> Home</a>
        	<a href="http://forum.coursesuite.ninja/categories/" target="_blank">Forum</a>
            <a href="http://help.coursesuite.ninja/" target="_blank">Helpdesk</a>
        <?php if (Session::userIsLoggedIn() && false) { ?>
        	<a href="http://buggr.coursesuite.ninja/" target="_blank" data-tooltip="Found a bug? Log it!">Buggr!</a>
        <?php } ?>
        <?php if (!Session::userIsLoggedIn()) { ?>
            <a href="<?php echo $baseurl; ?>login/"<?php if (View::checkForActiveControllerAndAction($filename, "login/index")) { echo ' class="active" '; } ?>>Login / Register</a>
            <!-- a href="<?php echo $baseurl; ?>register/"<?php if (View::checkForActiveControllerAndAction($filename, "register/index")) { echo ' class="active" '; } ?>>Register</a -->
        <?php } else { ?>
            <a href="<?php echo $baseurl; ?>user/index"<?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?>>My Account</a>
                <?php if (false) { ?><ul class="navigation-submenu">
                    <li <?php if (View::checkForActiveControllerAndAction($filename, "dashboard/subscription")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo $baseurl; ?>dashboard/subscription"><i class="cs-params"></i>My subscription</a>
                    </li>
                    <?php if (false) { ?>
                    <li <?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo $baseurl; ?>user/changeUserRole"><i class="cs-lab"></i>Upgrade account</a>
                    </li>
                    <li <?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo $baseurl; ?>user/editAvatar"><i class="cs-camera"></i>Edit your avatar</a>
                    </li>
                    <li <?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo $baseurl; ?>user/editusername"><i class="cs-tag"></i>Edit my username</a>
                    </li>
                    <?php } ?>
                    <li <?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo $baseurl; ?>user/edituseremail"><i class="cs-mail"></i>Edit my email</a>
                    </li>
                    <li <?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo $baseurl; ?>user/changePassword"><i class="cs-key"></i>Change Password</a>
                    </li>
                    <li class="seperator-top"></li>
                    <li <?php if (View::checkForActiveController($filename, "login")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo $baseurl; ?>login/logout"><i class="cs-lock"></i>Logout</a>
                    </li>
                </ul><?php } ?>
        <?php if (Session::get("user_account_type") == 7) : ?>
            <a href="<?php echo $baseurl; ?>admin/" class="admin-link <?php if (View::checkForActiveController($filename, "admin")) { echo 'active'; } ?>"><i class='cs-spanner'></i> Admin</a>
        <?php endif; ?>
            <a href="<?php echo $baseurl; ?>login/logout">Logout</a>
        <?php } ?>
        </ul></div>
        <?php
            if (!Session::userIsLoggedIn() && KeyStore::find("freetrial")->get()=="true" && !empty(KeyStore::find("freetriallabel")->get())) {
                echo "<a href='{$baseurl}register/index/freeTrial' class='free-trial green-button'>" . KeyStore::find("freetriallabel")->get() . "</a>";
            } elseif (UserModel::getTrialAvailability(intval(Session::get('user_id'))) && KeyStore::find("freetrial")->get()=="true" && !empty(KeyStore::find("freetriallabel")->get())) {
                echo "<a href='{$baseurl}register/registeredUserTrial' class='free-trial green-button'>" . KeyStore::find("freetriallabel")->get() . "</a>";
            }
        ?>
    </header>

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
			    		"<div class='content-container'>" . Text::toHtml($message["text"]). "</div>";
			    if (!isset($message["dismissable"])) echo "<a href='javascript:;' data-action='dismiss-message' data-action-id='" . $message["message_id"] . "' title='Dismiss this message'><i class='cs-cross'></i></a>";
			    echo "</div>";
		    }
		    echo "</section>";
		    // some way of registering a startup script event or handler file
		    // e.g. $this->scripts .= 'acknowledge-ajax.js';
	    }
?>