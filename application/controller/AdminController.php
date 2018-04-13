<?php

class AdminController extends Controller
{

    protected $model;

    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct($action_name)
    {
        parent::__construct(true, $action_name);

        parent::requiresDesktop();

        // special authentication check for the entire controller: Note the check-ADMIN-authentication!
        // All methods inside this controller are only accessible for admins (= users that have role type 7)

        Auth::checkAdminAuthentication();

        // ensure licences are up to date (cron does this anyway)
        Licence::refresh_licencing_info();

        $this->model = new stdClass();
        $this->model->menu = Config::Menu();
    }

    public function index()
    {
        $this->View->renderHandlebars("admin/index", $this->model, "_admin", true);
    }

    public function reports($table) {
        $model = $this->model;
        $model->datatable = Model::Read($table);
        $model->fields = Model::Columns($table);
        $model->editable = false;
        $this->View->renderHandlebars("admin/report", $model, "_admin", true);
    }

    public function crud($table) {
        $model = $this->model;
        $model->datatable = Model::Read($table);
        $fields = Model::Columns($table);
        $fields[] = ["type" => "control"];
        $model->fields = $fields;
        $model->selection = $table;
        $this->View->renderHandlebars("admin/crud/index", $model, "_admin", true);
    }

    public function users($tab = "recent")
    {

        $database = DatabaseFactory::getFactory()->getConnection();
        $users = array();
        $subsql = "";

        $model = $this->model;
        $model->search = Request::post("q", false, FILTER_SANITIZE_STRING);
        $model->tableheaders = ["id","Email","Last login","Login Count","Browser","Status","Starts","Ends","Reference","Product"];

        switch ($tab) {
            case "search":
                $users = Model::Read("users", "user_email LIKE :match", array(":match" => $model->search));
                $subsql = "SELECT status, added, endDate, referenceId, product_id FROM subscriptions WHERE user_id=:user ORDER BY endDate DESC, added DESC LIMIT 1";
                break;

            case "subscribed":
                $users = Model::Read("users", "user_id IN (SELECT user_id FROM subscriptions WHERE status = 'active') ORDER BY user_last_login_timestamp DESC, user_email");
                $subsql = "SELECT status, added, endDate, referenceId, product_id FROM subscriptions WHERE user_id=:user AND status='active' ORDER BY endDate DESC, added DESC LIMIT 1";
                break;

            case "cancelled":
                $users = Model::Read("users", "user_id IN (SELECT user_id FROM subscriptions WHERE statusReason like 'canceled%') ORDER BY user_last_login_timestamp DESC, user_email");
                $subsql = "SELECT status, added, endDate, referenceId, product_id FROM subscriptions WHERE user_id=:user AND statusReason like 'canceled%' ORDER BY endDate DESC, added DESC LIMIT 1";
                break;

            case "inactive":
                $users = Model::Read("users", "user_id IN (SELECT user_id FROM subscriptions WHERE status <> 'active') ORDER BY user_last_login_timestamp DESC, user_email");
                $subsql = "SELECT status, statusReason, added, endDate, referenceId, product_id FROM subscriptions WHERE user_id=:user AND status <> 'active' ORDER BY endDate DESC, added DESC LIMIT 1";
                $model->tableheaders = ["id","Email","Last login","Login Count","Browser","Status","Reason","Starts","Ends","Reference","Product"];
                break;

            case "recent":
                $users = Model::Read("users", "1=1 ORDER BY user_id DESC LIMIT 25", [], array("user_id", "user_last_login_timestamp","user_email","user_logon_count","last_browser"));
                $subsql = "SELECT status, added, endDate, referenceId, product_id FROM subscriptions WHERE user_id=:user ORDER BY endDate DESC, added DESC LIMIT 1";
                break;

            case "mostactive":
                $users = Model::Read("users", "1=1 ORDER BY user_logon_count DESC LIMIT 50", [], array("user_id", "user_last_login_timestamp","user_email","user_logon_count","last_browser"));
                $subsql = "SELECT status, added, endDate, referenceId, product_id FROM subscriptions WHERE user_id=:user ORDER BY endDate DESC, added DESC LIMIT 1";
                break;

            case "current":
                $users = Model::Read("users", "md5(user_id) IN (SELECT `user` FROM logons) ORDER BY user_last_login_timestamp DESC", [], array("user_id", "user_last_login_timestamp","user_email","user_logon_count","last_browser"));
                $subsql = "SELECT status, added, endDate, referenceId, product_id FROM subscriptions WHERE user_id=:user ORDER BY endDate DESC, added DESC LIMIT 1";
                break;
        }
        if (!empty($subsql)) {
            $subquery = $database->prepare($subsql);
        }

        foreach ($users as &$user) {
            $subquery->execute(array(":user" => $user->user_id));
            if ($user->user_id != Session::CurrentUserId()) {
                $user->impersonate = Text::base64_urlencode(Encryption::encrypt($user->user_id));
            }
            if ($subresult = $subquery->fetch()) {
                $user->subscription_status = $subresult->status;
                if ($tab === "inactive") $user->subscription_reason = $subresult->statusReason;
                $user->subscription_starts = $subresult->added;
                $user->subscription_ends = $subresult->endDate;
                $user->order_id = $subresult->referenceId;
                $user->product = (new ProductBundleModel("id", $subresult->product_id))->get_model();
            }
        }
        $model->datatable = $users;
        $this->View->renderHandlebars('admin/users/index', $model, "_admin", true);
    }

