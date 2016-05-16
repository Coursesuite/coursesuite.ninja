<?php $start = microtime(true); ?><!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="CourseSuite is a suite of online web apps allowing rapid creation of interactive and intuitive HTML5-based SCORM courses.">
    <meta name="author" content="Avide eLearning">


    <title>My CourseSuite</title>
    <link rel="icon" href="data:;base64,=">

	<!-- link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" -->
	<!-- link rel="stylesheet" href="https://code.getmdl.io/1.1.3/material.indigo-pink.min.css" -->
	<!-- script defer src="https://code.getmdl.io/1.1.3/material.min.js"></script -->

    <link href='//fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="<?php echo Config::get('URL'); ?>css/style.css" />
<?php
if (isset($this->sheets)) {
    foreach ($this->sheets as $sheet) {
	    echo "    <link rel='stylesheet' type='text/css' href='" . Config::get('URL') . "css/$sheet' />" . PHP_EOL;
    }
}
$google_analytics_id = Config::get('GOOGLE_ANALYTICS_ID');
if (isset($google_analytics_id)) {
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
<body>
    <header>
        <div><a href="<?php echo Config::get('URL'); ?>" class="logo"><img src="<?php echo Config::get('URL'); ?>img/cs_logo_70px_colour.png"></a></div>
        <div><nav>
        	<a href="<?php echo Config::get('URL'); ?>"><i class='cs-shop'></i></a>
        	<a href="http://forum.coursesuite.ninja" target="_blank">Forum</a>
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

    <main>