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

    /**
     * This method controls what happens when you move to /dashboard/index in your app.
     */
    public function index()
    {

        $sub = SubscriptionModel::getUserSubscriptions(Session::get('user_id'));
        $data = array();
        $data["subscription"] = $sub; // My Current Subscription -> Its Associated Tier -> Apps
        $data["token"] = ApiModel::encodeToken(Session::CurrentId());

        // you could do a multiview, but a view typically has its own wrapper element, so it's easier than having multiple stub views
        // $this->View->renderMulti(Array('dashboard/myapps','dashboard/index'), $data);

        $this->View->render('dashboard/index', $data);
    }

    public function subscription()
    {
        $this->View->render('dashboard/subscription');
    }

    // self::app_data
    // private function app_data() { }

}
