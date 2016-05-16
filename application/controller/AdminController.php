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
    public function index() {
	    $this->View->render('admin/index');
    }

    public function allUsers() {
	    $this->View->render('admin/allusers', array(
			    'users' => UserModel::getPublicProfilesOfAllUsers())
	    );
    }

    public function showLog($filter = "",$value = "") {

        $model = array(
            "digest_users" => LoggingModel::uniqueDigestUsers(),
            "filter_value" => $value,
            "messages" => LoggingModel::systemLog($filter,$value),
            "baseurl" =>  Config::get('URL'),
            "sheets" => array("flatpickr.min.css"),
            "scripts" => array("flatpickr.min.js")
        );
	    $this->View->renderHandlebars('admin/messages', $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION') );
    }

    public function editSections($id = 0, $action = "") {
	    $model = array(
		    "baseurl" => Config::get("URL"),
	    );
	    if (is_numeric($id) && $action > "") {
		    $model["id"] = $id;
		    $model["action"] = $action;
		    $model["data"] =  SectionsModel::getStoreSection($id);
	    } else {
		    $model["sections"] = SectionsModel::getAllStoreSections(true);
		}
	    $this->View->renderHandlebars('admin/sections', $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION') );
    }

    public function editApps($id = 0, $action = "", $filename = "") {
	    $model = array(
		    "baseurl" => Config::get("URL"),
			"apps" => AppModel::getAllApps(false),
	    );
	    if (is_numeric($id) && intval($id) > 0 ) {
		    $app = AppModel::getAppById($id);
			$upload_dir = Config::get('PATH_APP_MEDIA') . $app->app_key . '/';
		    switch ($action) {
				case "upload":
					$uplname = basename($_FILES["imageUpload"]["name"]); // name of file only
					$image_ext = pathinfo($uplname, PATHINFO_EXTENSION); // extension
					$tmpname = $_FILES["imageUpload"]["tmp_name"]; // php temporary file

					$display_url = Request::post("url", true);
					$display_caption = Request::post("caption", true);
					$image_colour = "#000000";
					$media = json_decode($app->media);

					if (isset($tmpname) && (getimagesize($tmpname) !== false) && ($image_ext == "jpg" || $image_ext == "png" || $image_ext == "jpeg" || $image_ext == "gif")) { // a file of acceptable type was uploaded
						
						$diskname = md5($uplname) . "." . $image_ext;
						$diskpath = $upload_dir . $diskname;
						$displaypath = '/img/apps/' . $app->app_key . '/' . $diskname;
							
						// thumbnail cache cleanup
						if (file_exists($diskpath)) unlink($diskpath); // delete existing versions including thumbnails
						if (file_exists($diskpath . '_thumb' . Config::get('SLIDE_PREVIEW_WIDTH') . '.jpg')) unlink($diskpath . '_thumb' . Config::get('SLIDE_PREVIEW_WIDTH') . '.jpg');
						if (file_exists($diskpath . '_thumb' . Config::get('SLIDE_THUMB_WIDTH') . '.jpg')) unlink($diskpath . '_thumb' . Config::get('SLIDE_THUMB_WIDTH') . '.jpg');

						// move the file upload into position, creating the position if required
						if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true); //TODO: 0774?
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
							"bgcolor" => "rgb(" .implode(",", $colour). ")",
						);

						// save media model
						AppModel::saveAppMedia($id, $media);

					} else if (isset($display_url)) {
						$thumb = "/img/hqdefault.jpg";
						if (strpos($display_url, "youtu") !== false) {
							$json = json_decode(file_get_contents("http://www.youtube.com/oembed?url=" . $display_url . "&format=json"));
							$display_url = str_replace("watch?=","embed", $display_url);
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
						$media[] = array(
							"video" => $display_url,
							"thumb" => $thumb,
							"caption" => $display_caption,
							"bgcolor" => $image_colour,
						);
						AppModel::saveAppMedia($id, $media);
					}
					Redirect::to("admin/editApps/$id/edit");
					break;
					
				case "delete":
					unlink($upload_dir . $filename);
					Redirect::to("admin/editApps/$id/edit");
					break;

		    }
		    $model["id"] = $id;
		    $model["action"] = $action;
		    $files = array();
		    foreach(glob($upload_dir . '*.{jpg,gif,png}', GLOB_BRACE) as $file) {
			    $files[] = basename($file);
		    }
		    $model["files"] = $files;
		    $model["data"] = $app;
	    }
	    $this->View->renderHandlebars('admin/apps', $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION') );
    }

    
	public function actionAccountSettings() {
		AdminModel::setAccountSuspensionAndDeletionStatus(
			Request::post('suspension'), Request::post('softDelete'), Request::post('user_id')
		);

		Redirect::to("admin");
	}
}