    // public function showLog($filter = "", $value = "", $limit = 100, $order_by = "id", $order_dir = "desc")
    // {

    //     $digest_users = LoggingModel::uniqueDigestUsers();
    //     if (empty($value)) $value = $digest_users[0];
    //     $model = array(
    //         "fields" => array("id","method_name", "digest_user", "added", "message", "param0"),
    //         "digest_users" => $digest_users,
    //         "filter_value" => $value,
    //         "order_by" => $order_by,
    //         "order_dir" => $order_dir,
    //         "limit" => $limit,
    //         "syslog" => LoggingModel::systemLog($filter, $value, $limit, $order_by, $order_dir),
    //     );
    //     $this->View->Requires("flatpickr.min.css");
    //     $this->View->Requires("flatpickr.min.js");
    //     $this->View->renderHandlebars('admin/showLog', $model, "_admin", true);
    // }

    // public function editSections($id = 0, $action = "")
    // {
    //     $url = Config::get("URL");
    //     $model = array(
    //         "sections" => SectionsModel::getAllStoreSections(true),
    //     );
    //     if (is_numeric($id) && intval($id) > 0) {
    //         $section = SectionsModel::getStoreSection($id);
    //         $model["action"] = $action;
    //     }
    //     switch ($action) {
    //         case "save":
    //             $section = array(
    //                 "id" => $id,
    //                 "label" => Request::post("label", false, FILTER_SANITIZE_STRING),
    //                 "epiphet" => Request::post("epiphet", false, FILTER_SANITIZE_STRING),
    //                 "cssclass" => Request::post("cssclass", false, FILTER_SANITIZE_STRING),
    //                 "visible" => Request::post("visible", false, FILTER_SANITIZE_NUMBER_INT),
    //                 "sort" => Request::post("sort", false, FILTER_SANITIZE_NUMBER_INT),
    //                 "html_pre" => Request::post("html_pre"),
    //                 "html_post" => Request::post("html_post"),
    //             );
    //             $id = SectionsModel::Save("store_sections", "id", $section);
    //             // $model["action"] = "edit";
    //             // could do this to change the url, but whatever
    //             Redirect::to("admin/editSections");
    //             break;

    //         case "new":
    //             $id = 0;
    //             $model["action"] = "new";
    //             $section = SectionsModel::Make();
    //             break;

    //         case "order":
    //             $table = Request::post("table");
    //             $field = Request::post("field");
    //             $keys = explode(',', Request::post("order")); // the order of ids, top to bottom
    //             $assoc = array_combine($keys, range(0, count($keys) - 1)); // a[1] => 0, a[3] => 1, a[2] => 2, etc
    //             SectionsModel::setOrder($assoc);
    //             exit;
    //             break;

    //     }
    //     $model["id"] = $id;
    //     if (isset($section)) {
    //         $model["data"] = $section;
    //     }
    //     $this->View->Requires("Sortable.min.js");
    //     $this->View->renderHandlebars('admin/editSections', $model, "_admin", true);
    // }

