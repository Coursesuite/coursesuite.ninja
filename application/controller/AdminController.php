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

    public function allUsers($action = "")
    {

        $q = null;
        $mr = true;

        switch ($action) {
            case "search":
                $q = Request::post("q", true);
                $mr = false;
                break;

        }

        $this->View->render('admin/allusers', array(
            'users' => UserModel::getPublicProfilesOfAllUsers($q, $mr))
        );
    }

    public function showLog($filter = "", $value = "")
    {

        $model = array(
            "digest_users" => LoggingModel::uniqueDigestUsers(),
            "filter_value" => $value,
            "syslog" => LoggingModel::systemLog($filter, $value),
            "baseurl" => Config::get('URL'),
            "sheets" => array("flatpickr.min.css"),
            "scripts" => array("flatpickr.min.js"),
        );
        $this->View->renderHandlebars('admin/syslog', $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

    public function editSections($id = 0, $action = "")
    {
        $url = Config::get("URL");
        $model = array(

            "baseurl" => $url,
            "sections" => SectionsModel::getAllStoreSections(true),
            "sheets" => array($url . "js/simplemde/simplemde.min.css"),
            "scripts" => array($url . "js/simplemde/simplemde.min.js", "Sortable.min.js", $url . "js/inline-attachment/inline-attachment.js", $url. "js/inline-attachment/codemirror.inline-attachment.js"),
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
                    "html_post" => Request::post("html_post"),
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

            case "order":
                $table = Request::post("table");
                $field = Request::post("field");
                $keys = explode(',', Request::post("order")); // the order of ids, top to bottom
                $assoc = array_combine($keys, range(0, count($keys) - 1)); // a[1] => 0, a[3] => 1, a[2] => 2, etc
                SectionsModel::setOrder($assoc);
                exit;
                break;

        }
        $model["id"] = $id;
        if (isset($section)) {
            $model["data"] = $section;
        }
        $this->View->renderHandlebars('admin/sections', $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

    public function editTiers($id = 0, $action = "")
    {
        $url = Config::get("URL");
        $model = array(
            "baseurl" => $url,
            "tiers" => TierModel::getAllTiers(),
            "sheets" => array($url . "js/simplemde/simplemde.min.css"),
            "scripts" => array($url . "js/simplemde/simplemde.min.js", $url . "js/inline-attachment/inline-attachment.js", $url. "js/inline-attachment/codemirror.inline-attachment.js"),
        );
        if (is_numeric($id) && intval($id) > 0) {
            $tier = TierModel::getTierById($id, false);
            $model["action"] = $action;
        }
        switch ($action) {
            case 'save':
                $tier = array(
                    "tier_id" => $id,
                    "tier_level" => Request::post("tier_level", false, FILTER_SANITIZE_NUMBER_INT),
                    "name" => Request::post("name", false, FILTER_SANITIZE_STRING),
                    "description" => Request::post("description", false, FILTER_SANITIZE_STRING),
                    "store_url" => Request::post("store_url", false, FILTER_SANITIZE_URL),
                    "active" => Request::post("active", false, FILTER_SANITIZE_NUMBER_INT),
                    "price" => Request::post("price", false, FILTER_SANITIZE_NUMBER_INT),
                    "currency" => Request::post("currency", false, FILTER_SANITIZE_STRING),
                    "period" => Request::post("period", false, FILTER_SANITIZE_STRING),
                    "pack_id" => Request::post("pack_id", false, FILTER_SANITIZE_NUMBER_INT),
                );
                $id = TierModel::save("tiers", "tier_id", $tier);
                Redirect::to("admin/editTiers");
                break;

            case 'new':
                $id = 0;
                $model["action"] = "new";
                $tier = TierModel::make('tiers');
                break;
        }

        $model["id"] = $id;
        if (isset($tier)) {
            $model["data"] = $tier;
        }
        $this->View->renderHandlebars('admin/tiers', $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }


    public function editApps($id = 0, $action = "", $filename = "")
    {
        $url = Config::get("URL");
        $model = array(
            "baseurl" => $url,
            "apps" => AppModel::getAllApps(false),
            "sheets" => array($url . "js/simplemde/simplemde.min.css"),
            "scripts" => array($url . "js/simplemde/simplemde.min.js", $url . "js/inline-attachment/inline-attachment.js", $url. "js/inline-attachment/codemirror.inline-attachment.js"),
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
                $make_thumbs = ((null !== Request::post("autothumb")) && (Request::post("autothumb") === "yes"));
                $add_slide = ((null !== Request::post("addslide")) && (Request::post("addslide") === "yes"));
                $media = json_decode($app->media);

                if (isset($tmpname) && !empty($tmpname) && (getimagesize($tmpname) !== false) && ($image_ext == "jpg" || $image_ext == "png" || $image_ext == "jpeg" || $image_ext == "gif")) {

                    // a file of acceptable type was uploaded
                    $diskname = md5($uplname) . "." . $image_ext;
                    $diskpath = $upload_dir . $diskname;
                    $displaypath = '/img/apps/' . $app->app_key . '/' . $diskname;

                    // thumbnail cache cleanup
                    if (file_exists($diskpath)) {
                        unlink($diskpath);
                    }

                    if ($make_thumbs) {
                        // delete existing versions including thumbnails
                        if (file_exists($diskpath . '_thumb' . Config::get('SLIDE_PREVIEW_WIDTH') . '.jpg')) {
                            unlink($diskpath . '_thumb' . Config::get('SLIDE_PREVIEW_WIDTH') . '.jpg');
                        }

                        if (file_exists($diskpath . '_thumb' . Config::get('SLIDE_THUMB_WIDTH') . '.jpg')) {
                            unlink($diskpath . '_thumb' . Config::get('SLIDE_THUMB_WIDTH') . '.jpg');
                        }
                    }

                    // move the file upload into position, creating the position if required
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true); //TODO: 0774?
                    }

                    move_uploaded_file($tmpname, $diskpath);

                    // get base colour
                    $colour = Image::getBaseColour($diskpath);

                    $media[] = array(
                        "preview" => $displaypath . '_thumb' . Config::get('SLIDE_PREVIEW_WIDTH') . '.jpg',
                        "image" => $displaypath,
                        "thumb" => $displaypath . '_thumb' . Config::get('SLIDE_THUMB_WIDTH') . '.jpg',
                        "caption" => $display_caption,
                        "bgcolor" => "rgba(" . implode(",", $colour) . ",.5)",
                    );

                    if ($make_thumbs) {
                        // generate standard sized thumbnails
                        Image::makeThumbnail($diskpath, $diskpath . '_thumb' . Config::get('SLIDE_PREVIEW_WIDTH'), Config::get('SLIDE_PREVIEW_WIDTH'), Config::get('SLIDE_PREVIEW_HEIGHT'), $colour, false);
                        Image::makeThumbnail($diskpath, $diskpath . '_thumb' . Config::get('SLIDE_THUMB_WIDTH'), Config::get('SLIDE_THUMB_WIDTH'), Config::get('SLIDE_THUMB_HEIGHT'), $colour);
                    }

                    // save media model
                    if ($add_slide) {
                        AppModel::Save("apps", "app_id", array("app_id" => $id, "media" => json_encode($media)));
                    }

                } else if (isset($display_url)) {
                    $thumb = "/img/hqdefault.jpg";
                    if (strpos($display_url, "youtu") !== false) {
                        $json = json_decode(file_get_contents("http://www.youtube.com/oembed?url=" . $display_url . "&format=json"));
                        // preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $display_url, $matches); // http://sourcey.com/youtube-html5-embed-from-url-with-php/
                        preg_match('/.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/', $display_url, $matches);

                        $display_url = "https://www.youtube.com/embed/" . $matches[1] . "?rel=0&showinfo=0&iv_load_policy=3&enablejsapi=1"; // https://developers.google.com/youtube/player_parameters

                        $image = $json->thumbnail_url; // 480px; we want >= 400, so this is ok
                        $thumb = str_replace("hqdefault.jpg", "default.jpg", $json->thumbnail_url); // 120px - see http://stackoverflow.com/a/2068371/1238884

                    } else if (strpos($display_url, "vimeo") !== false) {
                        $json = json_decode(file_get_contents("https://vimeo.com/api/oembed.json?url=" . $display_url));
                        $display_url = "https://player.vimeo.com" . $json->uri;
                        $image = "https://i.vimeocdn.com/video/" . $json->video_id . "_" . Config::get('SLIDE_PREVIEW_WIDTH') . ".jpg";
                        $thumb = "https://i.vimeocdn.com/video/" . $json->video_id . "_" . Config::get('SLIDE_THUMB_WIDTH') . ".jpg";
                        /* split thumbnail_url after last _ and replace with size.format, e.g.
                        https://i.vimeocdn.com//video//563138102_120.jpg
                        https://i.vimeocdn.com//video//563138102_1280.webp
                        https://i.vimeocdn.com//video//563138102_400.png
                         */
                    }
                    // get base colour
                    // if ($thumb !== "/img/hqdefault.jpg") {
                        $colour = Image::getBaseColour($thumb);
                    // } else {
                    //    $colour = [127, 127, 127];
                    // }

                    $media[] = array(
                        "preview" => $image,
                        "video" => $display_url,
                        "thumb" => $thumb,
                        "caption" => $display_caption,
                        "bgcolor" => "rgba(" . implode(",", $colour) . ",.5)",
                    );

                    if ($add_slide) {
                        AppModel::Save("apps", "app_id", array("app_id" => $id, "media" => json_encode($media)));
                    }
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
                    "whatisit" => Request::post("whatisit"),
                    "icon" => Request::post("icon", false, FILTER_SANITIZE_URL),
                    "url" => Request::post("url", false, FILTER_SANITIZE_URL),
                    "launch" => Request::post("launch", false, FILTER_SANITIZE_URL),
                    "auth_type" => Request::post("auth_type", false, FILTER_SANITIZE_NUMBER_INT),
                    "active" => Request::post("active", false, FILTER_SANITIZE_NUMBER_INT),
                    "popular" => Request::post("popular", false, FILTER_SANITIZE_NUMBER_INT),
                    "description" => Request::post("description"),
                    "media" => Request::post("media"),
                    "meta_description" => Request::post("meta_description"),
                    "meta_title" => Request::post("meta_title"),
                    "meta_keywords" => Request::post("meta_keywords"),
                );
                $id = AppModel::Save("apps", "app_id", $app);
                $model["action"] = "edit";

                $appTiers = Request::post("AppTiers");
                foreach ($appTiers as $apt) {
                    $apptier = new AppTierModel();
                    if (trim($apt["name"]) == "" && $apt["id"] > 0) {
                            $apptier->delete($apt["id"]);
                    } else if (trim($apt["name"]) > "") {
                        if ($apt["id"] == -1) {
                            $apptier->make();
                        } else {
                            $apptier->load($apt["id"]);
                        }
                        $at_model = $apptier->get_model(false);
                        $at_model["app_id"] = $id;
                        $at_model["tier_level"] = $apt["level"];
                        $at_model["name"] = $apt["name"];
                        $at_model["description"] = $apt["desc"];
                        $apptier->set_model($at_model);
                        $apptier->save();
                    }
                }

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
            $model["AppTiers"] = AppTierModel::get_tiers($id);
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
            Request::post('manualActivation'),
            Request::post('logonCap')
        );

        Redirect::to("admin/allUsers");
    }

    public function manualSubscribe()
    {
        $model = array(
            "baseurl" => Config::get("URL"),
            "users" => UserModel::getAllUsers(),
            "tiers" => TierModel::getAllTiers(true),
            "feedback" => Session::get("feedback_positive"),
        );
        Session::set("feedback_positive", null);
        $this->View->renderHandlebars('admin/manualSubscribe', $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));

    }

    public function actionManualSubscribe()
    {
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

    public function staticPage($id = 0, $action = "")
    {
        $url = Config::get("URL");
        $model = array(
            "baseurl" => $url,
            "action" => $action,
            "sheets" => array($url . "js/simplemde/simplemde.min.css"),
            "scripts" => array($url . "js/simplemde/simplemde.min.js", $url . "js/inline-attachment/inline-attachment.js", $url. "js/inline-attachment/codemirror.inline-attachment.js"),
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

    public function messages($message_id = 0, $action = "", $user_id = 0)
    {

        $baseurl = Config::get("URL");
        $model = array(
            "baseurl" => $baseurl,
            "action" => $action,
            "message_id" => $message_id,
            "user_id" => $user_id,
            "sheets" => array($baseurl . "js/simplemde/simplemde.min.css", "$baseurl/css/flatpickr.min.css"),
            "scripts" => array($baseurl . "js/simplemde/simplemde.min.js", "$baseurl/js/flatpickr.min.js", "$baseurl/js/inline-attachment/inline-attachment.js", "$baseurl/js/inline-attachment/codemirror.inline-attachment.js"),
        );

        switch ($action) {
            case "send":
                $text = Request::post("text", true);
                $level = intval(Request::post("level"));
                $expires = Request::post("expires");
                if (!empty($expires)) {
                    $expires = strtotime($expires);
                }

                $message_id = MessageModel::notify_user($text, $level, $user_id, $expires);
                Redirect::to("admin/messages/$message_id/edit");
                break;

            case "search":
                $q = Request::post("q", true);
                $model["q"] = $q;
                $model["results"] = UserModel::getUserDataByUserNameOrEmail($q, false, true);
                break;

            case "select":
                $u = UserModel::getPublicProfileOfUser($user_id);
                if (empty($u)) {
                    $u = new stdClass();
                    $u->user_id = 0;
                    $u->user_name = "All users";
                }
                $model["q"] = $u->user_email;
                $model["user"] = $u;
                break;

        }

//        $this->View->renderJSON($model);

        $this->View->renderHandlebars('admin/messages', $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));

    }

    public function testNotifies()
    {
        MessageModel::notify_user("You got a new notification from admin, and it's a good one", MESSAGE_LEVEL_HAPPY, 11);
        MessageModel::notify_user("Your credit card has expired and you're now booted out :(", MESSAGE_LEVEL_SAD, 11);
        MessageModel::notify_all("Hey, you are all a bunch of people.", MESSAGE_LEVEL_MEH, time() + 60);

        $this->View->output("I have added a couple of notifications...");
    }

    public function manageHooks($action = "", $id = 0)
    {
        switch ($action) {
            case "subscribe":
                $data = Curl::cloudConvertHook("subscribe", Config::get("URL") . "hooks/cloudconvert");
                break;

            case "unsubscribe":
                $data = Curl::cloudConvertHook("unsubscribe", $id);
                break;

        }
        $list = Curl::cloudConvertHook("list");
        $model = array(
            "baseurl" => Config::get("URL"),
            "action" => $action,
            "list" => json_decode($list, true),
            "stats" => HooksModel::stats(),
        );
        $this->View->renderHandlebars("admin/manageHooks", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));

    }

    public function storeSettings($action = "")
    {
        $url = Config::get("URL");
        switch ($action) {
            case "update":
                KeyStore::find("freetrial")->put(Request::post("freetrial"));
                KeyStore::find("howelse")->put(Request::post("howelse"));
                KeyStore::find("freetriallabel")->put(Request::post("freetriallabel"));
                KeyStore::find("tiersystem")->put(Request::post("tiersystem"));

                KeyStore::find("footer_col1")->put(Request::post("footer_col1"));
                KeyStore::find("footer_col2")->put(Request::post("footer_col2"));
                KeyStore::find("footer_col3")->put(Request::post("footer_col3"));

                KeyStore::find("purchasesystem")->put(Request::post("purchasesystem"));
                KeyStore::find("nopurchases")->put(Request::post("nopurchases"));

                KeyStore::find("contactform")->put(Request::post("contactform"));
                KeyStore::find("freeTrialHeader")->put(Request::post("freeTrialHeader"));
                KeyStore::find("freeTrialDescription")->put(Request::post("freeTrialDescription"));

                KeyStore::find("freeTrialDays")->put(Request::post("freeTrialDays"));

                Redirect::to("admin/storeSettings"); // to ensure it reloads

                break;
        }

        $footer_1 = KeyStore::find("footer_col1")->get() ?: Config::get("GLOBAL_FOOTER_COLUMN_1");
        $footer_2 = KeyStore::find("footer_col2")->get() ?: Config::get("GLOBAL_FOOTER_COLUMN_2");
        $footer_3 = KeyStore::find("footer_col3")->get() ?: Config::get("GLOBAL_FOOTER_COLUMN_3");

        // if (!empty($action)) exit(); // no flush
        $model = array(
            "baseurl" => Config::get("URL"),
            "freetrial" => KeyStore::find("freetrial")->get(),
            "freetriallabel" => KeyStore::find("freetriallabel")->get(),
            "howelse" => KeyStore::find("howelse")->get(),
            "tiersystem" => KeyStore::find("tiersystem")->get(),
            "footer" => array($footer_1, $footer_2, $footer_3),
            "purchasesystem" => KeyStore::find("purchasesystem")->get(),
            "nopurchases" => KeyStore::find("nopurchases")->get(),
            "contactform" => KeyStore::find("contactform")->get(),
            "freetrialheader" => KeyStore::find("freeTrialHeader")->get(),
            "freetrialdescription" => KeyStore::find("freeTrialDescription")->get(),
            "freetrialdays" => KeyStore::find("freeTrialDays")->get(3),

            "sheets" => array($url . "js/simplemde/simplemde.min.css"),
            "scripts" => array($url . "js/simplemde/simplemde.min.js", $url . "js/inline-attachment/inline-attachment.js", $url. "js/inline-attachment/codemirror.inline-attachment.js"),

        );
        $this->View->renderHandlebars("admin/storeSettings", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

    public function mailTemplates() {
        $record = KeyStore::find("emailTemplate");
        $postback = trim(Request::post("content"));
        $url = Config::get("URL");

        if (!empty($postback)) {
            $record->put($postback);
        }

        $model = array(
            "baseurl" => $url,
            "content" => $record->get(),
            "sheets" => array($url . "js/simplemde/simplemde.min.css"),
            "scripts" => array($url . "js/simplemde/simplemde.min.js", $url . "js/inline-attachment/inline-attachment.js", $url. "js/inline-attachment/codemirror.inline-attachment.js"),
        );

        $this->View->renderHandlebars("admin/mailTemplates", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));

    }

    public function whiteLabelling($org_id = 0, $app_key = "", $action = "") {
        $url = Config::get("URL");
        $model = array(
                "baseurl" => $url,
                "orgs" => OrgModel::getAll(),
                "apps" => AppModel::getAllAppKeys(),
                "action" => $action,
                "sheets" => array($url . "js/simplemde/simplemde.min.css"),
                "scripts" => array($url . "js/simplemde/simplemde.min.js", $url . "js/inline-attachment/inline-attachment.js", $url. "js/inline-attachment/codemirror.inline-attachment.js"),
        );
        switch($action) {
            case "edit":
                $model["selected_app_key"] = $app_key;
                $model["selected_org_id"] = $org_id;
                $org_model = OrgModel::getRecord($org_id);
                $tmp = json_decode($org_model->header);
                if (array_key_exists($app_key, $tmp)) {
                    $org_model->header = $tmp->$app_key;
                } else {
                    $org_model->header = "";
                }
                $tmp = json_decode($org_model->css);
                if (array_key_exists($app_key, $tmp)) {
                    $org_model->css = $tmp->$app_key;
                } else {
                    $org_model->css = "";
                }
                $model["selected_org"] = $org_model;
                break;

            case "save":
                $model = OrgModel::getRecord($org_id);
                $header = json_decode($model->header);
                $header->$app_key = Request::post("header");
                $model->header = json_encode($header, JSON_NUMERIC_CHECK);
                $css = json_decode($model->css);
                $css->$app_key = Request::post("css");
                $model->css = json_encode($css, JSON_NUMERIC_CHECK);
                OrgModel::Save($model);
                Redirect::to('admin/whiteLabelling');

                break;
        }

        $this->View->renderHandlebars("admin/whiteLabel", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));

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

    public function editBundles($id = 0, $action= "") {
        $url = Config::get("URL");
        $model = array(
            "baseUrl" => $url,
            "action" => $action,
            "bundles" => BundleModel::getBundles(),
            "sheets" => array($url . "js/simplemde/simplemde.min.css"),
            "scripts" => array($url . "js/simplemde/simplemde.min.js", $url . "js/inline-attachment/inline-attachment.js", $url. "js/inline-attachment/codemirror.inline-attachment.js"),
            );
        switch ($action) {
            case 'edit':
                $model["bundle"] = BundleModel::getBundleById($id);
                break;

            case 'save':
                StoreProductModel::save('app_bundles', 'product_id', array(
                    "product_id" => $id,
                    "display_name" => Request::post('display_name'),
                    "description" => Request::post('description'),
                    ));
                Redirect::to('admin/editBundles');
                break;

            case 'new':
                $model['bundle'] = array('a'=>1);
                $model['apps'] = AppModel::getAllApps(false);
                break;

            case 'create':
                $postData = array(
                    'apps' => Request::post('apps'),
                    'store_name' => Request::post('store_name'),
                    'display_name' => Request::post('display_name'),
                    'description' => Request::post('description'),
                    'active' => Request::post('active'),
                    'tier' => Request::post('tier'),
                );
                if (empty($postData['apps'])) {
                    Redirect::to('admin/editBundles');
                    break;
                } else {
                    $active = (empty($postData['active']) ? false : true);
                    StoreProductModel::createStoreProduct($postData['store_name'], $active, 2, $postData['tier']);
                    $product_id = StoreProductModel::getStoreProductByName($postData['store_name'])->product_id;
                    foreach ($postData['apps'] as $app) {
                        StoreProductModel::createProductAppLink($app, $product_id);
                    }
                    BundleModel::createBundle($product_id, $postData['display_name'], $postData['description']);
                    Redirect::to('admin/editBundles');
                    break;
                }
                break;

            case 'delete':
                $model["action"] = "delete";
                BundleModel::deleteBundle($id);
                Redirect::to('admin/editBundles');
                break;
        }
        $this->View->renderHandlebars("admin/editBundles", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

    public function Subscribers() {
        $model = AdminModel::CurrentSubscribersModel();
        $model["baseUrl"] = Config::get("URL");
        $this->View->renderHandlebars("admin/CurrentSubscribers", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

}
