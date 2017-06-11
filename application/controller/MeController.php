<?php

/**
 * MeController
 * Controls everything that is user-related about Me
 */
class MeController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class.
     */
    public function __construct()
    {
        parent::__construct();

        // VERY IMPORTANT: All controllers/areas that should only be usable by logged-in users
        // need this line! Otherwise not-logged in users could do actions.
        Auth::checkAuthentication();
    }

    // internal function for sending the email change verification email
    // probably should be statics on the account model
    private function sendChangeVerificationEmail($model)
    {
                $message = Text::formatString("EMAIL_USER_EMAIL_CHANGE_VERIFICATION", array(
                    "old" => $model["user_email"],
                    "new" => $model["user_email_update"],
                    "link" => Config::get("URL") . "email/verifyChange/" . $model["change_verification_hash"],
                ));
                Mail::sendMail($model["user_email_update"], Config::get("EMAIL_VERIFICATION_FROM_EMAIL"), Config::get("EMAIL_VERIFICATION_FROM_NAME"), Text::get("EMAIL_VERIFICATION_SUBJECT"), $message);
    }

    private function sendPasswordResetEmail($model)
    {
                $message = Text::formatString("EMAIL_USER_PASSWORD_RESET_REQUEST", array(
                    "link" => Config::get("URL") . "email/confirmResetPassword/" . $model["user_password_reset_hash"],
                ));
                Mail::sendMail($model["user_email"], Config::get("EMAIL_VERIFICATION_FROM_EMAIL"), Config::get("EMAIL_VERIFICATION_FROM_NAME"), Text::get("EMAIL_PASSWORD_SUBJECT"), $message);
    }

    /**
     * Show user's PRIVATE profile
     */
    public function index()
    {

        // get the view model (which is the account)
        $account = new AccountModel(Session::get('user_id'));
        $model = $account->get_model();

        // and other stuff we need in the view
        $model["baseurl"] = Config::get("URL");
        $model["csrf_token"] = Csrf::makeToken();
        $model["history"] = SubscriptionModel::get_user_subscription_history(Session::get("user_id"));

           // 'subscriptions' => array_reverse(SubscriptionModel::getAllSubscriptions(Session::get('user_id'), false, false, false, true, 'added')),
           // 'products' => ProductModel::getAllSubscriptionProducts(),
           // 'store_url' => TierModel::getTierById(1, false)->store_url . "?referrer=" . Text::base64enc(Encryption::encrypt(Session::CurrentUserId())) . Config::get('FASTSPRING_PARAM_APPEND'),

        $mc = new MailChimp(Session::get("user_email"));
        $model["subscriptions"] = $mc->getInterests();

        /*
        if (isset($model["subscriptions"]) && sizeof($model["subscriptions"]) > 0) {
        $fsprg = new Fastspring('coursesuite', Config::get('FASTSPRING_API_USER'), Config::get('FASTSPRING_API_PASSWORD'));
        try {
        $model["subscription_type"] = $fsprg->getSubscription($model['subscriptions'][0]->referenceId)->productName;
        } catch (Exception $e) {
        echo($e->getMessage());
        }
        }
         */
        $this->View->renderHandlebars("me/index", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));

    }

    public function update() {

        // try to ward off attacks
         if (!Csrf::isTokenValid()) {
            LoginModel::logout();
            Redirect::home();
            exit();
        }

        // delete this account forever, but send them a message
        if (Request::post("destroy") === "yes") {

            // TODO:
            // unregister them from MailChimp, or maybe move their subscription to a deleted bin

            $mail_sent = (new Mail)->sendGoodbye(Session::get("user_email"));
            LoginModel::logout();
            UserModel::destroyUserForever(Session::get('user_id'));
            Redirect::home();
            exit();
        }

        // the user model
        $account = new AccountModel(Session::get('user_id'));
        $model = $account->get_model();

        // the things we might have updated
        $email = trim(Request::post("email", false, FILTER_SANITIZE_EMAIL));
        $reset_password = (Request::post("reset_password") === "yes");
        $lists = Request::post("mailchimp_list") ?: [];

        // has the user requested a new password?
        // !important - do this BEFORE a requested email change
        if ($reset_password) {

            // generate a password reset hash
            $model["user_password_reset_hash"] = sha1(uniqid(mt_rand(), true));
            $model["user_password_reset_timestamp"] = time();

            // persist the change
            $account->set_model($model);
            $account->save();

             // email them the change hash to the NEW email, so that has to exist
            self::sendPasswordResetEmail($model);

           // set a persistent message so they know they have a pending change
            MessageModel::notify_user(Text::get("FEEDBACK_USER_PASSWORD_RESET_REQUEST"));

        }

        // has the user updated their email? Reverify the account
        if (!empty($email) && $email <> $model["user_email"]) {

                if (BlacklistModel::isBlacklisted($email)) {

                    Session::set("feedback_negative", "Sorry, this email domain has been blacklisted and cannot be used. Please email us for more information.");
                    Session::set("feedback_area", "user_email");

                } else if (UserModel::doesEmailAlreadyExist($email)) {

                    // form-based feedback
                    Session::set("feedback_negative", Text::get('FEEDBACK_USER_EMAIL_ALREADY_TAKEN_CHANGE'));
                    Session::set("feedback_area", "user_email");

                } else {

                    // store the requested email
                    $model["user_email_update"] = $email;

                    // generate a change hash
                    $model["change_verification_hash"] = sha1(uniqid(mt_rand(), true));

                    // persist the change
                    $account->set_model($model);
                    $account->save();

                     // email them the change hash to the NEW email, so that has to exist
                    self::sendChangeVerificationEmail($model);

                    // set a persistent message so they know they have a pending change
                    MessageModel::notify_user(Text::get("FEEDBACK_USER_EMAIL_CHANGE_VERIFICATION"));

                }

        }

        $mc = new MailChimp(Session::get("user_email"));
        $possible_interests = $mc->getAllInterests();
        $interests = new stdClass();
        foreach ($possible_interests as $item) {
            $itemId = $item["id"];
            $state = in_array($itemId, $lists);
            $interests->$itemId = $state;
        }
        $mc->setInterests($interests);

        // redirect back to /me/
        Redirect::to("me/");

    }

    // send the change verification email again
    public function reverify()
    {
        $account = new AccountModel(Session::get('user_id'));
        $model = $account->get_model();
        self::sendChangeVerificationEmail($model);
        MessageModel::notify_user(Text::get("FEEDBACK_USER_EMAIL_CHANGE_VERIFICATION"));
        Redirect::to("me/");
    }

    // cancel an email change
    public function cancelChange()
    {
        $account = new AccountModel(Session::get('user_id'));
        $model = $account->get_model();
        $model["user_email_update"] = NULL;
        $model["change_verification_hash"] = NULL;
        $account->set_model($model);
        $account->save();
        Redirect::to("me/");
    }

}
