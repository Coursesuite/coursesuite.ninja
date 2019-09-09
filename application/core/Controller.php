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
    public $Ajax = false;

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

        $this->Ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
        $this->Method = strtoupper($_SERVER['REQUEST_METHOD']);
        $this->View = new View(get_class($this), strtolower(substr(get_class($this), 0, -10)), $action_name);
        $this->ActionName = $action_name;
        $this->ControllerName = strtolower(substr(get_class($this), 0, -10)); // LoginController -> login

        // scripts and styles required by all pages should go here
        if ($this->ControllerName === "admin") {

            $this->View->Requires("https://cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js");
            $this->View->Requires("https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.css");
            $this->View->Requires("https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid-theme.min.css");
            $this->View->Requires("https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.js");

            // $this->View->Requires("bower_components/blueimp-file-upload/js/vendor/jquery.ui.widget.js");
            // $this->View->Requires("bower_components/blueimp-file-upload/js/jquery.iframe-transport.js");
            // $this->View->Requires("bower_components/blueimp-file-upload/js/jquery.fileupload.js");
            // $this->View->Requires("bower_components/cloudinary-jquery-file-upload/cloudinary-jquery-file-upload.js");

            // $this->View->Requires("//widget.cloudinary.com/global/all.js");

            $this->View->Requires("/js/simplemde/simplemde.min.css");
            $this->View->Requires("/js/simplemde/simplemde.min.js");
            $this->View->Requires("/js/inline-attachment/inline-attachment.js");
            $this->View->Requires("/js/inline-attachment/codemirror.inline-attachment.js");
            $this->View->Requires("markdown.js");
            if (Config::get("debug") === false) {
                $this->View->Requires(ADMIN_CSS);
            }

            $this->View->Requires("filedrop.js");
            $this->View->Requires("admin/menubar");

            $this->View->Requires("admin.js");

        } else {
            $this->View->Requires("https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.6/css/uikit.css");
            $this->View->Requires("https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.6/js/uikit.js");
            $this->View->Requires("https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.6/js/uikit-icons.js");
            $this->View->Requires("main.js");
            $this->View->Requires("/api/apps_colours_css", null, "css");
        }
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

private function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
}

    public function requiresBearer() {
		$h = $this->getAuthorizationHeader();
        if (!isset($h)) return false;
        $bearer = str_replace('Bearer: ', '', $h);
        if (!preg_match('/^[a-f0-9]{32}$/', $bearer)) return false;
        $id = SubscriptionModel::get_subscription_id_for_hash($bearer);
        if ($id === 0) die("Bearer Token was missing or invalid");
        return $id;
    }

    public function allowCORS() {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-Requested-With");
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: POST, OPTIONS");
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            exit(0);
        }
    }

    public function requiresAjax() {
        // if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        if ($this->Ajax === true) {
            return; // all good
        }
        header('HTTP/1.1 405 Method Not Allowed');
        die("Method Not Allowed");
    }

    public function requiresPost() {
        if ($this->Method === "POST") {
            return true;
        }
        header('HTTP/1.1 405 Method Not Allowed');
        die("Method Not Allowed");
    }

    public function requiresGet() {
        if ($this->Method === "GET") {
            return true;
        }
        header('HTTP/1.1 405 Method Not Allowed');
        die("Method Not Allowed");
    }

    public function requiresDesktop() {
        $md = new Mobile_Detect;
        if ($md->isMobile() || $md->isTablet()) {
            header("HTTP/1.1 418 I'm a teapot");
            die("I'm a <i>desktop</i> teapot.");
        }
    }
}
