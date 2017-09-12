<?php

/**
 * LoginController
 * Controls everything that is authentication-related
 */
class LoginController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class. The parent::__construct thing is necessary to
     * put checkAuthentication in here to make an entire controller only usable for logged-in users (for sure not
     * needed in the LoginController).
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Index, default action (shows the login form), when you do login/index
     */
    public function index()
    {
        // if user is logged in redirect to main-page, if not show the view
        if (LoginModel::isUserLoggedIn()) {
            Redirect::home();
        } else {
            $data = array(
                'redirect' => Text::unescape(Request::get('redirect') ? Request::get('redirect') : Session::get("RedirectTo") ? Session::get("RedirectTo") : null),
                'baseurl' => Config::get('URL'),
                'formdata' => Session::get('form_data'),
                // 'GoogleSiteKey' => Config::get('GOOGLE_CAPTCHA_SITEKEY'),
                'csrf_token' => Csrf::makeToken(),
            );
            $this->View->renderHandlebars('login/index', $data, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
        }
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

    public function logout()
    {
        LoginModel::logout();
        Redirect::home();
        exit();
    }

    public function loginWithCookie()
    {
        // run the loginWithCookie() method in the login-model, put the result in $login_successful (true or false)
        $login_successful = LoginModel::loginWithCookie(Request::cookie('remember_me'));

        // if login successful, redirect to dashboard/index ...
        if ($login_successful) {
            Redirect::to('store/index');
        } else {
            // if not, delete cookie (outdated? attack?) and route user to login form to prevent infinite login loops
            LoginModel::deleteCookie();
            Redirect::to('login/index');
        }
    }

    public function discourseSSO($skipGet = false)
    {
        Session::set('discourse_sso', true);

        // not sure how secure this is
        // if ($_SERVER['HTTP_REFERER'] == 'http://forum.coursesuite.ninja/'){

        if (Request::exists("sso") && Request::exists("sig")) {
            Session::set('discourse_payload', $_GET['sso']);
            Session::set('discourse_signature', $_GET['sig']);
        } else {
            if (!Session::get('discourse_payload') && !Session::get('discourse_signature')) {
                throw new Exception(Text::get("INCORRECT_USAGE"));
            }
        }

        if (LoginModel::isUserLoggedIn()) {
            $userInfo = LoginModel::discourseSSO();
            Discourse::login($userInfo->user_id, $userInfo->user_email, $userInfo->user_email, Session::get('discourse_payload'), Session::get('discourse_signature'));
        } else {
            Redirect::to('login/index');
        }

    }

    /* ------------------------------------------------------------------------------------------------------------------------------------------------------------

    STORE PAGE INTEGRATED REGISTRATION AND LOGON FORM HANDLER (ajax)

    ------------------------------------------------------------------------------------------------------------------------------------------------------------ */
    public function impersonate($enc = '') {
        $dec = Text::base64_urldecode($enc);
        $uid = Encryption::decrypt($dec);
        if ($uid > 0) {
            echo " uid=$uid";
            LoginModel::softLogout();
            $ac = new AccountModel($uid);
            $model = $ac->get_model();
            $mail = $model["user_email"];
            $type = $model["user_account_type"];
            LoginModel::setSuccessfulLoginIntoSession($uid, $mail, $type, null, null, null);
            Redirect::to('me/');
            exit;

        }
        header('HTTP/1.0 404 Not Found', true, 404);
        $this->View->render('error/404');
    }


    // /login/authenticate called using ajax from store/info/app_key -> integrated.hbp
    public function authenticate()
    {

        $redirect = trim(Request::post("redirect", true, FILTER_SANITIZE_STRING));

        LoginModel::softLogout(); // to be sure, to be sure

        $result = new stdClass();
        $result->message = "No data.";
        $result->className = "meh";

        $email = trim(Request::post("email", false, FILTER_SANITIZE_EMAIL));
        $password = trim(Request::post("password"));
        $remember = Request::post("remember");
        $remember = (isset($remember) && ($remember == "yes"));
        $app_key = trim(Request::post("app_key", false, FILTER_SANITIZE_STRING));
        // $captcha = Request::post('g-recaptcha-response');

        // if (!Config::get("debug") && ((empty($captcha) || (!($captcha_check = CaptchaModel::checkCaptcha($captcha)) === true)))) {
        //    $result->message = "You are apparently a robot. Did you forget to tick the box?";
        //} else

        if (!Csrf::validateToken(Request::post("csrf_token"))) {
            $result->message = "Invalid CSRF token. Please refresh and try again. ";

        } elseif ($email == "") {
            $result->message = "Please enter your email address.";

        } elseif (BlacklistModel::isBlacklisted($email)) {
            $result->message = "Sorry, this email domain has been blacklisted and can not be used. Please email us for more information.";
            $result->className = "sad";

        } elseif ($password == "" && RegistrationModel::user_account_already_exists_and_is_usable($email)) { // if email adress exists but they did not enter a password, then generate a password reset token and notify the user
            RegistrationModel::send_password_reset($email, $app_key);
            $result->message = "This account exists! We emailed a password reset link.";
            $result->className = "intermediate";

        } elseif ($password == "" && RegistrationModel::register_new_account_and_send_verification($email, $app_key)) { // insert this as an unverified user, and send them a verification + password combo email
            $result->message = "Welcome! We just sent you a password and activation link.";
            $result->className = "happy";

        } elseif ($email > "" && $password > "") {

                $model = Model::Read("users", "user_email=:email", array(":email" => $email));

                if (isset($model)) {

                    if (!isset($model[0])) {

                            $result->message = "Some of the account details are incorrect; try again after correcting them.";

                    } else {

                        $model = $model[0];
                        $model->last_browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
                        $model->last_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";

                        if (!isset($model->user_activation_hash) && ($model->user_active == 0 || $model->user_deleted == 1)) {

                            $result->message = "Account is disabled, you cannot log in.";

                        } elseif (isset($model->user_activation_hash)) {

                            RegistrationModel::resend_the_existing_account_activation_hash($email, $app_key);
                            $result->message = "This account still needs activation. We just re-sent the activation link.";

                        } elseif (($model->user_failed_logins >= 3) && ($model->user_last_failed_login > (time() - 30))) {

                            if (isset($model->user_password_reset_hash)) {
                                $result->message = "You need to click your password reset link, or wait 30 minutes to try again.";

                            } else {
                                RegistrationModel::send_password_reset($email, $app_key);
                                $result->message = "We just sent a password reset email. It seems you may need it.";

                            }

                        } elseif (!password_verify($password, $model->user_password_hash)) {

                            $model->user_failed_logins = (int) $model->user_failed_logins + 1;
                            $model->user_last_failed_login = time();
                            Model::Update("users","user_id",$model);
                            $result->message = "You got your account details wrong, please try again.";

                        } else {

                            // reset the bits of the model that might have been fiddled during the registration and logon process
                            $model->user_activation_hash = NULL;
                            $model->user_password_reset_hash = NULL;
                            $model->user_failed_logins = 0;
                            $model->user_last_failed_login = NULL;
                            $model->user_suspension_timestamp = NULL;
                            $model->user_password_reset_timestamp = NULL;
                            $model->user_logon_count = (int) $model->user_logon_count + 1;
                            Model::Update("users","user_id",$model);

                            // the actual logon, which updates the session
                            LoginModel::setSuccessfulLoginIntoSession($model->user_id, $model->user_email, $model->user_account_type, null, null, null);

                            // and finally, notify the user (which triggers a reload)
                            $result->message = "You're logged on ... just reloading, won't be a sec ...";
                            $result->positive = true;
                            $result->reload = true;

                        }
                    }
                } else {

                    // i don't want you to know what actually happened, in case I give away the schema
                    $result->message = "The fandangulator glipped through the brushmodique splangefoiler. Try gerpoken der klosenbutten.";

                }

        } else {

            // not sure how you managed to get to this codepath
            $result->message = "¯\_(ツ)_/¯";

        }

        // well the feedback has become a sorry mess
        if ($result->className === "sad") {
            Session::set("feedback_negative", $result->message);
        } else if ($result->className === "happy") {
            Session::set("feedback_positive", $result->message);
        } else if ($result->className === "intermediate") {
            Session::set("feedback_intermediate", $result->message);
        } else {
            Session::set("feedback_meh", $result->message);
        }

        Redirect::to($redirect);
    }

}
