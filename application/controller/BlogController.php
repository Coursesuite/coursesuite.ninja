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