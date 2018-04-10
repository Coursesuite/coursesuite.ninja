<?php

class ProductsController extends Controller
{

    // we are being passed the current function and parameters, so remember these in case we need to log on
    public function __construct($current_function = "", ...$params)
    {
        parent::__construct();

        // well cron isn't working again, so we shall just bung this in here
        // SubscriptionModel::validateSubscriptions();

        if (Session::userIsLoggedIn()) {
            Session::remove("RedirectTo");
        } else {
            $extra = (is_array($params[0])) ? "/" . implode("/", $params[0]) : "";
            Session::set("RedirectTo", "products/{$current_function}{$extra}");
        }
    }

    // apps/index is a feeder function (created in Application.php), so apps/foo/bar/qwert => apps/index([foo, bar, qwert])
    public function index(...$params)
    {
        list($route, $app_key, $action, $action_value) = array_pad($params, 4, "");

        switch (count($params)) {
            case 1: // products/freebies

                switch ($route) {
                    case "index":
                        $this->View->page_title = "​Apps to build SCORM courses, Free elearning plugins | CourseSuite";
                        $this->View->page_keywords = "moodle plugin, free moodle plugin, moodle auth plugin, wordpress to moodle, moodle sso";
                        $this->View->page_description = "Simple and powerful set of web-based authoring tools plus Free plugins for Moodle. Access for free.";
                    break;

                    case "freebies":
                        $this->View->page_title = "​Free elearning plugins from e-learning industry leaders | CourseSuite";
                        $this->View->page_keywords = "moodle plugin, free moodle plugin, moodle auth plugin, wordpress to moodle, moodle sso";
                        $this->View->page_description = "We’ve been working in e-learning industry for decades so we’ve learned a thing or two about what course developers are looking for. Wordpress to Moodle. Token Enrolment for Moodle. Course catalogue. Access now for free.";
                    break;

                    case "ninja":
                        $this->View->page_title = "Simple and powerful apps to build SCORM courses | CourseSuite";
                        $this->View->page_description = "A simple and powerful set of web-based authoring tools for desktop browsers that allow the rapid creation of intuitive, interactive HTML5based SCORM courses.";
                        $this->View->page_keywords = "elearning software, online learning software, online e-learning software, scorm html5, scorm wrapper, CourseSuite, Course Suite, ninja suite, ninjasuite";
                    break;
                }


                $model = StoreProductsModel::get_store_section_products_model($route);
                $this->View->Requires("products/tile");
                $this->View->Requires("main.js");
                $this->View->renderHandlebars("products/sections", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
                break;

            default: // products/freebies/wp2moodle
                $this->info($app_key, $action, $action_value);
                break;
        }
    }

    public function info($app_key, $action = "", $action_value = "")
    {
        if ($action === "statistics") {
            parent::allowCORS();
            parent::requiresAjax();
            LoggingModel::logInternal(__METHOD__,"statistics", Request::post("app_key",true), Request::post("hash"), file_get_contents("php://input"));
            exit();
        }
        $model = StoreProductsModel::get_store_info_model($app_key, $action, $action_value);

        // $this->View->Requires("login/logon_partial");
        //$this->View->Requires("products/make_slides");
        $this->View->Requires("products/lightbox");
        $this->View->Requires("products/fineprint");
        $this->View->Requires("main.js");

        $this->View->renderHandlebars("products/info", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

    public function changelog($app_key = "docninja") {
        $model = new stdClass();
        // $model->baseurl = Config::get("URL");;
        // $model->app_keys = AppModel::getAllAppKeys();
        // $model->app_key = $app_key;
        // if ($app_key > "") {
        $app_id = Model::ReadColumn("apps", "app_id", "app_key=:key", array(":key"=>$app_key));
        $model->changelog = ChangeLogModel::get_app_changelog($app_id);
        // }
        $this->View->renderHandlebars("products/changelog", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

    // an ajax form post handler
    // public function contact()
    // {
    //     $user_email = Request::post("your-email", true);
    //     $user_name = Request::post("your-name", true);
    //     $message = Request::post("your-message", true);
    //     $captcha = Request::post("g-recaptcha-response");
    //     $mail_sent = false;

    //     if (($captcha_check = CaptchaModel::checkCaptcha($captcha)) === true) {

    //         $mailer = new Mail;
    //         $mail_sent = $mailer->sendMail(Config::get('EMAIL_SUBSCRIPTION'), $user_email, $user_name, "CourseSuite Contact Form", $message);

    //     };

    //     if ($mail_sent) {
    //         $model = array(
    //             "sent" => true, // $mail_sent,
    //             "message" => Text::get("CONTACT_FORM_OK"),
    //         );
    //     } else {
    //         $model = array(
    //             "sent" => false, // $mail_sent,
    //             "message" => Text::get("CONTACT_FORM_SPAM"),
    //         );
    //     }
    //     $this->View->renderJSON($model);

    // }

    // accessed via an ajax get, so the template is null to avoid headers
    // public function contactForm()
    // {
    //     $model = new stdClass();
    //     $model->baseurl = Config::get('URL');
    //     $model->captchakey = Config::get('GOOGLE_CAPTCHA_SITEKEY');
    //     $this->View->renderHandlebars("products/contactForm", $model, null, Config::get('FORCE_HANDLEBARS_COMPILATION'), false);
    // }

}
