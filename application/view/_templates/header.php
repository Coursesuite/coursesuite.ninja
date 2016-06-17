<?php
$start = microtime(true);
$meta_description = Config::get('DEFAULT_META_DESCRIPTION');
$meta_keywords = Config::get('DEFAULT_META_KEYWORDS');
$meta_title = Config::get('DEFAULT_META_TITLE');

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
    <link href='//fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="<?php echo Config::get('URL'); ?>css/style.css" />
<?php
if (isset($this->sheets)) {
    foreach ($this->sheets as $sheet) {
	    if (strpos($sheet, "//") === false) {
		    echo "    <link rel='stylesheet' type='text/css' href='" . Config::get('URL') . "css/$sheet' />" . PHP_EOL;
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
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', '" . $google_analytics_id . "', 'auto');
      ga('send', 'pageview');

    </script>" . PHP_EOL;
}
?>
    </head>
<body id="<?php echo str_replace("/", "_", $filename); ?>">
    <header>
        <div><a href="<?php echo Config::get('URL'); ?>" class="logo"><img src="<?php echo Config::get('URL'); ?>img/cs_logo_70px_colour.png"></a></div>
        <div><nav>
        	<a href="<?php echo Config::get('URL'); ?>">Home</a>
        	<a href="http://forum.coursesuite.ninja/categories/" target="_blank">Forum</a>
        	<a href="http://buggr.coursesuite.ninja/" target="_bnlank">Buggr!</a>
        <?php if (!Session::userIsLoggedIn()) { ?>
            <a href="<?php echo Config::get('URL'); ?>login/"<?php if (View::checkForActiveControllerAndAction($filename, "login/index")) { echo ' class="active" '; } ?>>Login / Register</a>
            <!-- a href="<?php echo Config::get('URL'); ?>register/"<?php if (View::checkForActiveControllerAndAction($filename, "register/index")) { echo ' class="active" '; } ?>>Register</a -->
        <?php } else { ?>
            <a href="<?php echo Config::get('URL'); ?>user/index"<?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?>>My Account</a>
                <?php if (false) { ?><ul class="navigation-submenu">
                    <li <?php if (View::checkForActiveControllerAndAction($filename, "dashboard/subscription")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>dashboard/subscription"><i class="cs-params"></i>My subscription</a>
                    </li>
                    <?php if (false) { ?>
                    <li <?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>user/changeUserRole"><i class="cs-lab"></i>Upgrade account</a>
                    </li>
                    <li <?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>user/editAvatar"><i class="cs-camera"></i>Edit your avatar</a>
                    </li>
                    <li <?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>user/editusername"><i class="cs-tag"></i>Edit my username</a>
                    </li>
                    <?php } ?>
                    <li <?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>user/edituseremail"><i class="cs-mail"></i>Edit my email</a>
                    </li>
                    <li <?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>user/changePassword"><i class="cs-key"></i>Change Password</a>
                    </li>
                    <li class="seperator-top"></li>
                    <li <?php if (View::checkForActiveController($filename, "login")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>login/logout"><i class="cs-lock"></i>Logout</a>
                    </li>
                </ul><?php } ?>
        <?php if (Session::get("user_account_type") == 7) : ?>
            <a href="<?php echo Config::get('URL'); ?>admin/"<?php if (View::checkForActiveController($filename, "admin")) { echo ' class="active" '; } ?>>Admin</a>
        <?php endif; ?>
            <a href="<?php echo Config::get('URL'); ?>login/logout">Logout</a>
        <?php } ?>
        </ul></div>
    </header>

    <main><?php
	    
	    // if the user has any messages that they haven't acknowledged, render them here using some kind of template
	    // this is an example only, it needs to know about the kind of message so it can add a class to the acknowledgement-item box
	    if (isset($this->messages)) {
		    echo "<section class='user-acknowledgements'";
		    foreach ($this->messages as $message) {
			    echo "<div class='acknowledgement-item'><p class='body'>$message</p><a href='javascript:;'><i class='cs-circle-cross'></i></a></div>";
		    }
		    echo "</section>";
		    // some way of registering a startup script event or handler file
		    // e.g. $this->scripts .= 'acknowledge-ajax.js';
	    }
	    
?>