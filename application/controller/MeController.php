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
    public function __construct($action_name)
    {
        parent::__construct(true,$action_name);
        Auth::checkAuthentication();
    }

    // internal function for sending the email change verification email
    private function sendChangeVerificationEmail($model)
    {
        $message = Text::formatString("EMAIL_USER_EMAIL_CHANGE_VERIFICATION", array(
            "old" => $model["user_email"],
            "new" => $model["user_email_update"],
            "link" => Config::get("URL") . "email/verifyChange/" . $model["change_verification_hash"],
        ));
        Mail::sendMail($model["user_email_update"], Config::get("EMAIL_VERIFICATION_FROM_EMAIL"), Config::get("EMAIL_VERIFICATION_FROM_NAME"), Text::get("EMAIL_VERIFICATION_SUBJECT"), $message);
    }

    /**
     * Show user's PRIVATE profile
     */
    public function index()
    {

        // get the view model (which is the account)
        $account = new AccountModel(Session::CurrentUserId());
        $model = $account->get_model();

        // and other stuff we need in the view
        $model["baseurl"] = Config::get("URL");
        $model["csrf_token"] = Csrf::makeToken();
        $model["history"] = SubscriptionModel::get_user_subscription_history(Session::get("user_id"));

        $mc = new MailChimp(Session::get("user_email"));
        $model["subscriptions"] = $mc->getInterests();

        $model["CurrentSubs"] = SubscriptionModel::get_current_subscribed_apps_model(Session::get("user_id"));

        $this->View->renderHandlebars("me/index", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));

    }

    public function update() {

        $result = new stdClass();
        $result->message = "Updated"; // "¯\_(ツ)_/¯";
        $result->className = "happy";

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
            UserModel::destroyUserForever(Session::CurrentUserId());
            Redirect::home();
            exit();
        }

        // the user model
        $account = new AccountModel(Session::CurrentUserId());
        $model = $account->get_model();


        /* ---------------------- email ---------------------- */
        $email = trim(Request::post("email", false, FILTER_SANITIZE_EMAIL));
        if (!empty($email) && $email <> $model["user_email"]) {

            if (BlacklistModel::isBlacklisted($email)) {

                $result->message = Text::get('REGISTRATION_DOMAIN_BLACKLISTED');
                $result->className = "sad";
                $result->csrf_token = Csrf::makeToken();

            } else if (UserModel::doesEmailAlreadyExist($email)) {

                $result->message = Text::get('FEEDBACK_USER_EMAIL_ALREADY_TAKEN_CHANGE');
                $result->className = "intermediate";
                $result->csrf_token = Csrf::makeToken();

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
                $notify_text = Text::get("FEEDBACK_USER_EMAIL_CHANGE_VERIFICATION");
                MessageModel::notify_user($notify_text);
                $result->csrf_token = Csrf::makeToken();
                $result->message = $notify_text;
                $result->className = "happy";

            }

        }

        /* ---------------------- mailchimp ---------------------- */
        $lists = Request::post("mailchimp_list") ?: [];
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

        if ($this->Method === "AJAX") {
            $this->View->renderJSON($result);
        } else {
            Redirect::to("me/");
        }

    }

    // send the change verification email again
    public function reverify()
    {
        $account = new AccountModel(Session::CurrentUserId());
        $model = $account->get_model();
        self::sendChangeVerificationEmail($model);
        MessageModel::notify_user(Text::get("FEEDBACK_USER_EMAIL_CHANGE_VERIFICATION"));
        Redirect::to("me/");
    }

    // cancel an email change
    public function expunge()
    {
        $account = new AccountModel(Session::CurrentUserId());
        $model = $account->get_model();
        $model["user_email_update"] = NULL;
        $model["change_verification_hash"] = NULL;
        $account->set_model($model);
        $account->save();
        Redirect::to("me/");
    }

    public function api() {
        $model = ApiModel::publicApi(Session::CurrentUserId());
        $this->View->renderHandlebars("me/api", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));

    }

}
