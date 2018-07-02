<?php

/**
 * MeController
 * Controls everything that is user-related about Me
 */
class MeController extends Controller
{

	private $_base_model;
	/**
	 * Construct this object by extending the basic Controller class.
	 */
	public function __construct($action_name)
	{
		parent::__construct(true,$action_name);
		Auth::checkAuthentication();
		$this->_base_model = [
			"api_visible" => Config::get("API_VISIBLE")
		];
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

	/* --------------------------- AJAX ACTIONS -------------------------------- */
	public function acknowledge() {
		self::requiresAjax();
		$message_id = Request::post("id", false, FILTER_VALIDATE_INT);
		MessageModel::markAsRead($message_id);
	}

	/* --------------------------- DEFAULT VIEW -------------------------------- */
	public function index() {
		self::orders();
	}

	/* --------------------------- MENUBAR ITEMS -------------------------------- */
	public function orders ($action = "view", $order = "") {
		$account = new AccountModel("id",Session::CurrentUserId());
		$feedback = "";
		$feedback_level = MESSAGE_LEVEL_HAPPY;
		switch ($action) {
			case "cancel":
				if (empty($order)) break;
				// value is encrypted for safety
				$fsSubscriptionId = Encryption::decrypt(Text::base64dec($order));
				if (!empty($fsSubscriptionId)) {
					$results = FastSpringModel::delete("/subscriptions/{$fsSubscriptionId}");
					foreach ($results->subscriptions as $item) {
						if ($item->subscription === $fsSubscriptionId) {
							if ($item->result === "success") {
								$feedback = "Order cancelation requested, may take a few minutes to apply.";
							} else if ($item->result === "error") {
								$feedback = $item->error->subscription;
								$feedback_level = MESSAGE_LEVEL_SAD;
							}
						}
					}
				}
				$action = "view";
				break;
		}

		if (!empty($feedback)) {
			MessageModel::notify_user($feedback, $feedback_level);
		}

		$model = $this->_base_model;
		$model["account"] = $account->get_model();
		$model["csrf_token"] = Csrf::makeToken();
		$model["selection"] = "orders";
		$model["subscriptions"] = SubscriptionModel::get_user_subscription_history(Session::CurrentUserId());
		// if ($order > "") $model["orderhistory"] = FastspringModel::get_order_history($order);

		$this->View->Requires("me/menubar");
		$this->View->Requires("account.menu.js");
		$this->View->renderHandlebars("me/orders/{$action}", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
	}

	public function account () {
		$account = new AccountModel("id",Session::CurrentUserId());

		$model = $this->_base_model;
		$model["account"] = $account->get_model();
		$model["logons"] = LoginModel::current_logins_model(Session::CurrentUserId());
		$model["csrf_token"] = Csrf::makeToken();
		$model["selection"] = "account";

		$this->View->Requires("me/menubar");
		$this->View->Requires("account.menu.js");
		$this->View->renderHandlebars("me/account", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
	}

	public function subscriptions () {
		$account = new AccountModel("id",Session::CurrentUserId());
		$mc = new MailChimp(Session::get("user_email"));

		$model = $this->_base_model;
		$model["account"] = $account->get_model();
		$model["csrf_token"] = Csrf::makeToken();
		$model["selection"] = "subscriptions";
		$model["subscriptions"] = $mc->getInterests();

		$this->View->Requires("me/menubar");
		$this->View->Requires("account.menu.js");
		$this->View->renderHandlebars("me/subscriptions", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
	}

	public function apikeys ($action = "view", $action_id = 0, ...$params) {

		if ($this->_base_model["api_visible"] !== true) Redirect::to("/404");

		$model = $this->_base_model;
		$action_id_raw = $action_id;
		$action_id = filter_var(intval($action_id, 10), FILTER_VALIDATE_INT, array('options' => array('min-range' => -1, 'max_range' => PHP_INT_MAX)));

		$model["csrf_token"] = Csrf::makeToken();
		$model["selection"] = "apikeys";
		$model["command"] = $action;
		$model["command_id"] = $action_id;
		$model["command_id_raw"] = $action_id_raw;
		$model["param_array"] = $params;
		$model["command_feedback"] = "";

		// TODO: if the user hasn't already had a trial
		$model["showtrial"] = (!SubscriptionModel::user_has_subscription(Session::CurrentUserId()));
		$model["urls"] = array(
			"trial" => Config::get("URL") . "me/apikeys/trial/",
			"volumelicence" => Keystore::find("volumelicence")->get("")
		);

		$template = "view";

		//$this->View->Requires("me/apikeys/features");
		//$this->View->Requires("me/apikeys/addSubAccount");
		//$this->View->Requires("me/apikeys/editSubAccount");

		if (AccountModel::validate_action($action, $action_id, $action_id_raw, $this)) {
			switch ($action) {
				case "trial":
					$trialId = SubscriptionModel::create_trial_subscription(Session::CurrentUserId());
					var_dump($trialId);
					if ($trialId === -1) {
						MessageModel::notify_user(Text::get("TRIAL_API_ALREADY_SUBSCRIBED"), MESSAGE_LEVEL_HAPPY, Session::CurrentUserId());
					} elseif ($trialId === -2) {
						MessageModel::notify_user(Text::get("TRIAL_API_TOO_MANY_TRIALS"), MESSAGE_LEVEL_MEH, Session::CurrentUserId());
					} elseif ($trialId > 0) {
						// trial-started message?
					} else {
						MessageModel::notify_user(Text::get("TRIAL_API_UNAVAILABLE"), MESSAGE_LEVEL_SAD, Session::CurrentUserId());
					}
					Redirect::to("me/apikeys");
					break;

				case "save":
					$email = trim(Request::post("email", false, FILTER_VALIDATE_EMAIL));
					if (empty($email)) {
						$model["command_feedback"] = Text::get("FEEDBACK_EMAIL_FIELD_EMPTY");
						$model["user_email"] = $email;
						$model["command"] = ($action_id === -1) ? "add" : "edit";
					} else {
						if ($action_id === -1) {
							if (RegistrationModel::register_sub_account(Session::CurrentUserId(), $email) === 0) {
								$model["command_feedback"] = Text::get("FEEDBACK_USER_EMAIL_ALREADY_TAKEN");
								$model["user_email"] = $email;
								$model["command"] = "add";
							} else {
								// success
								Redirect::to("me/apikeys");
							}
						} elseif ($action_id === 0) {
								$model["command_feedback"] = Text::get("INCORRECT_USAGE");
						} else {
							if (Model::Exists("users","user_email=:email",array(":email"=>$email))) {
								$model["command_feedback"] = Text::get("FEEDBACK_USER_EMAIL_ALREADY_TAKEN");
								$model["user_email"] = $email;
								$model["command"] = "edit";
							} else {
								AccountModel::modify_sub_account($action_id, $email);
								Redirect::to("me/apikeys");
							}
						}
					}
					break;

				case "edit":
					$model["sub_heading"] = "Edit sub-account";
					$account = new AccountModel("id",$action_id);
					$model["account"] = $account->get_model();
					$template = "SubAccount-edit";
					break;

				case "add":
					$model["sub_heading"] = "Add sub-account";
					$template = "SubAccount-add";
					break;

				case "features":
					$model["sub_heading"] = "Manage connected apps";
					list($app_key, $function, $method) = array_pad($params, 3, null);
					$subscription_id = Model::ReadColumn("subscriptions", "subscription_id", "subscription_id IN (SELECT subscription_id FROM subscriptions WHERE md5(referenceId)=:hash)", [":hash"=>$action_id_raw]);
					switch ($function) {
						case "customtemplate":
							$wm = new WhiteLabelModel("get", ["subscription_id" => $subscription_id, "app_key" => $app_key]);
							$model["white_label"] = $wm->get_model();
							if ($method === "save") {
								list($filetype, $filename, $tmpname) = [strtolower($_FILES["file"]["type"]), strtolower($_FILES["file"]["name"]), $_FILES["file"]["tmp_name"]];
								if (strpos($filetype,"zip")!==false && strpos($filename,".zip")!==false) {
									Model::WriteBlob("whitelabel", "template", $tmpname, "id=:id", [":id"=>$wm->get_id()]);
									$model["white_label"]->template = true;
								}
							} else if ($method === "remove") {
								$model["white_label"]->template = null;
								$wm->set_model($model["white_label"]);
								$wm->save();
							} else {
								// $template is binary, which crashes lightncandy, so fudge the property
								$model["white_label"]->template = empty($model["white_label"]->template) ? false : true;
							}
							break;

						case "whitelabel":
							$wm = new WhiteLabelModel("get", array("subscription_id" => $subscription_id, "app_key" => $app_key));
							$wmodel = $wm->get_model();
							if ($method === "save") {
								$wmodel->html = Request::post_html("html"); // sanitized html
								$wmodel->css = Request::post("css", true);
								$wm->set_model($wmodel);
								$wm->save();
							}
							$model["white_label"] = $wm->get_model();
							break;

						case "publishurl":
							$wm = new WhiteLabelModel("get", array("subscription_id" => $subscription_id, "app_key" => $app_key));
							$wmodel = $wm->get_model();
							$publishToUrl = Request::post("url", false, FILTER_VALIDATE_URL);
							if ($method === "save") {
								$wmodel->publish_to = is_bool($publishToUrl) ? null : $publishToUrl;
								$wm->set_model($wmodel);
								$wm->save();
							}
							$model["white_label"] = $wm->get_model();
							break;

					}
					$apiInfo = AppModel::public_info_model($action_id_raw, true);
					$apiMods = ApiModel::get_api_mods(); // get default settings, apiInfo->app->mods might override it
					foreach ($apiInfo as &$app) {
						if (empty($app->mods)) {
							$app->mods = $apiMods; // apply default
						} else {
							$app->mods = json_decode($app->mods);
						}
					}
					$model["api_info"] = $apiInfo;
					$template = "features";
					break;

				case "view":
					$account = new AccountModel("id",Session::CurrentUserId());
					$model["account"] = $account->get_model();
					$model["apikeys"] = $account->get_apikeys();
					$model["dropdown"] = ProductBundleModel::get_store_dropdown_model();
					$model["apikey_text"] = KeyStore::find("apikey_text")->get();
					// $model["trial_dropdown"] = array(array(
					// 	"label" => Text::get("TRIAL_API_LABEL"),
					// 	"value" => Config::get("URL") . "me/apikeys/trial/"
					// ));
					// $this->View->Requires("account.apikeys.js");
					break;
			}
		}
		$this->View->Requires("apikeys.js");
		$this->View->Requires("me/menubar");
		$this->View->renderHandlebars("me/apikeys/{$template}", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
	}

	public function support () {
		global $PAGE;
		$model["selection"] = "support";
		$model["helpdesk"] = HelpdeskModel::get_my_tickets($PAGE->user_email);
		$model["AppsBySection"] = StoreProductsModel::get_store_section_products_model("index");
		$this->View->Requires("me/menubar");

		$this->View->Requires("me/support_account");
		$this->View->Requires("me/support_helpdesk");
		$this->View->Requires("me/support_guides");
		// $this->View->Requires("me/support_faq");

		$dn = new AppModel("app_key","docninja");
		// $model["youtube"] = YouTubeModel::get_app_videos($dn);

		$this->View->renderHandlebars("me/support", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
	}


	/**
	 * Show user's PRIVATE profile
	 */
	public function orig()
	{

		// get the view model (which is the account)
		$account = new AccountModel("id",Session::CurrentUserId());
		$model = $account->get_model();

		// $model->sheets = ["uikit/uikit.min.css"];
		// $model->scripts = ["https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.35/js/uikit.min.js", "https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.35/js/uikit-icons.min.js"];

		// and other stuff we need in the view
		$model["baseurl"] = Config::get("URL");
		$model["csrf_token"] = Csrf::makeToken();
		$model["history"] = SubscriptionModel::get_user_subscription_history(Session::CurrentUserId());

		$mc = new MailChimp(Session::get("user_email"));
		$model["subscriptions"] = $mc->getInterests();

		$model["logons"] = LoginModel::current_logins_model(Session::CurrentUserId());

		$model["CurrentSubs"] = SubscriptionModel::get_current_subscribed_apps_model(Session::CurrentUserId());

		$this->View->renderHandlebars("me/orig", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));

	}

	public function update($route = "account") {

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
		$account = new AccountModel("id",Session::CurrentUserId());
		$model = $account->get_model();

		/* ---------------------- email ---------------------- */
		$email = trim(Request::post("email", false, FILTER_SANITIZE_EMAIL));
		if ($route === "account" && !empty($email) && $email <> $model["user_email"]) {

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

			Redirect::to("me/account");

		}

		/* ---------------------- mailchimp ---------------------- */
		if ($route === "subscriptions") {
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

			Redirect::to("me/subscriptions");

		}

		// default redirect
		Redirect::to("me/");

	}

	// send the change verification email again
	public function reverify()
	{
		$account = new AccountModel("id",Session::CurrentUserId());
		$model = $account->get_model();
		self::sendChangeVerificationEmail($model);
		MessageModel::notify_user(Text::get("FEEDBACK_USER_EMAIL_CHANGE_VERIFICATION"));
		Redirect::to("me/");
	}

	// cancel an email change
	public function expunge()
	{
		$account = new AccountModel("id",Session::CurrentUserId());
		$model = $account->get_model();
		$model["user_email_update"] = NULL;
		$model["change_verification_hash"] = NULL;
		$account->set_model($model);
		$account->save();
		Redirect::to("me/");
	}

	// show the api functions
	public function api($action = "") {
		$model = ApiModel::publicApi(Session::CurrentUserId());
		$this->View->renderHandlebars("me/api", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));

	}

	// log out my sessions other than the current
	public function logoutother() {
		$user_id = Session::CurrentUserId();
		$retain_cookie = Request::cookie('login');
		Auth::logout_user_all($user_id, $retain_cookie);
		Redirect::to("me/account");
	}

	public function dl($hash, ...$params) {
		// 08ff71edaab9952f06d5f1da1c4acedf/docninja/template
		exit;
	}

}
