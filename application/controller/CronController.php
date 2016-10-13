<?php

class CronController extends Controller
{

    // public function __construct()
    // {
    //     parent::__construct();
    // }

    public function index()
    {

        $ssn = Application::php_session();
        $ssn->get_active_sessions(); // internally garbage collects

        // Clean old sessions
        Session::clean();

        // keep subscription active states up to date
        SubscriptionModel::validateSubscriptions();

        // keep track of trial users
        UserModel::trialUserExpire();

        ob_clean();

    }
}
