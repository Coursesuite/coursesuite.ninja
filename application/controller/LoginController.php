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
            // echo "redirectto = " . Session::get("RedirectTo");
            // redirect might be set in querystring, or in session, or not at all
            $data = array(
                'redirect' => Request::get('redirect') ? Request::get('redirect') : Session::get("RedirectTo") ? Session::get("RedirectTo") : null,
                'baseurl' => Config::get('URL'),
                'formdata' => Session::get('form_data'),
            );
            $this->View->render('login/index', $data);
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

    /**
     * The login action, when you do login/login
     */
    public function login()
    {
        // check if csrf token is valid
        if (!Csrf::isTokenValid()) {
            LoginModel::logout();
            Redirect::home();
            exit();
        }

        // perform the login method, put result (true or false) into $login_successful
        $login_successful = LoginModel::login(
            Request::post('user_name'), Request::post('user_password'), Request::post('set_remember_me_cookie'), Session::get('discourse_sso'), Session::get('discourse_payload'), Session::get('discourse_signature')
        );

        // check login status: if true, then redirect user to user/index, if false, then to login form again
        if ($login_successful) {
            if (Session::get('discourse_sso')) {
                Redirect::to('login/discourseSSO');
            } elseif (Request::post('redirect')) {
                Redirect::to(ltrim(urldecode(Request::post('redirect')), '/'));
            } else {
                Redirect::to('user/index');
            }
        } else {
            Redirect::to('login/index');
        }
    }

    public function loginSingle()
    {
        $this->View->render('login/loginSingle');
    }

    /**
     * The logout action
     * Perform logout, redirect user to main-page
     */
    public function logout()
    {
        LoginModel::logout();
        Redirect::home();
        exit();
    }

    /**
     * Login with cookie
     */
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

    /**
     * Show the request-password-reset page
     */
    public function requestPasswordReset()
    {
        $this->View->render('login/requestPasswordReset');
    }

    /**
     * The request-password-reset action
     * POST-request after form submit
     */
    public function requestPasswordReset_action()
    {
        $result = PasswordResetModel::requestPasswordReset(Request::post('user_name_or_email'), Request::post('g-recaptcha-response'));
        //if ($result === TRUE) { // all seemed to work; go back to the login page
        $this->View->render('login/requestPasswordReset');
        // Redirect::to('login/index');
        //} else { // stay here so they can fix it
        //    Redirect::to("login/requestPasswordReset");
        //}
    }

    /**
     * Verify the verification token of that user (to show the user the password editing view or not)
     * @param string $user_name username
     * @param string $verification_code password reset verification token
     */
    public function verifyPasswordReset($user_name, $verification_code)
    {
        // check if this the provided verification code fits the user's verification code
        if (PasswordResetModel::verifyPasswordReset($user_name, $verification_code)) {
            // pass URL-provided variable to view to display them
            $this->View->render('login/resetPassword', array(
                'user_name' => $user_name,
                'user_password_reset_hash' => $verification_code,
            ));
        } else {
            Redirect::to('login/index');
        }
    }

    /**
     * Set the new password
     * Please note that this happens while the user is not logged in. The user identifies via the data provided by the
     * password reset link from the email, automatically filled into the <form> fields. See verifyPasswordReset()
     * for more. Then (regardless of result) route user to index page (user will get success/error via feedback message)
     * POST request !
     * TODO this is an _action
     */
    public function setNewPassword()
    {
        PasswordResetModel::setNewPassword(
            Request::post('user_name'), Request::post('user_password_reset_hash'),
            Request::post('user_password_new'), Request::post('user_password_repeat')
        );
        Redirect::to('login/index');
    }

    // check a session to see if it's active
    // http://auth.coursesuite.ninja.dev/login/validateSession/gpac1drc8g62cefakdd7r78737/docninja/
    public function validateSession($encrypted_session_id, $app_id = null)
    {

        $session_id = Encryption::decrypt($encrypted_session_id);

        echo Session::isActiveSession($session_id, $app_id);
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
            Discourse::login($userInfo->user_id, $userInfo->user_email, $userInfo->user_name, Session::get('discourse_payload'), Session::get('discourse_signature'));
        } else {
            Redirect::to('login/index');
        }

    }

    /* ------------------------------------------------------------------------------------------------------------------------------------------------------------

    STORE PAGE INTEGRATED REGISTRATION AND LOGON FORM HANDLER (ajax)

    ------------------------------------------------------------------------------------------------------------------------------------------------------------ */


    // /login/authenticate called using ajax from store/info/app_key -> integrated.hbp
    public function authenticate()
    {

        LoginModel::softLogout(); // to be sure, to be sure

        $result = new stdClass();
        $result->message = "No data.";

        $email = trim(Request::post("email", false, FILTER_SANITIZE_EMAIL));
        $password = trim(Request::post("password"));
        $remember = Request::post("remember");
        $remember = (isset($remember) && ($remember == "yes"));
        $app_key = trim(Request::post("app_key", false, FILTER_SANITIZE_STRING));
        $captcha = Request::post('g-recaptcha-response');

        if (empty($captcha) || (!($captcha_check = CaptchaModel::checkCaptcha($captcha)) === true)) {
            $result->message = "You are apparently a robot. Did you forget to tick the box?";

        } elseif (!Csrf::validateToken(Request::post("csrf_token"))) {
            $result->message = "Form validation error. Please refresh and try again.";

        } elseif ($email == "") {
            $result->message = "Please enter your email address.";

        } elseif ($password == "" && RegistrationModel::user_account_already_exists_and_is_usable($email)) {
            // if email adress exists, then generate a password reset token and notify the user
            RegistrationModel::send_password_reset($email, $app_key);
            $result->message = "This account exists. We emailed a password reset link.";
            $result->classname = "happy";

        } elseif ($password == "" && RegistrationModel::register_new_account_and_send_verification($email)) {
            $result->message = "Welcome! We just sent you a password and activation link.";
            $result->classname = "happy";

        } elseif ($email > "" && $password > "") {
                $model = Model::Read("users", "user_email=:email", array(":email" => $email));
                if (isset($model)) {
                    if (!isset($model[0])) {
                            $result->message = "Some of the account details are incorrect; try again after correcting them.";

                    } else {
                        $model = $model[0];
                        if ($model->user_active == 0 || $model->user_deleted == 1) {
                            $result->message = "Account is disabled, you cannot log in.";
                        } elseif (isset($model->user_activation_hash)) {
                            RegistrationModel::resend_the_existing_account_activation_hash($email, $app_key);
                            $result->message = "Account still needs activation. We just re-sent the activation link.";

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
                            LoginModel::setSuccessfulLoginIntoSession($model->user_id, $model->user_name, $model->user_email, $model->user_account_type, null, null, null);

                            // and finally, notify the user (which triggers a reload)
                            $result->message = "<p class='happy'>You're logged on ... just reloading, won't be a sec ...</p>";
                            $result->positive = true;
                            $result->reload = true;

                        }
                    }
                } else {
                    $result->message = "The fandangulator glipped through the brushmodique splangefoiler.";

                }

        } else {
            // not sure how you managed to get to this codepath
            $result->message = "¯\_(ツ)_/¯";

        }

        // regen the csrf token for the next attempt
        $result->csrf = Csrf::makeToken();
        $this->View->renderJSON($result);
    }

}
