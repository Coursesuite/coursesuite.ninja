<?php

/**
 * This is the "base controller class". All other "real" controllers extend this class.
 * Whenever a controller is created, we also
 * 1. initialize a session
 * 2. check if the user is not logged in anymore (session timeout) but has a cookie
 */
class Controller
{
    /** @var View View The view object */
    public $View;
    public $ActionName;

    /**
     * Construct the (base) controller. This happens when a real controller is constructed, like in
     * the constructor of IndexController when it says: parent::__construct();
     */
    public function __construct($requires_session = true)
    {
        // always initialize a session
        if ($requires_session) {
            Session::init();

            // check session concurrency
            // Auth::checkSessionConcurrency();

            // user is not logged in but has remember-me-cookie ? then try to login with cookie ("remember me" feature)
            if (!Session::userIsLoggedIn() and Request::cookie('remember_me')) {
                header('location: ' . Config::get('URL') . 'login/loginWithCookie');
            }
        }

        // create a view object to be able to use it inside a controller, like $this->View->render();
        // @param (optional): the name of the constructor class
        $this->View = new View(get_class($this));
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
