<?php

/**
 * Class Redirect
 *
 * Simple abstraction for redirecting the user to a certain page
 */
class Redirect
{

	// reload THIS page
	public static function here($meta = false) {
		if ($meta === true) {
			die("<meta http-equiv='refresh' content='0' />");
		} else {
			header('Location: '.$_SERVER['REQUEST_URI']);
			die();
		}
	}

	// to the home page
	public static function home($kind = "location")
	{
		$home = Config::get('URL');
		switch ($kind) {
			case "meta":
				die("<meta http-equiv=\"refresh\" content=\"0;URL='{$home}'\" />");
				break;

			case "refresh":
				header("Refresh: 0;url='{$home}'");
				break;

			default:
				header("Location: {$home}");
		}
		die();
	}

	// go TO a page that is on this site
	public static function to($path, $kind = "location")
	{
		if (0===strpos($path,"/")) {
			$path = substr($path,1);
		}
		$url = Config::get('URL') . $path;
		switch ($kind) {
			case "meta":
				die("<meta http-equiv=\"refresh\" content=\"0;URL='{$url}'\" />");
				break;

			case "refresh":
				header("Refresh: 0;url='{$url}'");
				break;

			default:
				header("Location: {$url}");
		}
		die();
	}

	// go to an EXTERNAL page, typically an app link
	public static function external($url)
	{
		// may have an apache vhost environment set to shim in a developer domain, i.e. ".dev"
		$sfx = Environment::suffix();
		if (!empty($sfx)) {
			if (strpos($url, ".ninja/app/") !== false) {
				$url = str_replace(".ninja/app/", ".ninja$sfx/app/", $url);
				$url = str_replace("https://", "http://", $url);
			}
		}
		header("location: " . $url);
		die();
	}

	// redirect to the store page for a given app key (may not know the best route)
	public static function app($key, $append = "") {
		$url = StoreProductsModel::find_store_route_for_app($key);
		header("location: " . $url . $append);
		die();
	}
}
