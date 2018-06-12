<?php

	/* ------------------------------------------------------------------------------------------------------------------------------------------------------------

	STORE PAGE REGISTRATION AND ACCOUNT VALIDATION

	------------------------------------------------------------------------------------------------------------------------------------------------------------ */

class RegistrationModel
{

	private static function graph($msg) {
		$alt = "cs-label-muted";
		$css = $alt;
		if ($msg > 0) $css = "uk-label-success";
		if ($msg < 0) $css = "uk-label-warning";
		$html[] = "<p>";
		$html[] = "<span class='uk-label " . (abs($msg)===1 ? $css : $alt) . "' uk-tooltip='First, enter your email address to register or log in'><span uk-icon='pencil'></span> Enter email</span>";
		$html[] = " <span uk-icon='arrow-right'></span> ";
		$html[] = "<span class='uk-label " . (abs($msg)===2 ? $css : $alt) . "' uk-tooltip='Next, check your email for the password'><span uk-icon='mail'></span> Get password</span>";
		$html[] = " <span uk-icon='arrow-right'></span> ";
		$html[] = "<span class='uk-label cs-label-muted' uk-tooltip='Finally, you are good to go!'><span uk-icon='sign-in'></span> logged in!</span>";
		$html[] = "</p>";
		return implode('',$html);
	}

	/**
	 * registering requires a model to be present on every View page
	 * which you don't get if you are already logged on
	 * this routine builds the model based on your input
	 * and as such is also the primary logon routine
	 */
	public static function get_page_model($route = "") {

		if (Session::userIsLoggedIn()) return;

		$result = new stdClass();
		$result->csrf_token = Csrf::makeToken();
		$result->className = "";
		$result->message = "";
		$result->graph = "";
		$result->reset = false;

		$email 		= trim(Request::post("email", false, FILTER_SANITIZE_EMAIL));
		$password 	= trim(Request::post("onetimepassword"));

		$result->show = ($password > "" || $email > "");
		$result->sent = false;

		Response::cookie("login", null);

		if ($password > "" and $email == "") {
			$email = Session::get("otp_email", "");
		}

		if ($result->show && !Csrf::validateToken(Request::post("csrf_token"))) {
			$result->className = "uk-text-danger";
			$result->message = "Invalid CSRF token. Please refresh and try again. ";

		} elseif (BlacklistModel::isBlacklisted($email)) {
			$result->message = Text::get('REGISTRATION_DOMAIN_BLACKLISTED');
			$result->className = "uk-text-danger";

		} elseif ($email == "" && $password == "") {
			$result->graph = self::graph(1);
			$result->message = "";

		} elseif (Auth::is_administrator_email($email)) {
			$result->graph = self::graph(0);
			$result->message = "Please logon using the appropriate method for this account.";

		} elseif ($email > "" && $password == "") { // don't resend if you hit refresh whilst the modal is loaded
			if (self::send_one_time_password($email, $route)) {
				Session::set("otp_email", $email);
				$result->sent = true;
				$result->className = "uk-text-success";
				$result->graph = self::graph(2);
				$result->message = "Check your email for the password.";
			}
		} elseif ($email == "" && $password > "") {
			$result->graph = self::graph(-1);
			$result->message = "Sorry your session has timed out - refresh your browser.";
			$result->className = "uk-text-warning";

		} elseif ($email > "" && $password > "") {
			$model = Model::Read("users", "user_email=:email", array(":email" => $email));
			if (isset($model)) $model = $model{0};
			if (!password_verify($password, $model->user_password_hash)) {
				$result->graph = self::graph(-2);
				$result->message = "Password mismatch. Try again, or refresh your browser to start over.</p>";
				$result->reset = true;
				$result->sent = true;
				$result->className = "uk-text-warning";
				$model->user_failed_logins += 1;
				$model->user_last_failed_login = time();
				Model::Update("users","user_id",$model);

			} else {
				Session::remove("otp_email");
				$model->user_password_reset_hash = NULL;
				$model->user_failed_logins = 0;
				$model->user_last_login_timestamp = time();
				$model->user_last_failed_login = NULL;
				$model->user_suspension_timestamp = NULL;
				$model->user_password_reset_timestamp = NULL;
				// $model->user_remember_me_token = md5(uniqid());
				$model->user_logon_count = (int) $model->user_logon_count + 1;
				// if ((int) $model->user_account_type !== (int) Config::get('ADMIN_ACCOUNT_LEVEL')) {
				// 	$model->user_password_hash = password_hash(uniqid(), PASSWORD_DEFAULT); // scramble current password so the current one can't be used again
				// }
				Model::Update("users","user_id",$model);

				// the actual logon is just setting a hash value in a cookie
				// then putting that into the db for auto-logon
				Auth::set_user_logon_cookie($model->user_id);
				$result->show = false; // no need to keep showing the dialogue

				Redirect::here(true);

			}
		}

		$result->email = $email;
		return $result;
	}

	// create or update a user account and set the password; email the user that password
	public static function send_one_time_password($email, $route = "")
	{
		$database = DatabaseFactory::getFactory()->getConnection();

		$model = Model::Read("users", "user_email=:email", array(":email" => $email));
		if (empty($model)) { // user with this email address was not found
			$model = (object) Model::Create("users");
			$model->user_email = $email;
			$model->user_active = 1;
			$model->user_creation_timestamp = time();
		} else {
			$model = $model{0};
		}

		$password = (new Sayable(9))->generate();
		if (Config::get("debug")) {
			Session::set("otp", $password);
		}
		$model->user_password_hash = password_hash($password, PASSWORD_DEFAULT);

		$model->last_browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
		$model->last_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";

		$user_id = Model::Update("users", "user_id", $model);

		// if (empty($route)) {
		$linkUrl = Config::get('URL') . $route;
		// } else {
		//	$linkUrl = Config::get('URL') . StoreProductsModel::find_store_route_for_app($app_key);
		// }

		$mail_sent = (new Mail)->sendOneTimePassword($email, $password, $linkUrl);
		return $mail_sent;

	}

	public static function register_sub_account($owner_user_id, $sub_account_email) {

		$user_id = 0;
		$model = Model::Read("users", "user_email=:email", array(":email" => $sub_account_email));
		if (empty($model)) {

			// user with this email address was not found - create it
			$model = (object) Model::Create("users");
			$model->user_email = $sub_account_email;
			$model->user_active = 1;
			$model->user_creation_timestamp = time();
			$model->user_parent_id = $owner_user_id;

			// generate a secret key for the api
			$model->secret_key = Text::base64enc(Encryption::encrypt(uniqid()));

			// ensure a password is set on the account
			$model->user_password_hash = password_hash(uniqid(), PASSWORD_DEFAULT);

			// save the model
			$user_id = Model::Update("users", "user_id", $model);

		}
		// return the id of the new user, or zero
		return $user_id;

	}

}
