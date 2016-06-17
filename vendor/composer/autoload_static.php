<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit11dbfb6a54a44f9def62d52ba2467c6e
{
    public static $files = array (
        'e40631d46120a9c38ea139981f8dab26' => __DIR__ . '/..' . '/ircmaxell/password-compat/lib/password.php',
    );

    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'ReCaptcha\\' => 10,
            'Rakshazi\\' => 9,
        ),
        'L' => 
        array (
            'LightnCandy\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'ReCaptcha\\' => 
        array (
            0 => __DIR__ . '/..' . '/google/recaptcha/src/ReCaptcha',
        ),
        'Rakshazi\\' => 
        array (
            0 => __DIR__ . '/..' . '/rakshazi/digestauth/Rakshazi',
        ),
        'LightnCandy\\' => 
        array (
            0 => __DIR__ . '/..' . '/zordius/lightncandy/src',
        ),
    );

    public static $fallbackDirsPsr4 = array (
        0 => __DIR__ . '/../..' . '/application/core',
        1 => __DIR__ . '/../..' . '/application/model',
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'ParsedownExtra' => 
            array (
                0 => __DIR__ . '/..' . '/erusev/parsedown-extra',
            ),
            'Parsedown' => 
            array (
                0 => __DIR__ . '/..' . '/erusev/parsedown',
            ),
        ),
        'C' => 
        array (
            'ColorThief' => 
            array (
                0 => __DIR__ . '/..' . '/ksubileau/color-thief-php/lib',
            ),
        ),
    );

    public static $classMap = array (
        'EasyPeasyICS' => __DIR__ . '/..' . '/phpmailer/phpmailer/extras/EasyPeasyICS.php',
        'PHPMailer' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.phpmailer.php',
        'PHPMailerOAuth' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.phpmaileroauth.php',
        'PHPMailerOAuthGoogle' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.phpmaileroauthgoogle.php',
        'POP3' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.pop3.php',
        'SMTP' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.smtp.php',
        'ntlm_sasl_client_class' => __DIR__ . '/..' . '/phpmailer/phpmailer/extras/ntlm_sasl_client.php',
        'phpmailerException' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.phpmailer.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit11dbfb6a54a44f9def62d52ba2467c6e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit11dbfb6a54a44f9def62d52ba2467c6e::$prefixDirsPsr4;
            $loader->fallbackDirsPsr4 = ComposerStaticInit11dbfb6a54a44f9def62d52ba2467c6e::$fallbackDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit11dbfb6a54a44f9def62d52ba2467c6e::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit11dbfb6a54a44f9def62d52ba2467c6e::$classMap;

        }, null, ClassLoader::class);
    }
}
