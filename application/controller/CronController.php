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

        // kills off junk accounts
        RegistrationModel::cleanup_unverified_new_accounts_after_a_while();

        // start garbage collection
        $ssn = Application::php_session();
        $ssn->get_active_sessions();

        // Clean old sessions
        Session::clean();
        // ob_clean();

    }
}
