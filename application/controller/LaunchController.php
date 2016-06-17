<?php
class LaunchController extends Controller {
    public function __construct() {
        parent::__construct();
        Auth::checkAuthentication();
    }

    public function index() {
        Redirect::to("store");
    }

    public function app($appkey = "") {
	    
    }

}
