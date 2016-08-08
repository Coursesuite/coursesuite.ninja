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
	public static function to($path) {
		// $to = array_map('trim', $path);
		header("location: " . Config::get('URL') . $path);
	}

	public static function external($url) {
		$rd = "location: " . $url;
		header($rd);
	}
}