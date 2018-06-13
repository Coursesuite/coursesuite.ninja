<?php

class ContentController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

   public function index($route)
    {
        $feedback = "";
        $model = new stdClass();
        $page = (new StaticPageModel("page_key", $route))->get_model();
        if ($this->Method === "POST" && Csrf::isTokenValid() && $route === "contact") {
            $email = Request::post("email", false, FILTER_VALIDATE_EMAIL);
            $fullname = Request::post("fullname");
            $phone = Request::post("phone", false, FILTER_VALIDATE_EMAIL);
            $message = Request::post("message", true);
            HelpdeskModel::create_ticket($email, $fullname, $phone, "CourseSuite Contact Form", $message);
            $feedback = "<p class='uk-text-success'>Your message was sent.</p>";
        }
        if (!isset($page->content)) {
            Redirect::to("404");
        }

        // make a csrf token in case the page wants a form; may extend this later
        $tok = Csrf::makeToken();
        $page->content = str_replace(["{{csrf}}","{{feedback}}"],[$tok, $feedback],$page->content); // vsprintf has escaping issues

        $this->View->page_title = $page->meta_title;
        $this->View->page_keywords = $page->meta_keywords;
        $this->View->page_description = $page->meta_description;
        $model->staticPage = $page;
        $this->View->renderHandlebars("content/index", $model, "_templates", Config::get("FORCE_HANDLEBARS_COMPILATION"));
    }

    public function popup($route)
    {
        $model = new stdClass();
        $page = (new StaticPageModel("page_key", $route))->get_model();
        if (!isset($page->content)) {
            Redirect::to("404");
        }
        $this->View->page_title = $page->meta_title;
        $this->View->page_keywords = $page->page_keywords;
        $this->View->page_description = $page->page_description;
        $model->staticPage = $page;
        $this->View->renderHandlebars("content/index", $model, "_overlay", Config::get("FORCE_HANDLEBARS_COMPILATION"));
    }

    // return an image (jpeg) from the base64 encoded url and width.
    // creates the thumb and saves it to disk in the /img/thumbs/ folder
    public function image($path, $width) {

        $path = Text::base64_urldecode($path);

        if (strpos("/", $path) === 0) {
            $path = trim($path,"/");
        }
        if (strpos("://", $path) === false) {
            $path = Config::get("URL") . $path;
        }

        $thumb = "./img/thumb/" . md5($path) . "_$width.jpg";
        if (!file_exists($thumb)) {
            $image = Image::urlThumb($path, $thumb, $width, true);
        }

       header("Content-Type: image/jpeg");
       $size = filesize($thumb);
       header("Content-Length: $size");
       readfile($thumb);

    }

}
