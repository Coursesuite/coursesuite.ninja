<?php

/**
 * Configuration for DEVELOPMENT environment
 * To create another configuration set just copy this file to config.production.php etc. You get the idea :)
 */

/**
 * Configuration for: Error reporting
 * Useful to show every little problem during development, but only show hard / no errors in production.
 * It's a little bit dirty to put this here, but who cares. For development purposes it's totally okay.
 */
error_reporting(E_ALL);
ini_set("display_errors", 1);

/**
 * Configuration for cookie security
 * Quote from PHP manual: Marks the cookie as accessible only through the HTTP protocol. This means that the cookie
 * won't be accessible by scripting languages, such as JavaScript. This setting can effectively help to reduce identity
 * theft through XSS attacks (although it is not supported by all browsers).
 *
 * @see php.net/manual/en/session.configuration.php#ini.session.cookie-httponly
 */
ini_set('session.cookie_httponly', 1);

/**
 * Returns the full configuration.
 * This is used by the core/Config class.
 */
return array(
	/**
	 * Configuration for: Base URL
	 * This detects your URL/IP incl. sub-folder automatically. You can also deactivate auto-detection and provide the
	 * URL manually. This should then look like 'http://192.168.33.44/' ! Note the slash in the end.
	 */
	'URL' => 'http://' . $_SERVER['HTTP_HOST'] . str_replace('public', '', dirname($_SERVER['SCRIPT_NAME'])),
	/**
	 * Configuration for: Folders
	 * Usually there's no reason to change this.
	 */
	'PATH_CONTROLLER' => realpath(dirname(__FILE__).'/../../') . '/application/controller/',
	'PATH_VIEW' => realpath(dirname(__FILE__).'/../../') . '/application/view/',
	/**
	 * Configuration for: Avatar paths
	 * Internal path to save avatars. Make sure this folder is writable. The slash at the end is VERY important!
	 */
	'PATH_AVATARS' => realpath(dirname(__FILE__).'/../../') . '/public/avatars/',
	'PATH_AVATARS_PUBLIC' => 'avatars/',

	'PATH_APP_MEDIA' => realpath(dirname(__FILE__).'/../../') . '/public/img/apps/',

	/**
	 * Configuration for: Default controller and action
	 */
	'DEFAULT_CONTROLLER' => 'store',
	'DEFAULT_ACTION' => 'index',

	/**
	 * Path for PRECOMPILED PHP templates (e.g. from Handlebars)
	 */
	'PATH_VIEW_PRECOMPILED' => realpath(dirname(__FILE__).'/../../') . '/precompiled/',

	/**
	 * Configuration for: Database
	 * DB_TYPE The used database type. Note that other types than "mysql" might break the db construction currently.
	 * DB_HOST The mysql hostname, usually localhost or 127.0.0.1
	 * DB_NAME The database name
	 * DB_USER The username
	 * DB_PASS The password
	 * DB_PORT The mysql port, 3306 by default (?), find out via phpinfo() and look for mysqli.default_port.
	 * DB_CHARSET The charset, necessary for security reasons. Check Database.php class for more info.
	 */
	'DB_TYPE' => 'mysql',
	'DB_HOST' => '127.0.0.1',
	'DB_NAME' => 'frumbert_cshuge',
	'DB_USER' => 'frumbert_cshuge',
	'DB_PASS' => 'qh+7$^kT(~sd',
	'DB_PORT' => '3306',
	'DB_CHARSET' => 'utf8',
	/**
	 * Configuration for: Captcha size
	 * The currently used Captcha generator (https://github.com/Gregwar/Captcha) also runs without giving a size,
	 * so feel free to use ->build(); inside CaptchaModel.
	 */
	'CAPTCHA_WIDTH' => 359,
	'CAPTCHA_HEIGHT' => 100,

	/* google settings */
	'GOOGLE_CAPTCHA_SECRET' => '6LerkRcTAAAAAEUuS9ihNcY5m1cal1atUwl3iPTa',
	'GOOGLE_CAPTCHA_SITEKEY' => '6LerkRcTAAAAAE9yKd7WVCXGDlhhvN_MqGH7vwwe',
	'GOOGLE_ANALYTICS_ID' => null,

	/**
	 * Configuration for: Cookies
	 * 1209600 seconds = 2 weeks
	 * COOKIE_PATH is the path the cookie is valid on, usually "/" to make it valid on the whole domain.
	 * @see http://stackoverflow.com/q/9618217/1114320
	 * @see php.net/manual/en/function.setcookie.php
     *
     * COOKIE_DOMAIN: The domain where the cookie is valid for. Usually this does not work with "localhost",
	 * ".localhost", "127.0.0.1", or ".127.0.0.1". If so, leave it as empty string, false or null.
	 * When using real domains make sure you have a dot (!) in front of the domain, like ".mydomain.com". This is
	 * strange, but explained here:
	 * @see http://stackoverflow.com/questions/2285010/php-setcookie-domain
     * @see http://stackoverflow.com/questions/1134290/cookies-on-localhost-with-explicit-domain
     * @see http://php.net/manual/en/function.setcookie.php#73107
     *
     * COOKIE_SECURE: If the cookie will be transferred through secured connection(SSL). It's highly recommended to set it to true if you have secured connection.
     * COOKIE_HTTP: If set to true, Cookies that can't be accessed by JS - Highly recommended!
     * SESSION_RUNTIME: How long should a session cookie be valid by seconds, 604800 = 1 week.
	 */
	'COOKIE_RUNTIME' => 1209600,
	'COOKIE_PATH' => '/',
    'COOKIE_DOMAIN' => "",
    'COOKIE_SECURE' => false,
    'COOKIE_HTTP' => true,
    'SESSION_RUNTIME' => 604800,
	/**
	 * Configuration for: Avatars/Gravatar support
	 * Set to true if you want to use "Gravatar(s)", a service that automatically gets avatar pictures via using email
	 * addresses of users by requesting images from the gravatar.com API. Set to false to use own locally saved avatars.
	 * AVATAR_SIZE set the pixel size of avatars/gravatars (will be 44x44 by default). Avatars are always squares.
	 * AVATAR_DEFAULT_IMAGE is the default image in public/avatars/
	 */
	'USE_GRAVATAR' => true,
	'GRAVATAR_DEFAULT_IMAGESET' => 'wavatar',
	'GRAVATAR_RATING' => 'pg',
	'AVATAR_SIZE' => 44,
	'AVATAR_JPEG_QUALITY' => 85,
	'AVATAR_DEFAULT_IMAGE' => 'default.jpg',



	// slideshow (app page) default sizes (affects thumbnail generation)
	'SLIDE_PREVIEW_WIDTH' => 459,
	'SLIDE_PREVIEW_HEIGHT' => 344,
	'SLIDE_THUMB_WIDTH' => 120,
	'SLIDE_THUMB_HEIGHT' => 90,


    /**
     * Configuration for: Encryption Keys
     * ENCRYPTION_KEY, HMAC_SALT: Currently used to encrypt and decrypt publicly visible values, like the user id in
	 * the cookie. Change these values for increased security, but don't touch if you have no idea what this means.
     */
    'ENCRYPTION_KEY' => '6#x0gÊìf^25cL1f$08&',
    'HMAC_SALT' => '8qk9c^4L6d#15tM8z7n0%',
	/**
	 * Configuration for: Email server credentials
	 *
	 * Here you can define how you want to send emails.
	 * If you have successfully set up a mail server on your linux server and you know
	 * what you do, then you can skip this section. Otherwise please set EMAIL_USE_SMTP to true
	 * and fill in your SMTP provider account data.
	 *
	 * EMAIL_USED_MAILER: Check Mail class for alternatives
	 * EMAIL_USE_SMTP: Use SMTP or not
	 * EMAIL_SMTP_AUTH: leave this true unless your SMTP service does not need authentication
	 */
	'EMAIL_USED_MAILER' => 'phpmailer', // 'log'
	'EMAIL_USE_SMTP' => true,
	'EMAIL_SMTP_HOST' => 'amethyst.relentlesshosting.com.au', // 'mail.coursesuite.hosting24.com.au',
	'EMAIL_SMTP_AUTH' => true,
	'EMAIL_SMTP_USERNAME' => 'no-reply@coursesuite.frumbert.org', // 'no.user',
	'EMAIL_SMTP_PASSWORD' => 'fJ0b3=#U,ei7', // 'iuh@943^f',
	'EMAIL_SMTP_PORT' => 465,
	'EMAIL_SMTP_ENCRYPTION' => 'tls', // 'ssl',

	/**
	 * Configuration for: Email content data
	 */
	'EMAIL_PASSWORD_RESET_URL' => 'login/verifypasswordreset',
	'EMAIL_PASSWORD_RESET_FROM_EMAIL' => 'no-reply@coursesuite.ninja',
	'EMAIL_PASSWORD_RESET_FROM_NAME' => 'My Coursesuite',
	'EMAIL_PASSWORD_RESET_SUBJECT' => 'Password reset for CourseSuite',
	'EMAIL_PASSWORD_RESET_CONTENT' => 'Please click on this link to reset your password: ',
	'EMAIL_VERIFICATION_URL' => 'register/verify',
	'EMAIL_VERIFICATION_FROM_EMAIL' => 'no-reply@coursesuite.ninja',
	'EMAIL_VERIFICATION_FROM_NAME' => 'CourseSuite',
	'EMAIL_VERIFICATION_SUBJECT' => 'Account activation for CourseSuite',
	'EMAIL_VERIFICATION_CONTENT' => 'Please click on this link to activate your account: ',


	/*
	 * digest authentication users
	 *
	 */

	'DIGEST_USERS' => array(
		'admin' => 'nT5YyYb233WB',
		'apiuser' => 'rpxC5kCmYuFn',
		'tokenuser' => 'GEv6mJ7wJgWR',
		'fastspring' => 'e93NcNdpntFq',
		'discourse' => '3kurpzSGRAq4',
	),
	
	'FASTSPRING_SECRET_KEY' => '263c9f8ead6933f5794bf79dc19e48fc',


	'FORCE_HANDLEBARS_COMPILATION' => true // set to true to force recompilation of handlebars templates on the fly

);
