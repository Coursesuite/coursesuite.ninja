<?php

class FreebiesController extends Controller
{

    // we are being passed the current function and parameters, so remember these in case we need to log on
    public function __construct($current_function = "", ...$params)
    {
        parent::__construct();
        if (Session::userIsLoggedIn()) {
            Session::remove("RedirectTo");
        } else {
            $extra = (is_array($params[0])) ? "/" . implode("/", $params[0]) : "";
            Session::set("RedirectTo", "freebies/$current_function$extra");
        }
    }

    // /moodle or /moodle/index
    public function index()
    {
        $model = StoreModel::get_store_index_model(); // same thing as store
        $this->View->Requires("main.js");
        $this->View->renderHandlebars("freebies/index", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

}
