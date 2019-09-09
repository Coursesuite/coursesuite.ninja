<?php

class StoreProductsModel
{

	// index page model
	public static function get_store_section_products_model($route)
	{
		$model = new stdClass();
		if ($route === "index") {
			$sections = SectionsModel::getAllStoreSections(false, true);
			foreach ($sections as &$section) {
				$section->Apps = SectionsModel::get_store_section_apps($section->id, true);
			}
		} else {
			$sections = SectionsModel::getStoreSectionByRoute($route,true);
		}
		$model->Section = $sections;
		return $model;
	}

	// info page model
	public static function get_store_info_model($app_key, $action = "", $action_value = "")
	{

		$model = new stdClass();
		// $model->Feedback = new stdClass();
		$model->ContextualStore = Config::get("FASTSPRING_CONTEXTUAL_STORE");
		$user_email_address = "";

		// don't process certain actions if the user is already logged on (got here via link, back, email, etc)
		if (Session::userIsLoggedIn()) {
			if ($action == "validate" || $action == "reset") {
				$action = "";
			}

			$model->Demoable = ($app_key == "api" && ApiModel::api_is_demoable());
		}

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
				Redirect::app($app_key);
				break;

			case "in-use":
				MessageModel::notify_user("### App in use!\n\nApp is already open elsewhere and you are not licenced for concurrent use. Please close other browsers or tabs.", MESSAGE_LEVEL_MEH);
				Redirect::app($app_key);
				break;

			case "bad-tier":
				MessageModel::notify_user("### Licence problem!\n\nThe app is not licenced for your tier.", MESSAGE_LEVEL_SAD);
				Redirect::app($app_key);
				break;

		}

		$app = (new AppModel("app_key", $app_key))->get_model(true); // ::getAppByKey($app_key);
		$app->cssproperties = json_decode($app->cssproperties); // TODO: automate

		// do entries in the changelog exist for this app?
        // $app->changelog = ChangeLogModel::has_changelog($app->app_id);

		// what are the entries in the changelog for this app?
        $app->changelog = ChangeLogModel::get_app_changelog($app->app_id);

		// expose media as an object to the app
		// $app->media = json_decode($app->media);
		$app->files = (new FilesModel("app",$app->app_key))->get_model();
		//$app->files = json_decode($app->files);

		$model->App = $app;

		// bundles return the whole model including active results (no filter on constructor), so ...
		$pb = (new ProductBundleModel("app_id", $app->app_id))->get_model(true);
		if (!empty($pb)) {
			$pb = array_filter($pb, function($v) {
				if ($v->active !== "1") return false;
				return true;
			});
		}
		$model->Bundles = $pb;

		$model->IsLoggedIn = Session::userIsLoggedIn();
		$model->PreloadedEmail = Session::CurrentUsername();
		$model->Token = Request::cookie('login');
		$model->FreeTrialDays = KeyStore::find("freeTrialDays")->get(3);

		if (Session::userIsLoggedIn()) {

			// subscription model(s) for the current app/user (e.g. products that contain this app; may be one or more if they bought the app as well as a bundle containing the app)
			$model->Subscriptions = SubscriptionModel::get_subscriptions_for_user_for_app(Session::CurrentUserId(), $app->app_id);

			// fastspring store link querystring parameters
			$model->FastspringParams = "?referrer=" . Text::base64enc(Encryption::encrypt(Session::CurrentUserId())) . Config::get('FASTSPRING_PARAM_APPEND');

			// page edit link for admins
			if (Session::userIsAdmin()) {
				$model->editlink = Config::get("URL") . 'admin/editApps/' . $app->app_id . '/edit';
			}
		} else {
			$model->csrf_token = Csrf::makeToken();

		}
		return $model;
	}

	public static function find_store_route_for_app($app_key) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$query = $database->prepare("
			SELECT concat('products/', ss.route, '/', a.app_key) FROM store_sections ss
			INNER JOIN apps a ON find_in_set(cast(a.app_id as char), ss.app_ids)
			WHERE a.app_key = :key
		");
		$query->execute([":key"=>$app_key]);
		return $query->fetchColumn(0);
	}

}
