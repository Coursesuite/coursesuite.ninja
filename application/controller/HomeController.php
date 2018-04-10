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

        $this->View->page_title = "Elearning software tools to build interactive SCORM courses | CourseSuite";
        $this->View->page_keywords = "elearning software, online learning software, online e-learning software, scorm html5, scorm wrapper, CourseSuite, Course Suite";
        $this->View->page_description = "Ninja Suite by CourseSuite are a simple and powerful set of web-bases authoring apps that allow you to rapidly create interactive HTML5-based SCORM courses. Try free for 7 days.";

        $this->View->Requires("home/who_we_are");
        $this->View->Requires("home/featured_products");
        $this->View->Requires("home/testimonials");
        $this->View->Requires("home/trusted_by");
        $this->View->Requires("home/subscribe_link");

        $this->View->Requires("main.js");
        $this->View->Requires("https://cdn-images.mailchimp.com/embedcode/horizontal-slim-10_7.css");

        $this->View->renderHandlebars("home/index", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

}