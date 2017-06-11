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
                LoginModel::loginWithAccount($account);
            }
            Redirect::to("me/");
        }
        Redirect::to("/");
    }

    // user clicks an account activation action in an email
    public function validate($hash = "") {
        $email = "";
        if (RegistrationModel::validate_user_activation_hash_and_reset_condition($hash, $email)) {
            MessageModel::notify_user("Your account is now active, and we logged you on automatically!");
            Redirect::to("/");
        } else {
            Session::add('feedback_negative', 'Sorry, that activation hash did not work.');
            Redirect::to("login/");
        }
    }

    // user clicks a password reset confirmation from an email
    public function confirmResetPassword($hash = "") {
        $account = UserModel::getAccountByHash($hash, "reset");
        if (is_null($account)) {
            $status = "invalid";
        } else {
            $model = $account->get_model();
            date_default_timezone_set('UTC');
            if ((new DateTime('@' . $model["user_password_reset_timestamp"]))->getTimestamp() > strtotime("-30 minutes")) {
                $password = Text::generatePassword();
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $model["user_password_hash"] = $password_hash;
                $status = "sent";

                $mail_sent = (new Mail)->sendPassword($model["user_email"], $password, Config::get('URL') . "login/");

            } else {
                $status = "expired";
            }
            $model["user_password_reset_timestamp"] = null;
            $model["user_password_reset_hash"] = null;
            $account->set_model($model);
            $account->save();
            LoginModel::logout();
        }
        Redirect::to("email/passwordReset/" . $status . "/");
    }

    // RENDER the notification page that occurs after a password action (starts on a /me/ page)
    public function passwordReset($status) {
        if ($status == "sent") {
            $model = array(
                "message" => "Your new password is on its way",
                "status" => $status,
            );
            $this->View->renderHandlebars("login/status", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
        } else if ($status == "expired") {
            $model = array(
                "message" => "Sorry, the link has expired. Please log on and try again.",
                "status" => $status,
            );
            $this->View->renderHandlebars("login/status", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
        } else if ($status == "invalid") {
            $model = array(
                "message" => "Sorry, the reset hash was not valid or has expired. Please log on and try again.",
                "status" => $status,
            );
            $this->View->renderHandlebars("login/status", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
        } else {
            Redirect::to("/404/");
        }
    }

}