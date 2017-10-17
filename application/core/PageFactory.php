<?php

// a storage are for variables that last the lifetime of the page but no longer. Such as a user object from the remember me cookie

class PageFactory {
    private static $factory;

    public static function getFactory($cookie_value) {
        if (!self::$factory) {
            self::$factory = new PageFactory($cookie_value);
        }
        return self::$factory;
    }

    function __construct($cookie_value = "") {
    	if (isset($cookie_value) && !empty($cookie_value) && preg_match('/^[a-fA-F0-9]{32}$/', $cookie_value)) {
    		$model = Model::Read("users","user_remember_me_token=:hash", array(":hash" => $cookie_value))[0];
    		foreach (get_object_vars($model) as $key => $value) {
    			$this->$key = is_null($value) ? '' : is_numeric($value) ? (int) $value : (string) $value;
    		}
    	}
    	return $this;
    }

    public function get($name) {
    	return (isset($this->name) && isset($this->$name)) ? $this->$name : null;
    }

    public function set($name, $value) {
    	$this->$name = $value;
    }

    public function remove($name) {
    	if (isset($this->$name)) unset($this->$name);
    }

}

// PageFactory::getFactory(Request::cookies('login'))->get("user_email");

// $session = PageFactory::getFactory(Request::cookies('login'));
// $session->get("user_email");
// $session->set("non_persistent_value", "foo");
// $session->remove("non_persistent_value");

