<?php

class StoreModel
{

    // index page model
    public static function get_store_index_model()
    {
        $url = Config::get("URL");
        $sections = SectionsModel::getAllStoreSections();
        foreach ($sections as &$section) {
            $section->Apps = AppModel::getAppsByStoreSection($section->id);
        }

        $model = new stdClass();
        $model->Section = $sections;
        $model->baseurl = $url;

        if (Session::userIsLoggedIn()) {
            // these are the app ids that a user has a current subscription
            $model->SubscribedApps = SubscriptionModel::get_subscribed_app_ids_for_user(Session::CurrentUserId());

            // admins get to see all store sections
            if (Session::get("user_account_type") == 7) {
                foreach ($sections as &$section) {
                    $section->visible = 1;
                }
            }
        }

        $model->storeurl = $url . CONFIG::get('DEFAULT_CONTROLLER') . '/info/';
        $model->howelse = (KeyStore::find("howelse")->get() == "true");

        return $model;
    }

    // info page model
    public static function get_store_info_model($app_key, $action = "", $action_value = "")
    {

        $url = Config::get("URL");
        $app = AppModel::getAppByKey($app_key);
        $model = new stdClass();

        switch ($action) {
            case "validate":
                //  user is openening this store info link from a registration validation link
                // action value will be the users.user_activation_hash to check
                if (RegistrationModel::validate_user_activation_hash_and_reset_condition($action_value)) {
                    $model->ShowSubscribe = true; // show the subscribe to newsletter feature
                    $model->Feedback = "Cool, your account has been activated. Please log in using the password we provided.";
                    $model->FeedbackClass = "happy";
                } else {
                    $model->Feedback = "Uh-oh! Something went wrong validating this account. Try it again, or try re-registering.";
                    $model->FeedbackClass = "";
                }

            case "reset":
                // user has already been sent a password reset link and now needs to be given a new password
                RegistrationModel::validate_password_reset_hash_and_genetate_new_password_and_email_it($action_value, $app_key);
                $model->Feedback = "Nice one. We have generated a new password for you and emailed it to you.";
                $model->FeedbackClass = "happy";

            case "timeout":
                // user launched app but it told them they were concurrent or timed out
                $model->Feedback = "You'll need to log on again to launch this app.";
                $model->FeedbackClass = "happy";


            break;
        }

        $model->baseurl = $url;
        $model->App = $app;
        $model->Tiers = AppTierModel::get_tiers($app->app_id);
        $model->Bundles = BundleModel::get_bundles($app->app_id);
        $model->IsLoggedIn = Session::userIsLoggedIn();
        $model->GoogleSiteKey = Config::get('GOOGLE_CAPTCHA_SITEKEY');

        if (Session::currentUserId() > 0) {

            // subscription model(s) for the current app/user (e.g. products that contain this app; may be one or more if they bought the app as well as a bundle containing the app)
            $model->Subscriptions = SubscriptionModel::get_subscriptions_for_user_for_app(Session::CurrentUserId(), $app->app_id);

            // fastspring store link querystring parameters
            $model->FastspringParams = "?referrer=" . Text::base64enc(Encryption::encrypt(Session::CurrentUserId())) . Config::get('FASTSPRING_PARAM_APPEND');

            // page edit link for admins
            if (Session::get("user_account_type") == 7) {
                $model["editlink"] = $url . 'admin/editApps/' . $app->app_id . '/edit';
            }
        } else {
            $model->csrf_token = Csrf::makeToken();

        }

        return $model;
    }

}