    public function product_bundles($method = "index", $id = 0) {
        $model = $this->model;
        $model->method = $method;
        $model->id = $id;
        switch ($method) {
            case "delete":
                // TODO; check no subscriptions are tied to this bundle
                Model::Destroy("product_bundles","id=:id", [":id" => $id]);
                $model->method = "index";
                break;

            case "save":
                $bundle = array(
                    "id" => $id,
                    "sort" => Request::post("sort", false, FILTER_SANITIZE_NUMBER_INT),
                    "product_key" => Request::post("product_key", false, FILTER_SANITIZE_STRING),
                    "store_url" => Request::post("store_url", false, FILTER_SANITIZE_STRING),
                    "active" => Request::post("active", false, FILTER_SANITIZE_NUMBER_INT),
                    "label" => Request::post("label", false, FILTER_SANITIZE_STRING),
                    "description" => Request::post("description"),
                    "price" => Request::post("price", false, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                    "concurrency" => Request::post("concurrency", false, FILTER_SANITIZE_NUMBER_INT),
                    "app_ids" => implode(',', Request::post("app_ids")),
                );
                $model->id = Model::Update("product_bundle", "id", $bundle);
                $model->method = "index";
                break;

            case "new":
                $model->formdata = (new ProductBundleModel("id", 0))->get_model();
                $model->method = "edit";
                break;

            case "edit":
                $model->formdata = (new ProductBundleModel("id", $id))->get_model();
                break;

        }
        if ($model->method === "index") {
            $model->index =  ProductBundleModel::get_all_models(false);
        } else {
            $model->formdata->Apps = AppModel::getAllApps(true);
            // https://selectize.github.io/selectize.js/
            $this->View->Requires("https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js");
            $this->View->Requires("https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/css/selectize.default.min.css");
            $this->View->Requires("https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/js/standalone/selectize.min.js");
            $this->View->Requires("connected_apps_selectize.js");
        }
        $this->View->renderHandlebars("admin/product_bundles/" . $model->method, $model, "_admin", true);
    }

    public function store_sections($method = "index", $id = 0) {
        $model = $this->model;
        $model->method = $method;
        $model->id = $id;
        switch ($method) {
            case "delete":
                Model::Destroy("store_sections", "id=:id", [":id"=>$id]);
                $model->method = "index";
            break;

            case "save":
                $section = array(
                    "id" => $id,
                    "label" => Request::post("label", false, FILTER_SANITIZE_STRING),
                    "epiphet" => Request::post("epiphet", false, FILTER_SANITIZE_STRING),
                    "cssclass" => Request::post("cssclass", false, FILTER_SANITIZE_STRING),
                    "visible" => Request::post("visible", false, FILTER_SANITIZE_NUMBER_INT),
                    "sort" => Request::post("sort", false, FILTER_SANITIZE_NUMBER_INT),
                    "html_pre" => Request::post("html_pre"),
                    "html_post" => Request::post("html_post"),
                    "route" => Request::post("route", false, FILTER_SANITIZE_STRING),
                    "routeLabel" => Request::post("routeLabel", false, FILTER_SANITIZE_STRING),
                    "app_ids" => implode(',', Request::post("app_ids")),
                    "meta_title" => Request::post("meta_title", false, FILTER_SANITIZE_STRING),
                    "meta_keywords" => Request::post("meta_keywords", false, FILTER_SANITIZE_STRING),
                    "meta_description" => Request::post("meta_description", false, FILTER_SANITIZE_STRING),
                );
                $model->id = SectionsModel::Save("store_sections", "id", $section);
                $model->method = "index";
            break;

            case "edit":
                $model->formdata = (new SectionsModel("id", $id))->get_model(true);
                break;

            case "new":
                $model->formdata = (new SectionsModel("id", 0))->get_model(true);
                $model->method = "edit";
                break;


        }
        if ($model->method === "index") {
            $model->index = SectionsModel::getAllStoreSections(false,false);
        } else {
            $this->View->Requires("https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js");
            // https://selectize.github.io/selectize.js/
            $this->View->Requires("https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/css/selectize.default.min.css");
            $this->View->Requires("https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/js/standalone/selectize.min.js");
            $this->View->Requires("connected_apps_selectize.js");
        }
        $this->View->renderHandlebars("admin/store_sections/" . $model->method, $model, "_admin", true);
    }

    public function apps($method = "index", $id = 0) {
        $model = $this->model;
        $model->method = $method;
        $model->id = $id;
        switch ($method) {
            case "delete":
                Model::Destroy("changelog", "app_id=:id", [":id" => $id]);
                Model::Destroy("app_section", "app_id=:id", [":id" => $id]);
                Model::Destroy("apps","app_id=:id", [":id" => $id]);
                $model->method = "index";
                break;

            case "edit":
                // $model->examples = NavModel::admin_examples();
                $model->formdata = (new AppModel("app_id", $id))->get_model(true);
                $model->formdata->cssproperties = json_decode($model->formdata->cssproperties); // TODO make this automatic
                $model->formdata->ApiMods = ApiModel::get_api_mods(json_decode($model->formdata->mods, true));
                break;

            case "new":
                $model->formdata = (new AppModel("app_id", 0))->get_model(false);
                $model->method = "edit";
                break;

             case "addlog":
                $entry = trim(Request::post("entry"));
                $model->method = "changelog";
                if (!empty($entry)) {
                    $log = array(
                        "app_id" => $id,
                        "value" => $entry
                    );
                    Model::Update("changelog","id",$log);
                }
                // no break - fall through to next action

            case "changelog":
                $model->formdata = (new AppModel("app_id", $id))->get_model(false);
                $model->changelog = ChangeLogModel::get_app_changelog($id);
                break;

            case "save":
                $app = array(
                    "app_id" => $id,
                    "app_key" => Request::post("app_key", false, FILTER_SANITIZE_STRING),
                    "name" => Request::post("name"),
                    "tagline" => Request::post("tagline"),
                    "whatisit" => Request::post("whatisit"),
                    "icon" => Request::post("icon", false, FILTER_SANITIZE_URL),
                    "url" => Request::post("url", false, FILTER_SANITIZE_URL),
                    "launch" => Request::post("launch", false, FILTER_SANITIZE_URL),
                    "guide" => Request::post("guide", false, FILTER_SANITIZE_URL),
                    "auth_type" => Request::post("auth_type", false, FILTER_SANITIZE_NUMBER_INT),
                    "active" => Request::post("active", false, FILTER_SANITIZE_NUMBER_INT),
                    "popular" => Request::post("popular", false, FILTER_SANITIZE_NUMBER_INT),
                    "meta_description" => Request::post("meta_description"),
                    "meta_title" => Request::post("meta_title"),
                    "meta_keywords" => Request::post("meta_keywords"),
                    "colour" => Request::post("colour"),
                    "glyph" => Request::post("glyph"),
                    "media" => Request::post("media"),
                    "cssproperties" => array(
                        "appHeader" => Text::iif(Request::post("appHeader"), "uk-section cs-app-header cs-bgcolour-" . Request::post("app_key") ." uk-light"),
                        "appSlides" => Text::iif(Request::post("appSlides"), "uk-section cs-app-slides"),
                        "appLinks" => Text::iif(Request::post("appLinks"), "uk-section cs-app-links")
                    )
                );
                // persist enabled mods
                $basemods = ApiModel::get_api_mods();
                $mods = Request::post("mods");
                foreach ($basemods as $key => $value) {
                    if (is_array($mods) && in_array($key, $mods)) {
                        $basemods[$key]["enabled"] = true;
                    } else {
                        $basemods[$key]["enabled"] = false;
                    }
                }
                // $app["mods"] = json_encode($basemods, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_NUMERIC_CHECK);
                $app["mods"] = $basemods;
                $model->id = Model::Update("apps", "app_id", $app);
                $model->method = "index";

                $sections = Request::post("section_id");
                $classnames = Request::post("section_classname");
                $contents = Request::post("section_content");
                $colours = Request::post("section_colour");
                foreach ($sections as $i => $value) {
                    $classname = $classnames[$i];
                    $content = $contents[$i];
                    $colour = $colours[$i];
                    $as_id = intval($value,10);
                    $app_section = new AppSectionModel($as_id);
                    if (!empty($content)) {
                        if ($as_id === 0) {
                            // content exists, id does not, make a new record
                            $app_section->make();
                        }
                        $as_model = $app_section->get_model();
                        $as_model->classname = $classname;
                        $as_model->content = $content;
                        $as_model->colour = $colour;
                        $as_model->app_id = $id;
                        $as_model->sort = $i;
                        $app_section->set_model($as_model);
                        $app_section->save();
                    } else if ($as_id > 0) {
                        // "delete" because content is empty but id > 0;
                        $app_section->delete();
                    } else {
                        // content was empty, id = 0, nothing to do
                    }
                    // var_dump([$action, $classname, $content, $id]);
                }
                break;
        }
        if ($model->method === "index") {
            $model->index = AppModel::getAllApps(false);
        } else {
            $this->View->Requires("Sortable.min.js");

            // https://selectize.github.io/selectize.js/
            $this->View->Requires("https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/css/selectize.default.min.css");
            $this->View->Requires("https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/js/standalone/selectize.min.js");
        }
        $this->View->renderHandlebars("admin/apps/" . $model->method, $model, "_admin", true);
    }


    // public function editApps($id = 0, $action = "", $filename = "")
    // {
    //     $url = Config::get("URL");
    //     $model = $this->model;
    //     $model->apps = AppModel::getAllApps(false);

    //     if (is_numeric($id) && intval($id) > 0) {

    //         $app = AppModel::getAppById($id);
    //         $upload_dir = Config::get('PATH_APP_MEDIA') . $app->app_key . '/';
    //         $model["action"] = $action;
    //         $files = array();
    //         foreach (glob($upload_dir . '*.{jpg,gif,png}', GLOB_BRACE) as $file) {
    //             $files[] = basename($file);
    //         }
    //         $model["files"] = $files;

    //     }
    //     switch ($action) {

    //         case "upload":
    //             $uplname = basename($_FILES["imageUpload"]["name"]); // name of file only
    //             $image_ext = pathinfo($uplname, PATHINFO_EXTENSION); // extension
    //             $tmpname = $_FILES["imageUpload"]["tmp_name"]; // php temporary file

    //             $display_url = Request::post("url", true, FILTER_SANITIZE_URL);
    //             $display_caption = Request::post("caption", true, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    //             $make_thumbs = ((null !== Request::post("autothumb")) && (Request::post("autothumb") === "yes"));
    //             $add_slide = ((null !== Request::post("addslide")) && (Request::post("addslide") === "yes"));
    //             $media = json_decode($app->media);

    //             if (isset($tmpname) && !empty($tmpname) && (getimagesize($tmpname) !== false) && ($image_ext == "jpg" || $image_ext == "png" || $image_ext == "jpeg" || $image_ext == "gif")) {

    //                 // a file of acceptable type was uploaded
    //                 $diskname = md5($uplname) . "." . $image_ext;
    //                 $diskpath = $upload_dir . $diskname;
    //                 $displaypath = '/img/apps/' . $app->app_key . '/' . $diskname;

    //                 // thumbnail cache cleanup
    //                 if (file_exists($diskpath)) {
    //                     unlink($diskpath);
    //                 }

    //                 if ($make_thumbs) {
    //                     // delete existing versions including thumbnails
    //                     if (file_exists($diskpath . '_thumb' . Config::get('SLIDE_PREVIEW_WIDTH') . '.jpg')) {
    //                         unlink($diskpath . '_thumb' . Config::get('SLIDE_PREVIEW_WIDTH') . '.jpg');
    //                     }

    //                     if (file_exists($diskpath . '_thumb' . Config::get('SLIDE_THUMB_WIDTH') . '.jpg')) {
    //                         unlink($diskpath . '_thumb' . Config::get('SLIDE_THUMB_WIDTH') . '.jpg');
    //                     }
    //                 }

    //                 // move the file upload into position, creating the position if required
    //                 if (!file_exists($upload_dir)) {
    //                     mkdir($upload_dir, 0777, true); //TODO: 0774?
    //                 }

    //                 move_uploaded_file($tmpname, $diskpath);

    //                 // get base colour
    //                 $colour = Image::getBaseColour($diskpath);

    //                 $media[] = array(
    //                     "preview" => $displaypath . '_thumb' . Config::get('SLIDE_PREVIEW_WIDTH') . '.jpg',
    //                     "image" => $displaypath,
    //                     "thumb" => $displaypath . '_thumb' . Config::get('SLIDE_THUMB_WIDTH') . '.jpg',
    //                     "caption" => $display_caption,
    //                     "bgcolor" => "rgba(" . implode(",", $colour) . ",.5)",
    //                 );

    //                 if ($make_thumbs) {
    //                     // generate standard sized thumbnails
    //                     Image::makeThumbnail($diskpath, $diskpath . '_thumb' . Config::get('SLIDE_PREVIEW_WIDTH'), Config::get('SLIDE_PREVIEW_WIDTH'), Config::get('SLIDE_PREVIEW_HEIGHT'), $colour, false);
    //                     Image::makeThumbnail($diskpath, $diskpath . '_thumb' . Config::get('SLIDE_THUMB_WIDTH'), Config::get('SLIDE_THUMB_WIDTH'), Config::get('SLIDE_THUMB_HEIGHT'), $colour);
    //                 }

    //                 // save media model
    //                 if ($add_slide) {
    //                     AppModel::Save("apps", "app_id", array("app_id" => $id, "media" => json_encode($media)));
    //                 }

    //             } else if (isset($display_url)) {
    //                 $thumb = "/img/hqdefault.jpg";
    //                 if (strpos($display_url, "youtu") !== false) {
    //                     $json = json_decode(file_get_contents("http://www.youtube.com/oembed?url=" . $display_url . "&format=json"));
    //                     // preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $display_url, $matches); // http://sourcey.com/youtube-html5-embed-from-url-with-php/
    //                     preg_match('/.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/', $display_url, $matches);

    //                     $display_url = "https://www.youtube.com/embed/" . $matches[1] . "?rel=0&showinfo=0&iv_load_policy=3&enablejsapi=1"; // https://developers.google.com/youtube/player_parameters

    //                     $image = $json->thumbnail_url; // 480px; we want >= 400, so this is ok
    //                     $thumb = str_replace("hqdefault.jpg", "default.jpg", $json->thumbnail_url); // 120px - see http://stackoverflow.com/a/2068371/1238884

    //                 } else if (strpos($display_url, "vimeo") !== false) {
    //                     $json = json_decode(file_get_contents("https://vimeo.com/api/oembed.json?url=" . $display_url));
    //                     $display_url = "https://player.vimeo.com" . $json->uri;
    //                     $image = "https://i.vimeocdn.com/video/" . $json->video_id . "_" . Config::get('SLIDE_PREVIEW_WIDTH') . ".jpg";
    //                     $thumb = "https://i.vimeocdn.com/video/" . $json->video_id . "_" . Config::get('SLIDE_THUMB_WIDTH') . ".jpg";
    //                     /* split thumbnail_url after last _ and replace with size.format, e.g.
    //                     https://i.vimeocdn.com//video//563138102_120.jpg
    //                     https://i.vimeocdn.com//video//563138102_1280.webp
    //                     https://i.vimeocdn.com//video//563138102_400.png
    //                      */
    //                 }
    //                 // get base colour
    //                 // if ($thumb !== "/img/hqdefault.jpg") {
    //                     $colour = Image::getBaseColour($thumb);
    //                 // } else {
    //                 //    $colour = [127, 127, 127];
    //                 // }

    //                 $media[] = array(
    //                     "preview" => $image,
    //                     "video" => $display_url,
    //                     "thumb" => $thumb,
    //                     "caption" => $display_caption,
    //                     "bgcolor" => "rgba(" . implode(",", $colour) . ",.5)",
    //                 );

    //                 if ($add_slide) {
    //                     AppModel::Save("apps", "app_id", array("app_id" => $id, "media" => json_encode($media)));
    //                 }
    //             }
    //             Redirect::to("admin/editApps/$id/edit");
    //             break;

    //         case "delete":
    //             unlink($upload_dir . $filename);
    //             Redirect::to("admin/editApps/$id/edit");
    //             break;

    //         case "save":
    //             $app = array(
    //                 "app_id" => $id,
    //                 "app_key" => Request::post("app_key", false, FILTER_SANITIZE_STRING),
    //                 "name" => Request::post("name"),
    //                 "tagline" => Request::post("tagline"),
    //                 "whatisit" => Request::post("whatisit"),
    //                 "icon" => Request::post("icon", false, FILTER_SANITIZE_URL),
    //                 "url" => Request::post("url", false, FILTER_SANITIZE_URL),
    //                 "launch" => Request::post("launch", false, FILTER_SANITIZE_URL),
    //                 "guide" => Request::post("guide", false, FILTER_SANITIZE_URL),
    //                 "auth_type" => Request::post("auth_type", false, FILTER_SANITIZE_NUMBER_INT),
    //                 "active" => Request::post("active", false, FILTER_SANITIZE_NUMBER_INT),
    //                 "popular" => Request::post("popular", false, FILTER_SANITIZE_NUMBER_INT),
    //                 "description" => Request::post("description"),
    //                 "media" => Request::post("media"),
    //                 "meta_description" => Request::post("meta_description"),
    //                 "meta_title" => Request::post("meta_title"),
    //                 "meta_keywords" => Request::post("meta_keywords"),
    //                 "colour" => Request::post("colour"),
    //                 "glyph" => Request::post("glyph"),
    //             );
    //             $id = AppModel::Save("apps", "app_id", $app);
    //             $model["action"] = "edit";

    //             $appTiers = Request::post("AppTiers");
    //             foreach ($appTiers as $apt) {
    //                 $apptier = new AppTierModel();
    //                 if (trim($apt["name"]) == "" && $apt["id"] > 0) {
    //                         $apptier->delete($apt["id"]);
    //                 } else if (trim($apt["name"]) > "") {
    //                     if ($apt["id"] == -1) {
    //                         $apptier->make();
    //                     } else {
    //                         $apptier->load($apt["id"]);
    //                     }
    //                     $at_model = $apptier->get_model(false);
    //                     $at_model["app_id"] = $id;
    //                     $at_model["tier_level"] = $apt["level"];
    //                     $at_model["name"] = $apt["name"];
    //                     $at_model["description"] = $apt["desc"];
    //                     $apptier->set_model($at_model);
    //                     $apptier->save();
    //                 }
    //             }

    //             // could do this to change the url, but whatever
    //             // Redirect::to("admin/editApps/$id/edit");
    //             break;

    //         case "new":
    //             $id = 0;
    //             $model["action"] = "new";
    //             $app = AppModel::Make();
    //             break;
    //     }

    //     $model["id"] = $id;
    //     if (isset($app)) {
    //         $model["data"] = $app;
    //         $model["AppTiers"] = AppTierModel::get_tiers($id);
    //     }
    //     $this->View->renderHandlebars('admin/editApps', $model, "_admin", true);
    // }


    public function static_pages($method = "index", $id = 0)
    {
        $model = $this->model;
        $model->method = $method;
        $model->id = $id;
        switch ($method) {
            case "delete":
                Model::Destroy("static_pages", "id=:id", [":id"=>$id]);
                $model->method = "index";
                break;

            case "save":
                $page = array(
                    "id" => $id,
                    "page_key" => Request::post("page_key", true, FILTER_SANITIZE_SPECIAL_CHARS),
                    "body_classes" => Request::post("body_classes", false, FILTER_SANITIZE_STRING),
                    "content" => Request::post("content"),
                    "meta_description" => Request::post("meta_description"),
                    "meta_title" => Request::post("meta_title"),
                    "meta_keywords" => Request::post("meta_keywords"),
                );
                $model->id = Model::Update("static_pages", "id", $page);
                $model->method = "index";
                break;

            case "new":
                $model->formdata = (new StaticPageModel("id", 0))->get_model();
                $model->method = "edit";
                break;

            case "edit":
                $model->formdata = (new StaticPageModel("id", $id))->get_model();
                break;

        }
        if ($model->method === "index") {
            $model->index = StaticPageModel::get_all_models();
        }
        $this->View->renderHandlebars('admin/static_pages/' . $model->method, $model, "_admin", true);
    }

    public function testimonials($method = "index", $id = 0)
    {
        $model = $this->model;
        $model->method = $method;
        $model->id = $id;
        switch ($method) {
            case "delete":
                Model::Destroy("testimonials", "id=:id", [":id"=>$id]);
                $model->method = "index";
                break;

            case "save":
                $testimonial = array(
                    "id" => $id,
                    "avatar" => Request::post("avatar", false, FILTER_SANITIZE_URL),
                    "name" => Request::post("body_classes", false, FILTER_SANITIZE_STRING),
                    "title" => Request::post("title", false, FILTER_SANITIZE_STRING),
                    "entry" => Request::post("entry"),
                    "published" => Request::post("published", false, FILTER_SANITIZE_NUMBER_INT),
                    "link" => Request::post("link", false, FILTER_SANITIZE_URL),
                    "handle" => Request::post("handle", false, FILTER_SANITIZE_STRING),
                );
                $model->id = Model::Update("testimonials", "id", $testimonial);
                $model->method = "index";
                break;

            case "new":
                $model->formdata = (new TestimonialsModel("id", 0))->get_model();
                $model->method = "edit";
                break;

            case "edit":
                $model->formdata = (new TestimonialsModel("id", $id))->get_model();
                break;

        }
        if ($model->method === "index") {
            $model->index = TestimonialsModel::get_all_models();
        }
        $this->View->renderHandlebars('admin/testimonials/' . $model->method, $model, "_admin", true);
    }


    // public function messages($message_id = 0, $action = "", $user_id = 0)
    // {

    //     $baseurl = Config::get("URL");
    //     $model = array(
    //         "action" => $action,
    //         "message_id" => $message_id,
    //         "user_id" => $user_id,
    //     );

    //     switch ($action) {
    //         case "send":
    //             $text = Request::post("text", true);
    //             $level = intval(Request::post("level"));
    //             $expires = Request::post("expires");
    //             if (!empty($expires)) {
    //                 $expires = strtotime($expires);
    //             }

    //             $message_id = MessageModel::notify_user($text, $level, $user_id, $expires);
    //             Redirect::to("admin/messages/$message_id/edit");
    //             break;

    //         case "search":
    //             $q = Request::post("q", true);
    //             $model["q"] = $q;
    //             $model["results"] = UserModel::getUserDataByUserNameOrEmail($q, false, true);
    //             break;

    //         case "select":
    //             $u = UserModel::getPublicProfileOfUser($user_id);
    //             if (empty($u)) {
    //                 $u = new stdClass();
    //                 $u->user_id = 0;
    //                 $u->user_name = "All users";
    //             }
    //             $model["q"] = $u->user_email;
    //             $model["user"] = $u;
    //             break;

    //     }
    //     $this->View->Requires("flatpickr.min.css");
    //     $this->View->Requires("flatpickr.min.js");
    //     $this->View->renderHandlebars('admin/messages', $model, "_admin", true);

    // }

    public function hooks($action = "", $id = 0)
    {
        $model = $this->model;
        switch ($action) {
            case "subscribe":
                $data = Curl::cloudConvertHook("subscribe", Config::get("URL") . "hooks/cloudconvert");
                break;

            case "unsubscribe":
                $data = Curl::cloudConvertHook("unsubscribe", $id);
                break;

        }
        $list = Curl::cloudConvertHook("list");
        $model->list = json_decode($list, true);
        $model->stats = HooksModel::stats();
        $this->View->renderHandlebars('admin/hooks/index', $model, "_admin", true);
    }

    public function storeSettings($action = "")
    {
        switch ($action) {
            case "update":
                KeyStore::find("footer_col1")->put(Request::post("footer_col1"));
                KeyStore::find("footer_col2")->put(Request::post("footer_col2"));
                KeyStore::find("footer_col3")->put(Request::post("footer_col3"));
                KeyStore::find("freeTrialDays")->put(Request::post("freeTrialDays"));
                KeyStore::find("emailTemplate")->put(Request::post("emailTemplate"));
                KeyStore::find("customcss")->put(Request::post("customcss"));
                KeyStore::find("volumelicence")->put(Request::post("volumelicence"));
                Redirect::to("admin/storeSettings"); // to ensure it reloads
                break;
        }

        $footer_1 = KeyStore::find("footer_col1")->get() ?: Config::get("GLOBAL_FOOTER_COLUMN_1");
        $footer_2 = KeyStore::find("footer_col2")->get() ?: Config::get("GLOBAL_FOOTER_COLUMN_2");
        $footer_3 = KeyStore::find("footer_col3")->get() ?: Config::get("GLOBAL_FOOTER_COLUMN_3");

        $model = $this->model;
        $model->footer = [$footer_1, $footer_2, $footer_3];
        $model->emailTemplate = KeyStore::find("emailTemplate")->get();
        $model->freetrialdays = KeyStore::find("freeTrialDays")->get(3);
        $model->customcss = KeyStore::find("customcss")->get();
        $model->volumelicence = KeyStore::find("volumelicence")->get("");

        $cache = CacheFactory::getFactory()->getCache();
        $cacheItem = $cache->deleteItem("custom_css");

        $this->View->renderHandlebars("admin/settings/index", $model, "_admin", true);
    }

    public function uploadMDE() {
        $model = array();
        // $_FILES = Array
        // (
        //     [file] => Array
        //         (
        //             [name] => image-1478751066687.gif
        //             [type] => image/gif
        //             [tmp_name] => /private/var/tmp/phpB6eGOM
        //             [error] => 0
        //             [size] => 1778921
        //         )
        // )
        $uplname = basename($_FILES["file"]["name"]); // name of file only
        $image_ext = pathinfo($uplname, PATHINFO_EXTENSION); // extension
        $tmpname = $_FILES["file"]["tmp_name"]; // php temporary file
        if (isset($tmpname) && !empty($tmpname) && (getimagesize($tmpname) !== false) && ($image_ext == "jpg" || $image_ext == "png" || $image_ext == "jpeg" || $image_ext == "gif")) {
            $upload_dir = Config::get('PATH_IMG_MEDIA');

            $diskname = md5($uplname) . "." . $image_ext;
            $diskpath = $upload_dir . $diskname;

            move_uploaded_file($tmpname, $diskpath);

            $model["filename"] = Config::get('URL') . 'img/' . $diskname;
        };
        $this->View->renderJSON($model);
    }

    // public function editBundles($id = 0, $action= "") {
    //     $url = Config::get("URL");
    //     $model = array(
    //         "action" => $action,
    //         "bundles" => BundleModel::getBundles(),
    //         );
    //     switch ($action) {
    //         case 'edit':
    //             $model["bundle"] = BundleModel::getBundleById($id);
    //             break;

    //         case 'save':
    //             StoreProductModel::save('app_bundles', 'product_id', array(
    //                 "product_id" => $id,
    //                 "display_name" => Request::post('display_name'),
    //                 "description" => Request::post('description'),
    //                 ));
    //             Redirect::to('admin/editBundles');
    //             break;

    //         case 'new':
    //             $model['bundle'] = array('a'=>1);
    //             $model['apps'] = AppModel::getAllApps(false);
    //             break;

    //         case 'create':
    //             $postData = array(
    //                 'apps' => Request::post('apps'),
    //                 'store_name' => Request::post('store_name'),
    //                 'display_name' => Request::post('display_name'),
    //                 'description' => Request::post('description'),
    //                 'active' => Request::post('active'),
    //                 'tier' => Request::post('tier'),
    //             );
    //             if (empty($postData['apps'])) {
    //                 Redirect::to('admin/editBundles');
    //                 break;
    //             } else {
    //                 $active = (empty($postData['active']) ? false : true);
    //                 StoreProductModel::createStoreProduct($postData['store_name'], $active, 2, $postData['tier']);
    //                 $product_id = StoreProductModel::getStoreProductByName($postData['store_name'])->product_id;
    //                 foreach ($postData['apps'] as $app) {
    //                     StoreProductModel::createProductAppLink($app, $product_id);
    //                 }
    //                 BundleModel::createBundle($product_id, $postData['display_name'], $postData['description']);
    //                 Redirect::to('admin/editBundles');
    //                 break;
    //             }
    //             break;

    //         case 'delete':
    //             $model["action"] = "delete";
    //             BundleModel::deleteBundle($id);
    //             Redirect::to('admin/editBundles');
    //             break;
    //     }
    //     $this->View->renderHandlebars("admin/editBundles", $model, "_admin", true);
    // }

    // public function encrypt() {
    //     $val = Request::post("value");
    //     $model = new stdClass();
    //     $model->value = $val;
    //     if (!empty($val)) {
    //         $model->output = Text::base64enc(Encryption::encrypt($val));
    //     }
    //     $this->View->renderHandlebars("admin/encrypt", $model, "_admin", true);
    // }

    // public function checkSSL() {
    //     $ch = curl_init("https://www.howsmyssl.com/a/check");
    //     curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     $server_output = json_decode(curl_exec($ch));
    //     $this->View->renderJSON($server_output, true);
    // }

    // public function editProducts($id = 0, $action = "") {
    //     $model = new stdClass();
    //     $this->View->renderHandlebars("admin/products", $model, "_admin", true);
    // }

    public function trustedby($method = "index") {

        switch ($method) {
            case "save":
                KeyStore::find("trustedby")->put(Request::post("entry"));
                break;
        }

        $model = $this->model;
        $model->formdata = array(
            "entry" => KeyStore::find("trustedby")->get()
        );
        $this->View->renderHandlebars("admin/trustedby/edit", $model, "_admin", true);
    }

    public function changeLog($log_id = 0, $action = "") {
        $url = Config::get("URL");
        $model = array(
            "action" => $action,
        );
        $app_id = Request::post("app_id");
        $entry = trim(Request::post("entry"));
        if ($action == "new" && $entry > "" && $app_id > 0) {
            $new = new ChangeLogModel;
            $new->make();
            $record = $new->get_model();
            unset($record["added"]);
            $record["app_id"] = $app_id;
            $record["value"] = $entry;
            $new->set_model($record);
            $new->save();
            //Redirect::to("/admin/changeLog");
        }
        $model["apps"] = AppModel::getAllAppKeys();
        $model["app_id"] = $app_id;
        $model["entries"] = ChangeLogModel::get_app_changelog($app_id);
        $this->View->renderHandlebars("admin/changelog", $model, "_admin", true);
    }

    public function purge() {

        $model = $this->model;
        $model->status = [];

        $cache = CacheFactory::getFactory()->getCache();

        $status = "Purging object caches ... ";
        $cache->deleteItem("custom_css");
        $cache->deleteItem("app_colours_css");
        $cache->deleteItem("home_model");
        $cache->deleteItem("products_nav_ninja");
        $cache->deleteItem("products_nav_freebies");
        $status .= "done";
        $model->status[] = $status;

        // handlebars templates
        $status = "Purging disk caches ... ";
        array_map('unlink', glob(Config::get("PATH_VIEW_PRECOMPILED") . "*.php"));
        $status .= "done";
        $model->status[] = $status;

        $this->View->renderHandlebars("admin/purge/index", $model, "_admin", true);

    }

}
