<?php $start = microtime(true); ?><!doctype html>
<html>
<head>
    <title>My CourseSuite</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <link rel="icon" href="data:;base64,=">

    <link href='//fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="<?php echo Config::get('URL'); ?>css/style.css" />

    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      // ga('create', 'UA-61936859-1', 'auto');
      // ga('send', 'pageview');

    </script>
    </head>
<body>

    <header>
        <a href="<?php echo Config::get('URL'); ?>" class="logo"><img src="<?php echo Config::get('URL'); ?>img/coursesuite.png"></a>
        <?php if (Session::userIsLoggedIn()) : ?>
        <ul class="navigation">
            <li <?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?> >
                <a href="<?php echo Config::get('URL'); ?>user/index"><i class="cs-user"></i>My Account</a>
                <ul class="navigation-submenu">
                    <li <?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>user/changeUserRole"><i class="cs-lab"></i>Upgrade account</a>
                    </li>
                    <li <?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>user/editAvatar"><i class="cs-camera"></i>Edit your avatar</a>
                    </li>
                    <li <?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>user/editusername"><i class="cs-tag"></i>Edit my username</a>
                    </li>
                    <li <?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>user/edituseremail"><i class="cs-mail"></i>Edit my email</a>
                    </li>
                    <li <?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>user/changePassword"><i class="cs-key"></i>Change Password</a>
                    </li>
                    <li <?php if (View::checkForActiveController($filename, "login")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>login/logout"><i class="cs-lock"></i>Logout</a>
                    </li>
                </ul>
            </li>
            <?php if (Session::get("user_account_type") == 7) : ?>
                <li <?php if (View::checkForActiveController($filename, "admin")) { echo ' class="active" '; } ?> >
                    <a href="<?php echo Config::get('URL'); ?>admin/"><i class="cs-lab"></i>Admin</a>
                </li>
            <?php endif; ?>
        </ul>
        <?php endif; ?>
    </header>

    <section>
        <nav id="routes">
            <ul class="navigation">
                <?php if (Session::userIsLoggedIn()) { ?>
                    <li <?php if (View::checkForActiveControllerAndAction($filename, "dashboard/index")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>dashboard/"><i class="cs-pen"></i>Apps</a>
                    </li>
                    <li <?php if (View::checkForActiveControllerAndAction($filename, "dashboard/subscription")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>dashboard/subscription"><i class="cs-banknote"></i>Subscription</a>
                    </li>
                    <li <?php if (View::checkForActiveController($filename, "note")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>note/"><i class="cs-heart"></i>Notes</a>
                    </li>
                    <li class="seperator"><span/></li>
                    <li <?php if (View::checkForActiveController($filename, "news")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>news/"><i class="cs-news"></i>News</a>
                    </li>
                    <li <?php if (View::checkForActiveController($filename, "support")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>support/"><i class="cs-bubble"></i>Support</a>
                    </li>

                <?php } else { ?>
                    <!-- for not logged in users -->
                    <li <?php if (View::checkForActiveControllerAndAction($filename, "login/index")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>login/"><i class="cs-lock"></i>Login</a>
                    </li>
                    <li <?php if (View::checkForActiveControllerAndAction($filename, "register/index")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>register/"><i class="cs-like"></i>Register</a>
                    </li>
                <?php } ?>
                <?php if (Session::get("user_account_type") == 7) : ?>
                    <li class="seperator"><span/></li>
                    <li <?php if (View::checkForActiveController($filename, "profile")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>profile/"><i class="cs-user"></i>User Admin</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <article>