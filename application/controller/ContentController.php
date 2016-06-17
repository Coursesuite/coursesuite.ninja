<?php

class ContentController extends Controller {
	
    public function __construct() {
        parent::__construct();
    }
    
    public function index($route) {
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
	        "App" => $meta // because I did header wrong, deal with it
	    );
	    $this->View->renderHandlebars("content/index", $model, "_templates", Config::get("FORCE_HANDLEBARS_COMPILATION"));
    }
    
}
