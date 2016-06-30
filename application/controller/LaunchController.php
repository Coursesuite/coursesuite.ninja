<?php

/**
 * This controller shows an area that's only visible for logged in users (because of Auth::checkAuthentication(); in line 16)
 */
class LaunchController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();

        // this entire controller should only be visible/usable by logged in users, so we put authentication-check here
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
