<?php

class AdminController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();

        // special authentication check for the entire controller: Note the check-ADMIN-authentication!
        // All methods inside this controller are only accessible for admins (= users that have role type 7)
        Auth::checkAdminAuthentication();
    }

    /**
     * This method controls what happens when you move to /admin or /admin/index in your app.
     */
    public function index()
    {
        $this->View->render('admin/index');
    }

    public function allUsers()
    {
        $this->View->render('admin/allusers', array(
            'users' => UserModel::getPublicProfilesOfAllUsers())
        );
    }

    public function showLog($filter = "", $value = "")
    {

        $model = array(
            "digest_users" => LoggingModel::uniqueDigestUsers(),
            "filter_value" => $value,
            "messages" => LoggingModel::systemLog($filter, $value),
            "baseurl" => Config::get('URL'),
            "sheets" => array("flatpickr.min.css"),
            "scripts" => array("flatpickr.min.js"),
        );
        $this->View->renderHandlebars('admin/messages', $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

    public function editSections($id = 0, $action = "")
    {
        $model = array(
            "baseurl" => Config::get("URL"),
            "sections" => SectionsModel::getAllStoreSections(true),
            "sheets" => array("//cdn.jsdelivr.net/simplemde/latest/simplemde.min.css"),
            "scripts" => array("//cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"),
        );
        if (is_numeric($id) && intval($id) > 0) {
            $section = SectionsModel::getStoreSection($id);
            $model["action"] = $action;
        }
        switch ($action) {
            case "save":
                $section = array(
                    "id" => $id,
                    "label" => Request::post("label", false, FILTER_SANITIZE_STRING),
                    "epiphet" => Request::post("epiphet", false, FILTER_SANITIZE_STRING),
                    "cssclass" => Request::post("cssclass", false, FILTER_SANITIZE_STRING),
                    "visible" => Request::post("visible", false, FILTER_SANITIZE_NUMBER_INT),
                    "sort" => Request::post("sort", false, FILTER_SANITIZE_NUMBER_INT),
                    "html_pre" => Request::post("html_pre"),
                    "html_post" => Request::post("html_post")
                );
                $id = SectionsModel::Save("store_sections", "id", $section);
                // $model["action"] = "edit";
                // could do this to change the url, but whatever
                Redirect::to("admin/editSections");
                break;

            case "new":
                $id = 0;
                $model["action"] = "new";
                $section = SectionsModel::Make();
                break;

         }
        $model["id"] = $id;
        if (isset($section)) {
            $model["data"] = $section;
        }
        $this->View->renderHandlebars('admin/sections', $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

    public function editApps($id = 0, $action = "", $filename = "")
    {

        $model = array(
            "baseurl" => Config::get("URL"),
            "apps" => AppModel::getAllApps(false),
            "sheets" => array("//cdn.jsdelivr.net/simplemde/latest/simplemde.min.css"),
            "scripts" => array("//cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"),
        );
        if (is_numeric($id) && intval($id) > 0) {

            $app = AppModel::getAppById($id);
            $upload_dir = Config::get('PATH_APP_MEDIA') . $app->app_key . '/';
            $model["action"] = $action;
            $files = array();
            foreach (glob($upload_dir . '*.{jpg,gif,png}', GLOB_BRACE) as $file) {
                $files[] = basename($file);
            }
            $model["files"] = $files;

        }
        switch ($action) {

            case "upload":
                $uplname = basename($_FILES["imageUpload"]["name"]); // name of file only
                $image_ext = pathinfo($uplname, PATHINFO_EXTENSION); // extension
                $tmpname = $_FILES["imageUpload"]["tmp_name"]; // php temporary file

                $display_url = Request::post("url", true, FILTER_SANITIZE_URL);
                $display_caption = Request::post("caption", true, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $media = json_decode($app->media);

                if (isset($tmpname) && (getimagesize($tmpname) !== false) && ($image_ext == "jpg" || $image_ext == "png" || $image_ext == "jpeg" || $image_ext == "gif")) {
                    // a file of acceptable type was uploaded
                    $diskname = md5($uplname) . "." . $image_ext;
                    $diskpath = $upload_dir . $diskname;
                    $displaypath = '/img/apps/' . $app->app_key . '/' . $diskname;

                    // thumbnail cache cleanup
                    if (file_exists($diskpath)) {
                        unlink($diskpath);
                    }
                    // delete existing versions including thumbnails
                    if (file_exists($diskpath . '_thumb' . Config::get('SLIDE_PREVIEW_WIDTH') . '.jpg')) {
                        unlink($diskpath . '_thumb' . Config::get('SLIDE_PREVIEW_WIDTH') . '.jpg');
                    }

                    if (file_exists($diskpath . '_thumb' . Config::get('SLIDE_THUMB_WIDTH') . '.jpg')) {
                        unlink($diskpath . '_thumb' . Config::get('SLIDE_THUMB_WIDTH') . '.jpg');
                    }

                    // move the file upload into position, creating the position if required
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true); //TODO: 0774?
                    }

                    move_uploaded_file($tmpname, $diskpath);

                    // get base colour
                    $colour = Image::getBaseColour($diskpath);

                    // generate standard sized thumbnails
                    Image::makeThumbnail($diskpath, $diskpath . '_thumb' . Config::get('SLIDE_PREVIEW_WIDTH'), Config::get('SLIDE_PREVIEW_WIDTH'), Config::get('SLIDE_PREVIEW_HEIGHT'), $colour);
                    Image::makeThumbnail($diskpath, $diskpath . '_thumb' . Config::get('SLIDE_THUMB_WIDTH'), Config::get('SLIDE_THUMB_WIDTH'), Config::get('SLIDE_THUMB_HEIGHT'), $colour);

                    $media[] = array(
                        "image" => $displaypath,
                        "thumb" => $displaypath . '_thumb' . Config::get('SLIDE_THUMB_WIDTH') . '.jpg',
                        "preview" => $displaypath . '_thumb' . Config::get('SLIDE_PREVIEW_WIDTH') . '.jpg',
                        "caption" => $display_caption,
                        "bgcolor" => "rgba(" . implode(",", $colour) . ",.5)",
                    );

                    // save media model
                    AppModel::Save("apps", "app_id", array("app_id" => $id, "media" => json_encode($media)));

                } else if (isset($display_url)) {
                    $thumb = "/img/hqdefault.jpg";
                    if (strpos($display_url, "youtu") !== false) {
                        $json = json_decode(file_get_contents("http://www.youtube.com/oembed?url=" . $display_url . "&format=json"));
                        preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $display_url, $matches); // http://sourcey.com/youtube-html5-embed-from-url-with-php/
                        $display_url = "https://www.youtube.com/embed/" . $matches[1] . "?rel=0&showinfo=0&iv_load_policy=3"; // https://developers.google.com/youtube/player_parameters
                        $thumb = str_replace("hqdefault.jpg", "default.jpg", $json->thumbnail_url); // http://stackoverflow.com/a/2068371/1238884

                    } else if (strpos($display_url, "vimeo") !== false) {
                        $json = json_decode(file_get_contents("https://vimeo.com/api/oembed.json?url=" . $display_url));
                        $display_url = "https://player.vimeo.com" . $json->uri;
                        $thumb = "https://i.vimeocdn.com/video/" . $json->video_id . "_" . Config::get('SLIDE_THUMB_WIDTH') . ".jpg";
                        /* split thumbnail_url after last _ and replace with size.format, e.g.
                    https://i.vimeocdn.com//video//563138102_120.jpg
                    https://i.vimeocdn.com//video//563138102_1280.webp
                    https://i.vimeocdn.com//video//563138102_400.png
                     */
                    }
                    // get base colour
                    if ($thumb !== "/img/hqdefault.jpg") {
                        $colour = Image::getBaseColour($thumb);
                    } else {
                        $colour = [127, 127, 127];
                    }

                    $media[] = array(
                        "video" => $display_url,
                        "thumb" => $thumb,
                        "caption" => $display_caption,
                        "bgcolor" => "rgba(" . implode(",", $colour) . ",.5)",
                    );

                    AppModel::Save("apps", "app_id", array("app_id" => $id, "media" => json_encode($media)));
                }
                Redirect::to("admin/editApps/$id/edit");
                break;

            case "delete":
                unlink($upload_dir . $filename);
                Redirect::to("admin/editApps/$id/edit");
                break;

            case "save":
                $app = array(
                    "app_id" => $id,
                    "app_key" => Request::post("app_key", false, FILTER_SANITIZE_STRING),
                    "name" => Request::post("name"),
                    "tagline" => Request::post("tagline"),
                    "icon" => Request::post("icon", false, FILTER_SANITIZE_URL),
                    "url" => Request::post("url", false, FILTER_SANITIZE_URL),
                    "launch" => Request::post("launch", false, FILTER_SANITIZE_URL),
                    "feed" => Request::post("feed", false, FILTER_SANITIZE_URL),
                    "auth_type" => Request::post("auth_type", false, FILTER_SANITIZE_NUMBER_INT),
                    "active" => Request::post("active", false, FILTER_SANITIZE_NUMBER_INT),
                    "description" => Request::post("description"),
                    "media" => Request::post("media"),
                    "meta_description" => Request::post("meta_description"),
                    "meta_title" => Request::post("meta_title"),
                    "meta_keywords" => Request::post("meta_keywords"),
                );
                $id = AppModel::Save("apps", "app_id", $app);
                $model["action"] = "edit";
                // could do this to change the url, but whatever
                // Redirect::to("admin/editApps/$id/edit");
                break;

            case "new":
                $id = 0;
                $model["action"] = "new";
                $app = AppModel::Make();
                break;
        }

        $model["id"] = $id;
        if (isset($app)) {
            $model["data"] = $app;
        }

        $this->View->renderHandlebars('admin/apps', $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

    public function actionAccountSettings()
    {
        AdminModel::setAccountSuspensionAndDeletionStatus(
	        Request::post('user_id'),
            Request::post('suspension'),
            Request::post('softDelete'), 
            Request::post('hardDelete'), 
            Request::post('manualActivation')
        );

        Redirect::to("admin");
    }
    
    public function manualSubscribe() {
        $model = array(
            "baseurl" => Config::get("URL"),
            "users" => UserModel::getAllUsers(),
            "tiers" => TierModel::getAllTiers(true),
            "feedback" => Session::get("feedback_positive")
        );
		Session::set("feedback_positive", null);
        $this->View->renderHandlebars('admin/manualSubscribe', $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
	    
    }
    
    public function actionManualSubscribe() {
	    // addSubscription($userid, $tierid, $endDate, $referenceId, $status, $statusReason, $testMode);
	    $userid = (int) Request::post('user_id');
	    $tierid = (int) Request::post('tier_id');
	    $value = SubscriptionModel::addSubscription(
	    	$userid,
	    	$tierid,
	    	null,
	    	'manually created by admin',
	    	'active',
	    	'',
	    	1);
	    Session::add('feedback_positive', "user $userid was manually subscribed to tier $tierid in test mode; result: $value");
	    Redirect::to("admin/manualSubscribe");
    }
    
    public function staticPage($id = 0, $action = "") {
        $model = array(
            "baseurl" => Config::get("URL"),
            "action" => $action,
            "sheets" => array("//cdn.jsdelivr.net/simplemde/latest/simplemde.min.css"),
            "scripts" => array("//cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"),
		);

        if (is_numeric($id) && intval($id) > 0) {
            $data = StaticPageModel::getRecord($id);
	    } else {
	    	$model["records"] = StaticPageModel::getAll();
	    }

	    switch ($action) {
		    case "save":
		    	$data = array(
                	"id" => $id,
                	"page_key" => Request::post("page_key", true, FILTER_SANITIZE_SPECIAL_CHARS),
                	"body_classes" => Request::post("body_classes", true, FILTER_SANITIZE_STRING),
                	"content" => Request::post("content"),
                    "meta_description" => Request::post("meta_description"),
                    "meta_title" => Request::post("meta_title"),
                    "meta_keywords" => Request::post("meta_keywords"),
                );
                StaticPageModel::Save("id", $data);
                $model["action"] = "edit";
		    	break;

            case "new":
                $id = 0;
                $model["action"] = "new";
                $data = StaticPageModel::Make();
                break;

	    }

        $model["id"] = $id;
        if (isset($data)) {
            $model["data"] = $data;
        }

	    $this->View->renderHandlebars('admin/staticPages', $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }
}
