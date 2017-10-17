<?php

/**
 * This is the "base controller class". All other "real" controllers extend this class.
 * Whenever a controller is created, we also
 * 1. initialize a session
 * 2. check if the user is not logged in anymore (session timeout) but has a cookie
 */
class Controller
{

    public $View;
    public $ControllerName;
    public $ActionName;
    public $Method;

    /**
     * Construct the (base) controller. This happens when a real controller is constructed, like in
     * the constructor of IndexController when it says: parent::__construct();
     */
    public function __construct($requires_session = true, $action_name = "")
    {
        global $PAGE; // a place to store variables for the duration of this controller, such as logon from cookie, a part of the Application

        if ($requires_session) {
            Session::init();
            $PAGE = PageFactory::getFactory(Request::cookie('login'));
        }
        $this->Method = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? "AJAX" : $_SERVER['REQUEST_METHOD'];
        $this->View = new View(get_class($this));
        $this->ActionName = $action_name;
        $this->ControllerName = strtolower(substr(get_class($this), 0, -10)); // LoginController -> login
    }

    public function requiresAuth($key = null) {
        $digest = new \Rakshazi\Digestauth;
        $digestUsers = DigestModel::get_users($key);
        // $valid = $digest->setUsers(Config::get('DIGEST_USERS'))->setRealm("CourseSuite")->enable();
        $valid = $digest->setUsers($digestUsers)->setRealm("CourseSuite")->enable();
        if (!$valid) {
            header('HTTP/1.1 401 Unauthorized');
            die("Digest Authentication Failed");
        }
        return $digest->user;
    }

}
