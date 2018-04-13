<?php

/**
 * Session class
 *
 * handles the session stuff. creates session when no one exists, sets and gets values, and closes the session
 * properly (=logout). Not to forget the check if the user is logged in or not.
 */
class Session
{

	/**
	 * starts the session
	 */
	public static function init()
	{
		// if no session exist, start the session
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	}

	public static function CurrentId()
	{
		return session_id();
	}

	/**
	 * sets a specific value to a specific key of the session
	 *
	 * @param mixed $key key
	 * @param mixed $value value
	 */
	public static function set($key, $value)
	{
		$_SESSION[$key] = $value;
	}

	public static function remove($key)
	{
		if (isset($_SESSION[$key])) {
			unset($_SESSION[$key]);
		}
	}
	/**
	 * gets/returns the value of a specific key of the session
	 *
	 * @param mixed $key Usually a string, right ?
	 * @return mixed the key's value or nothing
	 */
	public static function get($key, $default = null)
	{
		if (isset($_SESSION[$key])) {
			$value = $_SESSION[$key];
			// filter the value for XSS vulnerabilities
			$value = Filter::XSSFilter($value);
			return $value;
		} else {
			return $default;
		}
	}

	/**
	 * adds a value as a new array element to the key.
	 * useful for collecting error messages etc
	 *
	 * @param mixed $key
	 * @param mixed $value
	 */
	public static function add($key, $value)
	{
		$ar = $_SESSION[$key];
		if (is_array($ar)) {
			if (!in_array($value, $ar)) {
				// TIM: don't re-add duplicate values
				$_SESSION[$key][] = $value;
			}
		} else {
			$_SESSION[$key][] = $value;
		}
	}

	/**
	 * deletes the session (= logs the user out)
	 */
	public static function destroy()
	{
		session_destroy();
	}

	public static function reset() {
		if (session_status() == PHP_SESSION_NONE) {
        	session_start();
    	}
        session_unset();
        session_destroy();
        session_write_close();
        setcookie(session_name(),'',0,'/');
        session_regenerate_id(true);
	}

	public static function userIsLoggedIn()
	{
		global $PAGE;
		return (isset($PAGE->user_id));
	}

	public static function CurrentUsername() {
		global $PAGE;
		return (isset($PAGE->user_id)) ? $PAGE->user_email : "";
	}

	public static function CurrentUserId()
	{
		global $PAGE;
		if (isset($PAGE->user_id)) {
			return $PAGE->user_id;
		}
		return 0;
	}

	public static function userIsAdmin()
	{
		global $PAGE;
		return (isset($PAGE->user_account_type) && $PAGE->user_account_type === (int) Config::get('ADMIN_ACCOUNT_LEVEL'));
	}

}
