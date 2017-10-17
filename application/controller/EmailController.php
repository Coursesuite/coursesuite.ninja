<?php
/*
controller for receiving unauthenticated link actions from emails
*/
class EmailController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    // user has clicked the change email verification from their new email address
    public function verifyChange($hash = "") {
        global $PAGE;
        $account = UserModel::getAccountByHash($hash, "change");
        if (!is_null($account)) {
            $model = $account->get_model();
            $model["user_email"] = $model["user_email_update"];
            $model["user_email_update"] = null;
            $model["change_verification_hash"] = null;
            $model["user_active"] = 1;
            $account->set_model($model);
            $account->save();
            if (!Session::userIsLoggedIn()) {
                Auth::set_user_logon_cookie($model->user_id);
            }
            Redirect::to("me/");
        }
        Redirect::to("/");
    }

}