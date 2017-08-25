<?php

class LaunchController extends Controller
{

    public function __construct(...$args)
    {

        // if length < 1 then there is a problem
        // when length = 1, we are doing /launch/docninja/ - authenticated
        // when length is 2, we are doing /lanuch/docninja/md5hashvalue/ - maybe unauthenticated
        // if length is > 2 then there is a problem
        // so we only need to check auth when length is 1

        parent::__construct();
        $length = count($args[1]);
        if ($length < 1 || $length > 2) {
            Redirect::home();
        }

        if ($length === 1) {
        	if (AppModel::app_requires_authentication($args[1][0])) {
	            Auth::checkAuthentication();
        	}
        }

        // if ($length === 1 && !Session::CurrentUserId()) {
        //     Auth::checkAuthentication();
        // }
    }

/* this launches apps. do all final checks - security and such. */
    public function index($app_key, $hash = null)
    {

    	// if the app requires some kind of auth
        if (AppModel::app_requires_authentication($app_key)) {

            // need the licencing database to be accurate
            Licence::refresh_licencing_info();

        	// user is logged on, ignore the hash and obey the users purchase
            if ($user_id = Session::CurrentUserId()) {
                if (!SubscriptionModel::user_has_active_subscription_to_app($user_id, $app_key)) {
                    Redirect::to("store/$app_key/invalid/57894/");
                }
                $ref = SubscriptionModel::get_highest_tier_subscription_reference_for_user_for_app($app_key, $user_id);
                $hash = md5($ref);

            // not logged on, and no hash
            } else if (is_null($hash)) {
                Redirect::to("store/$app_key/invalid/10298/");

            // not logged on and hash exists, check that it is still in an active subscription
            } else {
                if (!ApiModel::validate_app_is_in_subscription($hash, $app_key)) {
                    Redirect::to("store/$app_key/invalid/99548/");
                }
            }
        }

        // build the url
        $url = AppModel::getLaunchUrl($app_key, $hash);

        if (!empty($url)) {
            Redirect::external($url);
        } else {
            Redirect::to("500"); // because we don't know what it could be
        }

    }

}
