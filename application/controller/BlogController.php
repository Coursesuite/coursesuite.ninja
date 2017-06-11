<?php

class BlogController extends Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index($entry = 0, $page = 0, $action = "")
	{
		$page = intval($page);
		$entry = intval($entry);
		$url = Config::get("URL");

		$blog = new BlogModel($entry, $page);
		$model = $blog->get_model();

		// admins can edit blog posts
		if (Session::userIsLoggedIn() && Session::get("user_account_type") == 7) {
			switch ($action) {
				case "create":
					$blog->make();
					$model = $blog->get_model();
					$model["show_editor"] = true;
					break;

				case "edit":
					$model["show_editor"] = true;
					break;

				case "save":
					if (Csrf::isTokenValid()) {
						if ($entry === 0) {
							$blog->make();
							$model = $blog->get_model();
							unset($model["entry_date"]);
						}
						$model["title"] = Request::post("title");
						$model["short_entry"] = Request::post("short_entry");
						$model["long_entry"] = Request::post("long_entry");
						$blog->set_model($model);
						$entry = $blog->save();
					}
					Redirect::to("blog/$entry");
					break;

				case "publish":
					if (Csrf::isTokenValid()) {
						$model["published"] = 1;
						$blog->set_model($model);
						$blog->save();
					}
					Redirect::to("blog/$entry");
					break;
			}

			// only extend the model after changes!
			$model["csrf_token"] = Csrf::makeToken();
			$model["editable"] = true;
			$model["sheets"] = array($url . "js/simplemde/simplemde.min.css");
			$model["scripts"] = array($url . "js/simplemde/simplemde.min.js", "Sortable.min.js", $url . "js/inline-attachment/inline-attachment.js", $url. "js/inline-attachment/codemirror.inline-attachment.js");

		}

		$model["baseurl"] = $url;
		$model["Pagination"] = array(
			"page" => $page,
			"total" => BlogModel::entry_count(),
			"size" => 10
		);

		$this->View->renderHandlebars("blog/index", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
	}
}