<?php

// error_reporting(E_ALL);
// ini_set("display_errors", 1);
ini_set('session.cookie_httponly', 1);

return array(
    'URL' => (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('public', '', dirname($_SERVER['SCRIPT_NAME'])),
    'PATH_CONTROLLER' => realpath(dirname(__FILE__) . '/../../') . '/application/controller/',
    'PATH_VIEW' => realpath(dirname(__FILE__) . '/../../') . '/application/view/',
    'PATH_AVATARS' => realpath(dirname(__FILE__) . '/../../') . '/public/avatars/',
    'PATH_AVATARS_PUBLIC' => 'avatars/',
    'PATH_APP_MEDIA' => realpath(dirname(__FILE__) . '/../../') . '/public/img/apps/',
    'PATH_IMG_MEDIA' => realpath(dirname(__FILE__) . '/../../') . '/public/img/',
    'PATH_ATTACHMENTS' => realpath(dirname(__FILE__) . '/../../') . '/public/files/',
    'PATH_CSS_ROOT' => realpath(dirname(__FILE__) . '/../../') . '/public/css/',

    'DEFAULT_CONTROLLER' => 'home',
    'DEFAULT_ACTION' => 'index',

    'DEFAULT_META_TITLE' => 'CourseSuite (Tim Config)',
    'DEFAULT_META_KEYWORDS' => 'coursesuite, coursesuite ninja, lms, online learning, html5 scorm, scorm package, scorm author, interactive learning, document conversion, courseware, moodle',
    'DEFAULT_META_DESCRIPTION' => 'CourseSuite is a suite of online web apps allowing rapid creation of interactive and intuitive HTML5-based SCORM courses.',
    'DEFAULT_META_COPYRIGHT' => '&copy; <a href="http://coursesuite.ninja">CourseSuite</a> 2016-3016',
    'PATH_VIEW_PRECOMPILED' => realpath(dirname(__FILE__) . '/../../') . '/precompiled/',
    'DB_TYPE' => 'mysql',
    'DB_HOST' => '127.0.0.1',
    'DB_NAME' => 'cs_preprod',
    'DB_USER' => 'cs_preprod',
    'DB_PASS' => 'RTX2VQ0}Ny',
    'DB_PORT' => '3306',
    'DB_CHARSET' => 'utf8',

    /**
    * Configuration for: Redis
    */
    'REDIS_HOST' => '127.0.0.1',
    'REDIS_PORT' => '6379',
    'REDIS_PASS' => null,
    'REDIS_PREFIX' => 'NinjaSuiteDev:',

    /**
    * Licencing server WebSocket
    */
    'WEBSOCKET_SCHEMA' => "ws://",
    'WEBSOCKET_HOST' => '127.0.0.1',
    'WEBSOCKET_PORT' => '9000',
    'WEBSOCKET_LAYER' => "window.onbeforeunload = function(){ Layer.onclose=function(){}; Layer.close(); }; Layer.onmessage = function(event) { var msg = JSON.parse(event.data); switch (msg.command) { case 'open':case 'close':location.href = App.Home; window.close(); break; default: console.log(msg); }}",
    'WEBSOCKET_LAYER_MINIFIED' => "eval(function(p,a,c,k,e,d){while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+c.toString(a)+'\\b','g'),k[c])}}return p}('9.a=b(1){8 0=7.4(1.6);c(0.d){3\"j\":3\"2\":l.i=h.e;f.2();m;g:k.5(0)}}',23,23,'msg|event|close|case|parse|log|data|JSON|var|Layer|onmessage|function|switch|command|Home|window|default|App|href|open|console|location|break'.split('|')))",


    'CAPTCHA_WIDTH' => 359,
    'CAPTCHA_HEIGHT' => 100,

    'GOOGLE_CAPTCHA_SECRET' => '6LerkRcTAAAAAEUuS9ihNcY5m1cal1atUwl3iPTa',
    'GOOGLE_CAPTCHA_SITEKEY' => '6LerkRcTAAAAAE9yKd7WVCXGDlhhvN_MqGH7vwwe',
    'GOOGLE_ANALYTICS_ID' => null,

    /**
     */
    'COOKIE_RUNTIME' => 1209600, // 2 weeks
    'COOKIE_PATH' => '/',
    'COOKIE_DOMAIN' => $_SERVER['HTTP_HOST'],
    'COOKIE_SECURE' => isset($_SERVER['HTTPS']) ? 1 : 0,
    'COOKIE_HTTP' => true,
    'SESSION_RUNTIME' => 604800, // 1 week
    'USE_GRAVATAR' => true,
    'GRAVATAR_DEFAULT_IMAGESET' => 'wavatar',
    'GRAVATAR_RATING' => 'pg',
    'AVATAR_SIZE' => 44,
    'AVATAR_JPEG_QUALITY' => 85,
    'AVATAR_DEFAULT_IMAGE' => 'default.jpg',
    'SLIDE_PREVIEW_WIDTH' => 400,
    'SLIDE_PREVIEW_HEIGHT' => 300,
    'SLIDE_THUMB_WIDTH' => 120,
    'SLIDE_THUMB_HEIGHT' => 90,
    'ENCRYPTION_KEY' => 'gQTwyP#HpXWS%5cWjMT#yUzT!UAN',
    'HMAC_SALT' => 'SP27uVe^zujNW9apqg#kaqA(BAx',

    'EMAIL_USED_MAILER' => 'phpmailer',
    'EMAIL_USE_SMTP' => false, // because I can't make true work because of some wierd cerificate error
    'EMAIL_SMTP_HOST' => 'mail.coursesuite.com.au',
    'EMAIL_SMTP_AUTH' => true,
    'EMAIL_SMTP_USERNAME' => 'no.user',
    'EMAIL_SMTP_PASSWORD' => 'iuh@943^f',
    'EMAIL_SMTP_PORT' => 465, // false=25, tls=587, // ssl=465
    'EMAIL_SMTP_ENCRYPTION' => false, // 'tls', // 'ssl',

    'EMAIL_PASSWORD_RESET_URL' => 'login/verifypasswordreset',
    'EMAIL_PASSWORD_RESET_FROM_EMAIL' => 'no-reply@coursesuite.ninja',
    'EMAIL_PASSWORD_RESET_FROM_NAME' => 'My Coursesuite',
    'EMAIL_PASSWORD_RESET_SUBJECT' => 'Password reset for CourseSuite',
    'EMAIL_VERIFICATION_URL' => 'register/verify',
    'EMAIL_VERIFICATION_FROM_EMAIL' => 'no-reply@coursesuite.ninja',
    'EMAIL_VERIFICATION_FROM_NAME' => 'CourseSuite',
    'EMAIL_VERIFICATION_SUBJECT' => 'Account activation for CourseSuite',
    'EMAIL_ADMIN' => 'admin@coursesuite.ninja',
    'EMAIL_SUBSCRIPTION' => 'info@coursesuite.com.au',
    'EMAIL_STANDARD_FROM_NAME' => 'CourseSuite Notifications',
    'EMAIL_STANDARD_FROM_ADDR' => 'info@coursesuite.com.au',
    'EMAIL_STANDARD_NO_REPLY' => 'no-reply@coursesuite.ninja',
    'DIGEST_USERS' => array(
        'admin' => 'nT5YyYb233WB',
        'apiuser' => 'rpxC5kCmYuFn',
        'tokenuser' => 'GEv6mJ7wJgWR',
        'fastspring' => 'e93NcNdpntFq',
        'discourse' => '3kurpzSGRAq4',
    ),
    'DISCOURSE_SSO_SECRET' => 'supersecretkeythatnoonewilleverknow',
    'CLOUDCONVERT_API_KEY' => '8pxT0DHRE5lpcVzildrPoEbztL9rc5Es89xG0incUfPNB93LLZueEr7zTK7PTuZmcV1hXkRMITbhjS-U1NnnzQ',
    'FASTSPRING_SECRET_KEY' => '263c9f8ead6933f5794bf79dc19e48fc',
    'FASTSPRING_PARAM_APPEND' => '&mode=test',
    'FASTSPRING_STORE' => 'coursesuite',
    'FASTSPRING_API_USER' => 'admin@coursesuite.ninja',
    'FASTSPRING_API_PASSWORD' => 'I9AN2on42Tom',
    'STORE_INFO_SHOW_PRICING' => true,
    'GLOBAL_FOOTER_COLUMN_1' => '<h3>About Us</h3><p>We are a small Australian team focused on developing a suite of great online web apps that allow rapid creation of interactive and intuitive HTML5-based SCORM courses. We also release Open Source plugins for Moodle, and produce online course content.</p>',
    'GLOBAL_FOOTER_COLUMN_2' => '<h3>More information</h3><p><a href="/content/privacy">Privacy</a> <a href="http://forum.coursesuite.ninja/categories/">Forum</a> <a href="http://sites.fastspring.com/coursesuite/order/contact">Contact Us</a> <a href="http://avide.com.au/">Avide eLearning</a></p>',
    'GLOBAL_FOOTER_COLUMN_3' => '<h3>all rights reserved</h3><p>&copy; CourseSuite 2016-3016<br/><a href="mailto:&#105;&#110;&#102;&#111;&#64;&#99;&#111;&#117;&#114;&#115;&#101;&#115;&#117;&#105;&#116;&#101;&#46;&#99;&#111;&#109;&#46;&#97;&#117;">&#105;&#110;&#102;&#111;&#64;&#99;&#111;&#117;&#114;&#115;&#101;&#115;&#117;&#105;&#116;&#101;&#46;&#99;&#111;&#109;&#46;&#97;&#117;</a></p>',
    'MAILCHIMP_API_KEY' => '0803286b6f9c681c80d7ad751d6beec3-us11',
    'MAILCHIMP_LIST_ID' => '08344979e7', // '2f5671f29b',
    'MAILCHIMP_INTEREST_ID' => 'd3a65e22a1',

    'FORCE_HANDLEBARS_COMPILATION' => true,

    'ADMIN_ACCOUNT_LEVEL' => 7,
    'ADMIN_ACCOUNT_EMAIL' => '%@coursesuite.com.au',

    'ADMIN_TOOLS' => array(
        "staticPage" => array("label" => "Edit static pages", "icon" => "cs-static-pages", "active" => true),
        "allUsers" => array("label" => "List / Search users", "icon" => "cs-users", "active" => true),
        "editSections" => array("label" => "Edit store sections", "icon" => "cs-store-sections", "active" => true),
        "editApps" => array("label" => "Edit apps", "icon" => "cs-apps", "active" => true),
        "assignApps" => array("label" => "Assign apps to store sections", "icon" => "cs-flag", "active" => false),
        "editAllProducts" => array("label" => "Edit subscription products", "icon" => "cs-products", "active" => false),
        "editTiers" => array("label" => "Edit tiers", "icon" => "cs-tiers", "active" => false),
        "manualSubscribe" => array("label" => "Manually manage subscriptions", "icon" => "cs-switch", "active" => false),
        "messages" => array("label" => "Notifications", "icon" => "cs-notifications", "active" => true),
        "editAppTierMatrix" => array("label" => "Edit app-tier matrix", "icon" => "cs-config", "active" => false),
        "manageHooks" => array("label" => "3rd party hooks / endpoints", "icon" => "cs-config", "active" => true),
        "storeSettings" => array("label" => "Misc store settings", "icon" => "cs-cog", "active" => true),
        "mailTemplates" => array("label" => "Edit mail templates", "icon" => "cs-mail", "active" => true),
        "whiteLabelling" => array("label" => "White Labelling via API", "icon" => "cs-settings", "active" => false),
        "editBundles" => array("label" => "Edit product bundles", "icon" => "cs-apps", "active" => false),
        "subscribers" => array("label" => "Paid Subscribers", "icon" => "fa fa-credit-card icon-hilight", "active" => false),
        "showLog" => array("label" => "System Log", "icon" => "fa fa-scroll", "active" => true),
        "editProducts" => array("label" => "Edit Products", "icon" => "fa fa-truck", "active" => true),
        "changeLog" => array("label" => "App Changelog", "icon" => "fa fa-list-alt", "active" => true),
    ),

    'API_TRIAL_PRODUCT_ID' => 'api-trial',
    'OST_APIKEY' => '5FC2364AEFA6F6BC8B34463E71B1F4F1', // dev.coursesuite.ninja key
    'OST_SECRET_SALT' => 'jZMglLS5w9ARCNzfdUIt7bWK=O0erJ1g',
);
