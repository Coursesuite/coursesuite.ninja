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
	    if (empty($appkey)) {
		    Redirect::to("404");
	    }
	    $url = AppModel::getLaunchUrl($appkey);
	    Redirect::external($url);
    }

}
