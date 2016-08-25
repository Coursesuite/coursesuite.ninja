<?php

/**
 * RegisterController
 * Register new user
 */
class RegisterController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class. The parent::__construct thing is necessary to
     * put checkAuthentication in here to make an entire controller only usable for logged-in users (for sure not
     * needed in the RegisterController).
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Register page
     * Show the register form, but redirect to main-page if user is already logged-in
     */
    public function index($freeTrial = null)
    {
        if (LoginModel::isUserLoggedIn()) {
            Redirect::home();
        } elseif ($freeTrial) {
            Session::set('free_trial', true);
            $this->View->render('register/freeTrial');
        } else {
            $this->View->render('register/index');
        }
    }

    /**
     * Register page action
     * POST-request after form submit
     */
    public function register_action()
    {
        $registration_successful = RegistrationModel::registerNewUser();

        // if ($registration_successful) {
        Redirect::to('login/index');
        // } else {
        //    Redirect::to('register/index');
        // }
    }

    /**
     * Verify user after activation mail link opened
     * @param int $user_id user's id
     * @param string $user_activation_verification_code user's verification token
     * @param string $user_newsletter subscribed
     */
    public function verify($user_id, $user_activation_verification_code, $user_newsletter_subscribed)
    {
        Session::set("feedback_area", "registration");

        if (isset($user_id) && isset($user_activation_verification_code)) {
            RegistrationModel::verifyNewUser($user_id, $user_activation_verification_code, $user_newsletter_subscribed);
            $this->View->render('login/loginSingle');
             // $this->View->render('register/verify');
        } else {
            Redirect::to('login/index');
        }
    }

    public function reVerify(){
        RegistrationModel::reSendVerificationEmail();
        Redirect::to('login/loginSingle');
    }

    public function freeTrial_action()
    {
        $registration_successful = RegistrationModel::registerNewUser();
        Redirect::to('register/index/freeTrial');
    }

    public function registeredUserTrial(){
        $this->View->render('register/registeredUserTrial');
    }

    public function registeredUserTrial_action()
    {
        SubscriptionModel::giveFreeSubscription(Session::get('user_id'), 1);
        UserModel::setTrialUnavailable(Session::get('user_id'));
        $mail = new Mail;
        $mail->sendMail(Config::get('EMAIL_ADMIN'), Config::get('EMAIL_SUBSCRIPTION'), 'Coursesuite Admin', 'Free trial activated', "User:" . Session::get('user_id') . ", " . Session::get('user_name') . " Just activated their free trial.");
        Redirect::to('user/index');
    }

    /**
     * Generate a captcha, write the characters into $_SESSION['captcha'] and returns a real image which will be used
     * like this: <img src="......./login/showCaptcha" />
     * IMPORTANT: As this action is called via <img ...> AFTER the real application has finished executing (!), the
     * SESSION["captcha"] has no content when the application is loaded. The SESSION["captcha"] gets filled at the
     * moment the end-user requests the <img .. >
     * Maybe refactor this sometime.
     */
    public function showCaptcha()
    {
        CaptchaModel::generateAndShowCaptcha();
    }
}
