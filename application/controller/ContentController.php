<?php

class ContentController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($route)
    {
        $model = new stdClass();
        $page = (new StaticPageModel("page_key", $route))->get_model();
        if (!isset($page->content)) {
            Redirect::to("404");
        }
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
