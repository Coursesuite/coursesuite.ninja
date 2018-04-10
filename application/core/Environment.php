<?php

/**
 * Class Environment
 *
 * Extremely simple way to get the environment, everywhere inside your application.
 */
class Environment
{
    public static function get()
    {
        // if APPLICATION_ENV constant exists (set in Apache configs)
        // then return content of APPLICATION_ENV
        // else return "development"
        if ($_SERVER['HTTP_HOST'] === "dev.coursesuite.ninja") {
            return "preprod";
        }
        return (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : "development");
    }

    public static function remoteIp()
    {
        return getenv('HTTP_CLIENT_IP')?:
                getenv('HTTP_X_FORWARDED_FOR')?:
                getenv('HTTP_X_FORWARDED')?:
                getenv('HTTP_FORWARDED_FOR')?:
                getenv('HTTP_FORWARDED')?:
                getenv('REMOTE_ADDR');
        // return $_SERVER['REMOTE_ADDR'];
    }

    public static function NinjaValidator()
    {
        return (isset($_SERVER["HTTP_X_NINJAVALIDATOR"]));
    }

    // in vhost VirtualHost
    // SetEnv DOMAIN_SUFFIX ".dev"
    public static function suffix()
    {
        return (getenv('DOMAIN_SUFFIX') ? getenv('DOMAIN_SUFFIX') : "");
    }

}
