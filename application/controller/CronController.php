<?php

class CronController extends Controller
{

    // public function __construct()
    // {
    //     parent::__construct();
    // }

    public function index()
    {

        // keep track of trial users
        UserModel::trialUserExpire();

        // keep subscription active states up to date
        SubscriptionModel::validateSubscriptions();

        $ssn = Application::php_session();
        $ssn->get_active_sessions(); // internally garbage collects

        // Clean old sessions
        Session::clean();
        // ob_clean();

    }
}
