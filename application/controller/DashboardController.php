<?php

/**
 * This controller shows an area that's only visible for logged in users (because of Auth::checkAuthentication(); in line 16)
 */
class DashboardController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();

        // this entire controller should only be visible/usable by logged in users, so we put authentication-check here
        Auth::checkAuthentication();

        // only let one use log on at a time
        Auth::checkSessionConcurrency();
    }

    private function dashboardData($current = "") {
        $sub = SubscriptionModel::getUserSubscriptions(Session::get('user_id'));
        $data = array();
        $data["subscription"] = $sub; // My Current Subscription -> Its Associated Tier -> Apps
        $data["token"] = ApiModel::encodeToken(Session::CurrentId());
        $data["selected"] = $current;
        $data["feed"] = "";

        $data["apikey"] = ApiModel::encodeApiToken("avide","docninja");

        if (!empty($current)) {
            // look up feeds and app info stuff
            $data["feed"] = "here's where the app feeds and stuff for <b>$current</b> would go";
        }
        return $data;
    }

    /**
     * This method controls what happens when you move to /dashboard/index in your app.
     */
    public function index() {
        $this->View->render('dashboard/index', self::dashboardData());
    }

    public function app($app_key) {
        $data = self::dashboardData($app_key);
        $this->View->render('dashboard/index', $data);
    }

    public function subscription() {
        $this->View->render('dashboard/subscription');
    }

}
