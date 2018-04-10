<?php

class Response {

	public static function cookie($name, $value) {
		if (is_null($value)) {
			setcookie($name, false, 1); // , Config::get('COOKIE_PATH'), Config::get('COOKIE_DOMAIN'), Config::get('COOKIE_SECURE'), Config::get('COOKIE_HTTP'));
		} else {
			// strtotime( '+30 days' )
			setcookie($name, $value, time() + Config::get('COOKIE_RUNTIME'), Config::get('COOKIE_PATH'), "." . Config::get('COOKIE_DOMAIN'), Config::get('COOKIE_SECURE'), Config::get('COOKIE_HTTP'));
		}
	}
}