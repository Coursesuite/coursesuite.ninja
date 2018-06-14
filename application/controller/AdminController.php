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

    // public function images($path = "", $action = "view", $fname = "") {
    //     $model = new stdClass();
    //     $rootpath = Config::get("PATH_IMG_MEDIA");
    //     if (!empty($path)) $path = Text::base64dec($path);
    //     $fpath = "{$rootpath}{$path}";
    //     switch ($action) {
    //         case "folder":
    //             if (!file_exists($fpath)) {
    //                 mkdir($fpath,0775,true);
    //                 chmod($fpath,0775);
    //             }
    //             break;

    //         case "upload":
    //             $fname = $_SERVER['HTTP_X_FILE_NAME'];
    //             if (!file_exists($fpath)) {
    //                 mkdir($fpath,0775,true);
    //                 chmod($fpath,0775);
    //             }
    //             if (file_exists($fpath . $fname)) unlink($fpath . $fname);
    //             file_put_contents($fpath . $fname, fopen('php://input', 'r'));
    //             $this->View->renderJSON(["result"=>"ok","filename"=>$fname]);
    //             die();
    //             break;

    //         case "delete":
    //             if (file_exists($fpath . Text::base64dec($fname))) unlink($fpath . Text::base64dec($fname));
    //             break;
    //     }
    //     $model->file = [];
    //     if (file_exists($fpath)) {
    //         $files = array_diff(scandir($fpath),['..','.']);
    //         foreach ($files as $entry) {
    //             if (strpos(mime_content_type($fpath.$entry),"image/")!==false) {
    //                 $file = [
    //                     "name" => $entry,
    //                     "size" => Text::byteConvert(filesize($fpath.$entry)),
    //                     "modified" => date ("M d Y H:i:s.",filemtime($fpath.$entry)),
    //                     "thumb" => "/content/image/" . Text::base64_urlencode("/img/{$path}/{$entry}"). "/100",
    //                 ];
    //                 $gis =getimagesize($fpath.$entry);
    //                 $file["info"] = $gis[0] . 'x' . $gis[1];
    //                 $model->file[] = $file;
    //             }
    //         }
    //     }

    //     // $this->View->Requires("filedrop.js");
    //     $this->View->Requires("filedrop.css");
    //     $this->View->renderHandlebars("admin/files/image", $model, "_overlay", true);

    // }

    public function files($area,$key,$action = "view",$fname="") {

        $model = new stdClass();
        $rootpath = Config::get("PATH_ATTACHMENTS");
        $fpath =  "{$rootpath}{$area}/{$key}/";
        $model->area = $area;
        $model->key = $key;

        switch ($action) {
            case "upload":
                $fname = $_SERVER['HTTP_X_FILE_NAME'];
                if (!file_exists($fpath)) {
                    mkdir($fpath,0775,true);
                    chmod($fpath,0775);
                }
                if (file_exists($fpath . $fname)) unlink($fpath . $fname);
                file_put_contents($fpath . $fname, fopen('php://input', 'r'));
                $this->View->renderJSON(["result"=>"ok","filename"=>$fname]);
                die();
                break;

            case "delete":
                if (file_exists($fpath . Text::base64dec($fname))) unlink($fpath . Text::base64dec($fname));
                break;
        }
        $model->file = [];
        if (file_exists($fpath)) {
            $files = array_diff(scandir($fpath),['..','.']);
            foreach ($files as $entry) {
                $file = [
                    "name" => $entry,
                    "mime" => mime_content_type($fpath.$entry),
                    "size" => Text::byteConvert(filesize($fpath.$entry)),
                    "modified" => date ("M d Y H:i:s.",filemtime($fpath.$entry))
                ];
                if (strpos($file["mime"],"image/")!==false) {
                    $file["thumb"] = "/content/image/" . Text::base64_urlencode("/files/{$area}/{$key}/{$entry}"). "/100";
                    $gis =getimagesize($fpath.$entry);
                    $file["info"] = $gis[0] . 'x' . $gis[1];
                }
                $model->file[] = $file;
            }
        }
        // $this->View->Requires("filedrop.js");
        $this->View->Requires("filedrop.css");
        $this->View->renderHandlebars("admin/files/index", $model, "_overlay", true);
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

    public function crud($table, $action="view") {
        $model = $this->model;
        $model->datatable = Model::Read($table);
        $fields = Model::Columns($table);
        $idname = "";
        foreach ($fields as $field) {
            if (array_key_exists("primary",$field) && $field["primary"]===true) $idname = $field["name"];
        }
        switch ($this->Method) {
            case "PUT": // insert
                parse_str(file_get_contents("php://input"),$post_vars);
                $record = Model::Insert($table, $post_vars);
                die(json_encode(array("fields"=>$post_vars)));

            case "POST"; // update
                $post_vars = (array) $_POST;
                Model::Update($table, $idname, $post_vars, true);
                die();

            case "DELETE": // delete
                parse_str(file_get_contents("php://input"),$post_vars);
                $sql = "DELETE FROM {$table} WHERE `{$idname}`=:value";
                DatabaseFactory::raw($sql, array(":value"=>$post_vars[$idname]));
                die();

        }
        $fields[] = ["type" => "control"];
        $model->fields = $fields;
        $model->selection = $table;
        $this->View->renderHandlebars("admin/crud/index", $model, "_admin", true);
    }

    public function account($user_id) {

        $model = $this->model;
        $model->account = (new AccountModel($user_id))->get_model(true);
        $this->View->renderHandlebars('admin/users/account', $model, "_admin", true);

    }

    public function users($tab = "recent")
    {

        $database = DatabaseFactory::getFactory()->getConnection();
        $users = array();
        $subsql = "";

        $model = $this->model;
        $model->search = Request::post("q", false, FILTER_SANITIZE_STRING);
        if (strpos($model->search, '*') === false) $model->search .= "%";
        $model->tableheaders = ["id","Email","Last login","Login Count","Browser","Status","Starts","Ends","Reference","Product"];
        switch ($tab) {
            case "search":
                $users = Model::Read("users", "user_email LIKE :match", array(":match" => str_replace('*','%', $model->search)));
                $subsql = "SELECT status, added, endDate, referenceId, product_id FROM subscriptions WHERE user_id=:user ORDER BY endDate DESC, added DESC LIMIT 1";
                break;

            case "subscribed":
                $users = Model::Read("users", "user_id IN (SELECT user_id FROM subscriptions WHERE status = 'active') ORDER BY user_last_login_timestamp DESC, user_email");
                $subsql = "SELECT status, added, endDate, referenceId, product_id FROM subscriptions WHERE user_id=:user AND status='active' ORDER BY endDate DESC, added DESC LIMIT 1";
                break;

            case "cancelled":
                $users = Model::Read("users", "user_id IN (SELECT user_id FROM subscriptions WHERE statusReason like 'canceled%') AND user_parent_id=0 ORDER BY user_last_login_timestamp DESC, user_email");
                $subsql = "SELECT status, added, endDate, referenceId, product_id FROM subscriptions WHERE user_id=:user AND statusReason like 'canceled%' ORDER BY endDate DESC, added DESC LIMIT 1";
                break;

            case "inactive":
                $users = Model::Read("users", "user_id IN (SELECT user_id FROM subscriptions WHERE status <> 'active') AND user_parent_id=0 ORDER BY user_last_login_timestamp DESC, user_email");
                $subsql = "SELECT status, statusReason, added, endDate, referenceId, product_id FROM subscriptions WHERE user_id=:user AND status <> 'active' ORDER BY endDate DESC, added DESC LIMIT 1";
                $model->tableheaders = ["id","Email","Last login","Login Count","Browser","Status","Reason","Starts","Ends","Reference","Product"];
                break;

            case "recent":
                $users = Model::Read("users", "1=1 AND user_parent_id=0 ORDER BY user_id DESC LIMIT 25", [], array("user_id", "user_last_login_timestamp","user_email","user_logon_count","last_browser"));
                $subsql = "SELECT status, added, endDate, referenceId, product_id FROM subscriptions WHERE user_id=:user ORDER BY endDate DESC, added DESC LIMIT 1";
                break;

            case "mostactive":
                $users = Model::Read("users", "1=1 AND user_parent_id=0 ORDER BY user_logon_count DESC LIMIT 50", [], array("user_id", "user_last_login_timestamp","user_email","user_logon_count","last_browser"));
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
            $my_user_id = Session::CurrentUserId();
            if ($user->user_id != $my_user_id) {
                $user->impersonate = Text::base64_urlencode(Encryption::encrypt("{$user->user_id},{$my_user_id}"));
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
                $cache = CacheFactory::getFactory()->getCache();
                $cache->deleteItemsByTag("model");
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
                    "icon" => Request::post("icon", false, FILTER_SANITIZE_STRING),
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
                $cache = CacheFactory::getFactory()->getCache();
                $cache->deleteItemsByTag("model");
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
                $cache = CacheFactory::getFactory()->getCache();
                $cache->deleteItemsByTag("model");
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
                    ),
                    "documents" => Request::post("documents"), // markdown of urls, downloads, bookmarks
                    // "tutorials" => Request::post("tutorials"), //  youtube links
                    "signup_form" => Request::post("signup_form"), // mailchimp list signup form
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

                // rebuild css cache and dump out less colour file for development
                AppModel::apps_colours_css(true);

                // rebuild the tutorials info cache
                // if (!empty($app["tutorials"])) {
                //     $tutes = explode("\n", $app["tutorials"]);
                //     YouTubeModel::precache($app["app_key"], $tutes);
                // }

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
                        $app_section->delete($as_id);
                    } else {
                        // content was empty, id = 0, nothing to do
                    }
                    // var_dump([$action, $classname, $content, $id]);
                }
                Redirect::to("admin/apps/edit/" . $model->id);
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
                Redirect::to("admin/static_pages/edit/" . $model->id);
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
                $cache = CacheFactory::getFactory()->getCache();
                $cache->deleteItemsByTag("model");
                $testimonial = array(
                    "id" => $id,
                    "avatar" => Request::post("avatar", false, FILTER_SANITIZE_URL),
                    "name" => Request::post("name", false, FILTER_SANITIZE_STRING),
                    "title" => Request::post("title", false, FILTER_SANITIZE_STRING),
                    "link" => Request::post("link", false, FILTER_SANITIZE_URL),
                    "handle" => Request::post("handle", false, FILTER_SANITIZE_STRING),
                    "entry" => Request::post("entry"),
                    "published" => Request::post("published", false, FILTER_SANITIZE_NUMBER_INT),
                    "sort" => Request::post("sort", false, FILTER_SANITIZE_NUMBER_INT),
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
                KeyStore::find("homepage_intro")->put(Request::post("homepage_intro"));
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
        $model->homepage_intro = KeyStore::find("homepage_intro")->get();
        $model->emailTemplate = KeyStore::find("emailTemplate")->get();
        $model->freetrialdays = KeyStore::find("freeTrialDays")->get(3);
        $model->customcss = KeyStore::find("customcss")->get();
        $model->volumelicence = KeyStore::find("volumelicence")->get("");

        $cache = CacheFactory::getFactory()->getCache();
        $cacheItem = $cache->deleteItem("custom_css");
        $cacheItem = $cache->deleteItem("home_model");

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

    public function uploadFDImage() {
        $rootpath = Config::get("PATH_IMG_MEDIA");
        $fold = uniqid();
        $fpath = $rootpath . $fold . '/';
        $fname = $_SERVER['HTTP_X_FILE_NAME'];
        $serverurl = $fpath . $fname;
        if (!file_exists($fpath)) {
            mkdir($fpath,0775,true);
            chmod($fpath,0775);
        }
        if (file_exists($serverurl)) unlink($serverurl);
        file_put_contents($serverurl, fopen('php://input', 'r'));
        $this->View->renderJSON(["result"=>"ok","filename"=>"/img/{$fold}/{$fname}","url"=>$serverurl]);
    }

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

    public function purge($tag = "coursesuite") {
        $model = $this->model;
        $model->status = [];
        $cache = CacheFactory::getFactory()->getCache();
        if ($tag !== "disk") {
            $status = "Purging object caches for tag `{$tag}` ... ";
            $cache->deleteItemsByTag($tag);
            $status .= "done";
            $model->status[] = $status;
        }
        // handlebars templates
        if ($tag === "disk" || $tag === "coursesuite") {
            $status = "Purging precompiled disk caches ... ";
            array_map('unlink', glob(Config::get("PATH_VIEW_PRECOMPILED") . "*.php"));
            $status .= "done";
            $model->status[] = $status;
        }
        $this->View->renderHandlebars("admin/purge/index", $model, "_admin", true);
    }

    // hidden helper methods

    // cacher lets you explore what is cached (by searching for tags - so tag your caches!)
    public function cacher($tag = "coursesuite", $action = "view") {
        $cache = CacheFactory::getFactory()->getCache();
        switch ($action) {
            case "delete":
                $cache->deleteItemsByTag($tag);
                break;
        }
        $model = $cache->getItemsByTag($tag);
        var_dump(array_keys($model), $model);
        exit;
    }

    // dumpr dumps a ctyped row from the database; without a key it shows you a ctyped empty row
    public function dumpr($table, $key=null, $action = "view") {
        $m = new dbRow($table,$key);
        switch ($action) {
            case "delete":
                $m->delete();
                break;
        }
        foreach ($m->properties() as $property) {
            echo $property;var_dump($m->$property);
        }
    }

}
