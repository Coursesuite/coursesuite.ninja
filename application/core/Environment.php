<?php

/**
 * Class Environment
 *
 * Extremely simple way to get the environment, everywhere inside your application.
 */
class Environment {
    public static function get() {
    	// if APPLICATION_ENV constant exists (set in Apache configs)
    	// then return content of APPLICATION_ENV
    	// else return "development"
    	return (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : "development");
    }

    public static function remoteIp() {
            return $_SERVER['REMOTE_ADDR'];
    }

    public static function NinjaValidator() {
	    return (isset($_SERVER["HTTP_X_NINJAVALIDATOR"]));
    }

    // in vhost VirtualHost
    // SetEnv DOMAIN_SUFFIX ".dev"
    public static function suffix() {
        return (getenv('DOMAIN_SUFFIX') ? getenv('DOMAIN_SUFFIX') : "");
    }

}
