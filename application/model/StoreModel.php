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
		// $model->Feedback = new stdClass();
		$user_email_address = "";

		// don't process certain actions if the user is already logged on (got here via link, back, email, etc)
		if (Session::userIsLoggedIn()) {
			if ($action == "validate" || $action == "reset") {
				$action = "";
			}
		}

		//if (null !== Session::get("feedback_positive")) {
		//	$model->Feedback->positive = Session::get("feedback_positive");
		//}
		//if (null !== Session::get("feedback_negative")) {
		//	$model->Feedback->negative = Session::get("feedback_negative");
		//}

		switch ($action) {
			case "validate":
				//  user is openening this store info link from a registration validation link
				if (RegistrationModel::validate_user_activation_hash_and_reset_condition($action_value, $user_email_address)) {
					$model->ShowSubscribe = true; // show the subscribe to newsletter feature
					$model->feedback_positive = "Cool, your account has been activated, and we logged you on automatically. Welcome aboard!";
				} else {
					$model->feedback_negative = "Uh-oh! Something went wrong validating this account. Try it again, or try re-registering.";
				}
				break;

			case "reset":
				// user has already been sent a password reset link and now needs to be given a new password
				RegistrationModel::validate_password_reset_hash_and_genetate_new_password_and_email_it($action_value, $app_key, $user_email_address);
				$model->feedback_positive = "Nice one. We have generated a new password for you and emailed it to you. Enter it above, and you're set!";
				break;

			case "bad-token":
				MessageModel::notify_user("### Token Problem!\n\nThe token used to launch this app was malformed. You may need to sign on again.", MESSAGE_LEVEL_SAD);
				Redirect::to("/store/info/$app_key");
				break;

			case "in-use":
				MessageModel::notify_user("### App in use!\n\nApp is already open elsewhere and you are not licenced for concurrent use. Please close other browsers or tabs.", MESSAGE_LEVEL_MEH);
				Redirect::to("/store/info/$app_key");
				break;

			case "bad-tier":
				MessageModel::notify_user("### Licence problem!\n\nThe app is not licenced for your tier.", MESSAGE_LEVEL_SAD);
				Redirect::to("/store/info/$app_key");
				break;

		}

		// expose media as an object to the app
		$app->media = json_decode($app->media);

		$model->baseurl = $url;
		$model->App = $app;
		$model->Tiers = AppTierModel::get_tiers($app->app_id);
		$model->Bundles = BundleModel::get_bundles($app->app_id);
		$model->IsLoggedIn = Session::userIsLoggedIn();
		// $model->GoogleSiteKey = Config::get('GOOGLE_CAPTCHA_SITEKEY');
		$model->PreloadedEmail = $user_email_address;
		$model->FreeTrialDays = KeyStore::find("freeTrialDays")->get(3);
		$model->scripts = ["//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"];

		if (Session::currentUserId() > 0) {

			// subscription model(s) for the current app/user (e.g. products that contain this app; may be one or more if they bought the app as well as a bundle containing the app)
			$model->Subscriptions = SubscriptionModel::get_subscriptions_for_user_for_app(Session::CurrentUserId(), $app->app_id);

			// fastspring store link querystring parameters
			$model->FastspringParams = "?referrer=" . Text::base64enc(Encryption::encrypt(Session::CurrentUserId())) . Config::get('FASTSPRING_PARAM_APPEND');

			// page edit link for admins
			if (Session::get("user_account_type") == 7) {
				$model->editlink = $url . 'admin/editApps/' . $app->app_id . '/edit';
			}
		} else {
			$model->csrf_token = Csrf::makeToken();

		}

		$model->ajax = false; // true;
		$model->redirect = "store/info/$app_key";

		return $model;
	}

}
