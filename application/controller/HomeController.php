<?php

class HomeController extends Controller
{

    public function __construct($current_function = "", ...$params)
    {
        parent::__construct();

        if ($current_function !== "") {
            $app = StoreProductsModel::find_store_route_for_app($current_function);
            if (!empty($app)) {
                Redirect::to($app);
                die();
            }
        }

        if (Session::userIsLoggedIn()) {
            Session::remove("RedirectTo");
        } else {
            $extra = (is_array($params[0])) ? "/" . implode("/", $params[0]) : "";
            Session::set("RedirectTo", "home/$current_function$extra");
        }
    }

    // /store or /store/index
    public function index()
    {

        $model = HomeModel::get_model();

        $model->login_label = Config::get("FASTSPRING_CONTEXTUAL_STORE") ? "login here" : "register / login";

        $this->View->page_title = KeyStore::find("DEFAULT_META_TITLE")->get(Config::get('DEFAULT_META_TITLE')); // "Elearning software tools to build interactive SCORM courses | CourseSuite";
        $this->View->page_keywords = KeyStore::find("DEFAULT_META_KEYWORDS")->get(Config::get('DEFAULT_META_KEYWORDS')); // "elearning software, online learning software, online e-learning software, scorm html5, scorm wrapper, CourseSuite, Course Suite";
        $this->View->page_description = KeyStore::find("DEFAULT_META_DESCRIPTION")->get(Config::get('DEFAULT_META_DESCRIPTION')); // "Ninja Suite by CourseSuite are a simple and powerful set of web-bases authoring apps that allow you to rapidly create interactive HTML5-based SCORM courses. Try free for 7 days.";

        $this->View->Requires("home/who_we_are");
        $this->View->Requires("home/featured_products");
        $this->View->Requires("home/testimonials");
        $this->View->Requires("home/trusted_by");
        $this->View->Requires("home/subscribe_link");

        $this->View->Requires("main.js");
        $this->View->Requires(KeyStore::find("mailchimp_stylesheet")->get());

        $this->View->renderHandlebars("home/index", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

}