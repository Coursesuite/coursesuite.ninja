<?php
class BlogController extends Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index($entry = null, $page_id = 0, $action = "")
	{

		if ($entry === "index") $entry = null;

		$page_id = intval($page_id);
		$url = Config::get("URL");

		$blog = new BlogModel($entry, $page_id);
		$model = $blog->get_model();
		if (!empty($model->Entries)) {
				$this->View->page_title = "CourseSuite Blog";
		} else {
				$this->View->page_title = $model->title . " : CourseSuite Blog";
		}
		// admins can edit blog posts
		// if (Session::userIsAdmin()) {
		// 	switch ($action) {
		// 		case "create":
		// 			$blog->make();
		// 			$model = $blog->get_model();
		// 			$model["show_editor"] = true;
		// 			break;

		// 		case "edit":
		// 			$model["show_editor"] = true;
		// 			break;

		// 		case "save":
		// 			if (Csrf::isTokenValid()) {
		// 				if ($entry === 0) {
		// 					$blog->make();
		// 					$model = $blog->get_model();
		// 					unset($model["entry_date"]);
		// 				}
		// 				$model["title"] = Request::post("title");
		// 				$model["short_entry"] = Request::post("short_entry");
		// 				$model["long_entry"] = Request::post("long_entry");
		// 				$blog->set_model($model);
		// 				$entry = $blog->save();
		// 			}
		// 			Redirect::to("blog/$entry");
		// 			break;

		// 		case "publish":
		// 			if (Csrf::isTokenValid()) {
		// 				$model["published"] = 1;
		// 				$blog->set_model($model);
		// 				$blog->save();
		// 			}
		// 			Redirect::to("blog/$entry");
		// 			break;
		// 	}

		// 	// only extend the model after changes!
		// 	$model["csrf_token"] = Csrf::makeToken();
		// 	$model["editable"] = true;

		// 	$this->View->Requires("simplemde/simplemde.min.css");
		// 	$this->View->Requires("simplemde/simplemde.min.js");
		// 	$this->View->Requires("Sortable.min.js");
		// 	$this->View->Requires("inline-attachment/inline-attachment.js");
		// 	$this->View->Requires("inline-attachment/codemirror.inline-attachment.js");

		// }

		$model->Pagination = array(
			"page" => $page_id,
			"total" => BlogModel::entry_count(),
			"size" => 10
		);


		$this->View->Requires("Text::Paginator");
		$this->View->Requires("https://coursesuite-ninja.disqus.com/count.js");
		$this->View->renderHandlebars("blog/index", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
	}
}