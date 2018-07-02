<?php


class LoginController extends Controller
{

	public function __construct($action_name)
	{
		parent::__construct(true,$action_name);
	}


	public function index()
	{
		// global $PAGE;

		if (Session::userIsLoggedIn()) {
			Redirect::home();
		} else {
			// $data = array(
			// 	'redirect' => Text::unescape(Request::get('redirect') ? Request::get('redirect') : Session::get("RedirectTo")),
			// 	'baseurl' => Config::get('URL'),
			// 	'csrf_token' => Csrf::makeToken(),
			// 	'ShowPassword' => (!empty(Session::get("otp_email"))),
			// 	'ajax' => true
			// );
			$this->View->Requires("login.js");
			$this->View->renderHandlebars('login/index', [], "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
		}
	}

	public function retry() {
		Session::remove("otp_email");
		Redirect::to("login");
	}

	public function timeout($appkey = "")
	{
		$model = array(
			'baseurl' => Config::get('URL'),
		);
		if (!empty($appkey)) {
			Session::set("RedirectTo", "launch/app/$appkey");
		}
		$this->View->renderHandlebars("login/timeout", $model, "_overlay", true);
	}

	public function logout($all = "false")
	{
		LoginModel::logout($all === "true");
		Redirect::home("refresh");
		exit();
	}

	/* ------------------------------------------------------------------------------------------------------------------------------------------------------------

	ADMIN function - impersonate (log in as) any user

	------------------------------------------------------------------------------------------------------------------------------------------------------------ */
	public function impersonate($enc = '') {
		$dec = Text::base64_urldecode($enc);
		$uids = Encryption::decrypt($dec);
		// quietly ignore missing params
		@list($uid,$source) = array_map('intval',explode(',',$uids));
		if ($uid > 0) {
			Session::reset();
			Auth::set_user_logon_cookie($uid,$source);
			Redirect::to('me/');
			exit;
		}
		header('HTTP/1.0 404 Not Found', true, 404);
		$this->View->render('error/404');
	}


	/* ------------------------------------------------------------------------------------------------------------------------------------------------------------

	STORE PAGE INTEGRATED REGISTRATION AND LOGON FORM HANDLER (ajax)

	------------------------------------------------------------------------------------------------------------------------------------------------------------ */

	// /login/authenticate called using ajax from store/info/app_key -> integrated.hbp
	// public function authenticate()
	// {

	// 	Response::cookie("login", null);

	// 	$result = new stdClass();
	// 	$result->message = ""; // "¯\_(ツ)_/¯";
	// 	$result->className = "meh";

	// 	$redirect 	= trim(Request::post("redirect", true, FILTER_SANITIZE_STRING));
	// 	$email 		= trim(Request::post("email", false, FILTER_SANITIZE_EMAIL));
	// 	$app_key 	= trim(Request::post("app_key", false, FILTER_SANITIZE_STRING));
	// 	$password 	= trim(Request::post("password"));

	// 	if ($password > "" and $email == "") {
	// 		$email = Session::get("otp_email", "");
	// 	}

	// 	if (!Csrf::validateToken(Request::post("csrf_token"))) {
	// 		$result->message = "Invalid CSRF token. Please refresh and try again. ";

	// 	} elseif (BlacklistModel::isBlacklisted($email)) {
	// 		$result->message = Text::get('REGISTRATION_DOMAIN_BLACKLISTED');
	// 		$result->className = "sad";

	// 	} elseif ($email == "" && $password == "") {
	// 		$result->message = "Please enter your email address.";

	// 	} elseif (Auth::is_administrator_email($email)) {
	// 		$result->message = "Please logon using the appropriate method for this account.";

	// 	} elseif ($email > "" && $password == "") {
	// 		RegistrationModel::send_one_time_password($email, $app_key);
	// 		Session::set("otp_email", $email);

	// 		$newmodel = new stdClass();
	// 		$newmodel->ajax = true;
	// 		$newmodel->baseurl = Config::get("URL");
	// 		$newmodel->csrf_token = Csrf::makeToken();
	// 		$newmodel->App = new stdClass();
	// 		$newmodel->App->app_key = $app_key;
	// 		$newmodel->ShowPassword = true;
	// 		if (!empty($app_key)) {
	// 			$newmodel->redirect = StoreProductsModel::find_store_route_for_app($app_key);
	// 		} else {
	// 			$newmodel->redirect = "/login/";
	// 		}
	// 		$result->html = $this->View->renderHandlebars("login/ajaxlogon", $newmodel, null, true, true);

	// 	} elseif ($email == "" && $password > "") {
	// 		$result->message = "Sorry your session has timed out.";
	// 		$result->className = "sad";

	// 	} elseif ($email > "" && $password > "") {
	// 		$model = Model::Read("users", "user_email=:email", array(":email" => $email));
	// 		if (isset($model)) $model = $model{0};
	// 		if (!password_verify($password, $model->user_password_hash)) {
	// 			$result->message = "Your password did not match.";
	// 			$result->className = "sad";
	// 			$result->csrf_token = Csrf::makeToken();
	// 		} else {
	// 			Session::remove("otp_email");
	// 			$model->user_password_reset_hash = NULL;
	// 			$model->user_failed_logins = 0;
	// 			$model->user_last_login_timestamp = time();
	// 			$model->user_last_failed_login = NULL;
	// 			$model->user_suspension_timestamp = NULL;
	// 			$model->user_password_reset_timestamp = NULL;
	// 			// $model->user_remember_me_token = md5(uniqid());
	// 			$model->user_logon_count = (int) $model->user_logon_count + 1;
	// 			if ((int) $model->user_account_type !== (int) Config::get('ADMIN_ACCOUNT_LEVEL')) {
	// 				$model->user_password_hash = password_hash(uniqid(), PASSWORD_DEFAULT); // scramble current password so the current one can't be used again
	// 			}
	// 			Model::Update("users","user_id",$model);

	// 			// the actual logon is just setting a hash value in a cookie
	// 			// then putting that into the db for auto-logon
	// 			Auth::set_user_logon_cookie($model->user_id);

	// 			$redirect = "/me/";
	// 			$result->redirect = $redirect;
	// 			$result->reload = true;
	// 		}
	// 	}

	// 	if ($this->Method === "AJAX") {
	// 		$this->View->renderJSON($result);
	// 	} else {
	// 		if ($result->className === "sad") {
	// 			Session::set("feedback_negative", $result->message);
	// 		} else if ($result->className === "happy") {
	// 			Session::set("feedback_positive", $result->message);
	// 		} else if ($result->className === "intermediate") {
	// 			Session::set("feedback_intermediate", $result->message);
	// 		} else {
	// 			Session::set("feedback_meh", $result->message);
	// 		}
	// 		Redirect::to($redirect);
	// 	}
	// }

	public function admin() {
		$data = new stdClass();
		$data->baseurl = Config::get('URL');
		$data->csrf_token = Csrf::makeToken();
		$password = trim(Request::post("password", false, FILTER_SANITIZE_STRING));
		if (!empty($password)) {
			$model = Model::Read("users", "user_account_type=:type AND user_email LIKE :email", array(":type" => Config::get('ADMIN_ACCOUNT_LEVEL'), ":email" => Config::get('ADMIN_ACCOUNT_EMAIL')));
			if (isset($model)) $model = $model{0};
			if (!password_verify($password, $model->user_password_hash)) {
				$data->feedback = "No that's not right, try again";
			} else {
				Auth::set_user_logon_cookie($model->user_id);
				Redirect::to("/admin/");
			}
		}
		$this->View->renderHandlebars('login/admin', $data, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
	}

	public function callback($object) {
		$json = json_decode(Text::base64dec($object));
		$logto = "/me/orders";
		$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : die("0xFF");
		$host = $_SERVER['HTTP_HOST'];
		$i = strpos($referer, $host); if ($i === false) die("0xFE");
		if (Config::get("AUTO_LOGON_TO") === "self") {
			$logto = substr($referer, $i + strlen($_SERVER['HTTP_HOST']));
		}
		if (!empty($json->reference)) {
			$uid = Model::ReadColumn("subscriptions", "user_id", "referenceId=:r", [":r"=>$json->reference]);
			if ($uid > 0) {
				Session::reset();
				Auth::set_user_logon_cookie($uid);
				Redirect::to($logto,"meta");// redirect after sending cookie
				exit;
			}
		}
	}

}
