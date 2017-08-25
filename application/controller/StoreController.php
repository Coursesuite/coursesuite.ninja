<?php

class StoreController extends Controller
{

    // we are being passed the current function and parameters, so remember these in case we need to log on
    public function __construct($current_function = "", ...$params)
    {
        parent::__construct();
        if (Session::userIsLoggedIn()) {
            Session::remove("RedirectTo");
        } else {
            $extra = (is_array($params[0])) ? "/" . implode("/", $params[0]) : "";
            Session::set("RedirectTo", "store/$current_function$extra");
        }
    }

    // /store or /store/index
    public function index()
    {
        $model = StoreModel::get_store_index_model();
        $this->View->renderHandlebars("store/index", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

    // /store/info/docninja
    public function info($app_key, $action = "", $action_value = "")
    {
        $model = StoreModel::get_store_info_model($app_key, $action, $action_value);
        $this->View->renderHandlebars("store/info", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

    // an ajax form post handler
    public function contact()
    {
        $user_email = Request::post("your-email", true);
        $user_name = Request::post("your-name", true);
        $message = Request::post("your-message", true);
        $captcha = Request::post("g-recaptcha-response");
        $mail_sent = false;

        if (($captcha_check = CaptchaModel::checkCaptcha($captcha)) === true) {

            $mailer = new Mail;
            $mail_sent = $mailer->sendMail(Config::get('EMAIL_SUBSCRIPTION'), $user_email, $user_name, "CourseSuite Contact Form", $message);

        };

        if ($mail_sent) {
            $model = array(
                "sent" => true, // $mail_sent,
                "message" => Text::get("CONTACT_FORM_OK"),
            );
        } else {
            $model = array(
                "sent" => false, // $mail_sent,
                "message" => Text::get("CONTACT_FORM_SPAM"),
            );
        }
        $this->View->renderJSON($model);

    }

    // accessed via an ajax get, so the template is null to avoid headers
    public function contactForm()
    {
        $model = new stdClass();
        $model->baseurl = Config::get('URL');
        $model->captchakey = Config::get('GOOGLE_CAPTCHA_SITEKEY');
        $this->View->renderHandlebars("store/contactForm", $model, null, Config::get('FORCE_HANDLEBARS_COMPILATION'), false);
    }

}
