<?php

/**
 * Class Redirect
 *
 * Simple abstraction for redirecting the user to a certain page
 */
class Redirect
{


    /**
     * To the homepage
     */
    public static function home()
    {
        header("location: " . Config::get('URL'));
    }

    /**
     * To the defined page
     *
     * @param $path - param array in order of precedence - if empty or not set, go to the next one
     */
    public static function to($path)
    {
        // die("location: " . Config::get('URL') . $path);
        header("location: " . Config::get('URL') . $path);
    }

    public static function external($url)
    {
        // may have an apache vhost environment set to shim in a developer domain, i.e. ".dev" or ".local"
        $sfx = Environment::suffix();
        if (!empty($sfx)) {
            if (strpos($url, ".ninja/app/") !== false) {
                $url = str_replace(".ninja/app/", ".ninja$sfx/app/", $url);
            }
        }
        // die("location: $url");
        header("location: " . $url);
    }
}
