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
        if (session_id() == '') {
            session_start();
        }
    }

    public static function CurrentId() {
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
    public static function get($key)
    {
        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];
        	// filter the value for XSS vulnerabilities
        	return Filter::XSSFilter($value);
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
		    if (!in_array($value, $ar)) { // TIM: don't re-add duplicate values
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
    
	/*
	 * Clean up expired or invalid sessions
	 * (Called from cron.php)
	 */    
    public static function clean() {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->query("delete from session_data where hash = 'f628356cee6cf4cf5249828feed7fcb3'");
		$query = $database->query("update users set session_id = null where session_id in (select session_id from session_data where session_expire < current_timestamp)");
		$query = $database->query("update users set session_id = null where session_id not in (select session_id from session_data)");

    }

    /**
     * update session id in database
     *
     * @access public
     * @static static method
     * @param  string $userId
     * @param  string $sessionId
     * @return string
     */
    public static function updateSessionId($userId, $sessionId = null){

        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "UPDATE users SET session_id = :session_id WHERE user_id = :user_id";

        $query = $database->prepare($sql);
        $query->execute(array(':session_id' => $sessionId, ":user_id" => $userId));

    }

    /**
     * checks for session concurrency
     *
     * This is done as the following:
     * UserA logs in with his session id('123') and it will be stored in the database.
     * Then, UserB logs in also using the same email and password of UserA from another PC,
     * and also store the session id('456') in the database
     *
     * Now, Whenever UserA performs any action,
     * You then check the session_id() against the last one stored in the database('456'),
     * If they don't match then log both of them out.
     *
     * @access public
     * @static static method
     * @return bool
     * @see Session::updateSessionId()
     * @see http://stackoverflow.com/questions/6126285/php-stop-concurrent-user-logins
     */
    public static function isConcurrentSessionExists(){

        $session_id = session_id();
        $userId     = Session::get('user_id');

        if(isset($userId) && isset($session_id)){

            $database = DatabaseFactory::getFactory()->getConnection();
            $sql = "SELECT session_id FROM users WHERE user_id = :user_id LIMIT 1";

            $query = $database->prepare($sql);
            $query->execute(array(":user_id" => $userId));

            $result = $query->fetch();
            $userSessionId = !empty($result) ? $result->session_id: null;

            return $session_id !== $userSessionId;
        }

        return false;
    }

    /**
     * Checks a session id to see if it exists in the database
     *
     * Typically called by an external site which is checking to see if the user is logged on (without neccesarily knowing who it is)
     *
     * @access public
     * @static static method
     * @return bool
     * @see Session::userIsLoggedIn()
     */
    public static function isActiveSession($session_id, $app_id = NULL) {
        if (isset($session_id)) {
            $database = DatabaseFactory::getFactory()->getConnection();
            $sql = "SELECT count(1) FROM session_data WHERE session_id = :sid AND session_expire < current_timestamp";
            $query = $database->prepare($sql);
            $query->execute(array(":sid" => $session_id));
            return ($query->fetchColumn() > 0);
        }
        return false;
    }

	// equivalent to http://php.net/manual/en/function.session-decode.php except doesn't populate $_SESSION - we can pluck results
	private static function unserialize_session($session_data, $start_index=0, &$dict=null) {
	   isset($dict) or $dict = array();
	   $name_end = strpos($session_data, "|", $start_index); // standard delimeter
	   if ($name_end !== FALSE) {
	       $name = substr($session_data, $start_index, $name_end - $start_index);
	       $rest = substr($session_data, $name_end + 1);
	
	       $value = unserialize($rest);      // PHP will unserialize up to "|" delimiter.
	       $dict[$name] = $value;
	
	       return self::unserialize_session($session_data, $name_end + 1 + strlen(serialize($value)), $dict);
	   }
	
	   return $dict;
	}

	// decode the data that is stored in [session_data].`session_data` - as an array
    private static function SessionData($session_id, $asObject = false) {
            $database = DatabaseFactory::getFactory()->getConnection();
            $query = $database->prepare("SELECT session_data FROM session_data WHERE session_id = :sid");
            $query->execute(array(":sid" => $session_id));
            $data = $query->fetchColumn();
            return (true == $asObject) ? (object) self::unserialize_session($data) : self::unserialize_session($data);
    }
    
    // return the userid that is stored in session
    public static function CurrentUserId() {
	     return Session::get('user_id');
    }

	// return the userid that is stored BY session in the database (but not neccesarily in $_SESSION)
    public static function UserIdFromSession($session_id) {
        if (isset($session_id)) {
	        $data = self::SessionData($session_id);
	        return (isset($data["user_id"])) ? $data["user_id"] : -1;
	    }
    }

	// return the useful parts of a user record that is stored BY session in the database (but not neccesarily in $_SESSION)
    public static function UserDetailsFromSession($session_id) {
        $obj = (object) array("user_id" => -1, "user_name" => "", "user_email" => "", "user_account_type" => -1); // new stdClass();
        if (isset($session_id)) {
	        $data = self::SessionData($session_id, true);
	        if (isset($data->user_logged_in)) { // we assume the rest is also built
		        $obj->user_id = $data->user_id;
			    $obj->user_name = $data->user_name;
				$obj->user_email = $data->user_email;
				$obj->account_type = $data->user_account_type;
	        }
        }
        return $obj;
    }

    /**
     * Checks if the user is logged in or not
     *
     * @return bool user's login status
     */
    public static function userIsLoggedIn()
    {
        return (self::get('user_logged_in') ? true : false);
    }
}
