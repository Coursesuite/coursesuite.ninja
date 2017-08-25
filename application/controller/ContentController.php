<?php

class ContentController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($route)
    {
        $page = StaticPageModel::getRecordByKey($route);
        if ($page == false) {
            Redirect::to("404");
        }
        $meta = new stdClass();
        $meta->meta_description = $page->meta_description;
        $meta->meta_keywords = $page->meta_keywords;
        $meta->meta_title = $page->meta_title;
        $model = array(
            "baseurl" => Config::get("URL"),
            "staticPage" => $page,
            "App" => $meta, // because I did header wrong, deal with it
        );
        $this->View->renderHandlebars("content/index", $model, "_templates", Config::get("FORCE_HANDLEBARS_COMPILATION"));
    }

    public function popup($route)
    {
        $page = StaticPageModel::getRecordByKey($route);
        if ($page == false) {
            Redirect::to("404");
        }
        $meta = new stdClass();
        $meta->meta_description = $page->meta_description;
        $meta->meta_keywords = $page->meta_keywords;
        $meta->meta_title = $page->meta_title;
        $model = array(
            "baseurl" => Config::get("URL"),
            "staticPage" => $page,
            "App" => $meta, // because I did header wrong, deal with it
        );
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
