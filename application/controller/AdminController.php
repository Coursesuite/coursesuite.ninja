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
        $model->account = (new AccountModel("id",$user_id))->get_model(true);
        $this->View->renderHandlebars('admin/users/account', $model, "_admin", true);

    }

   public function hax($action = "new", $row_id = 0) {

        $database = DatabaseFactory::getFactory()->getConnection();

        $model = $this->model;

        switch ($action) {
            case "deactivate":
                $query = $database->prepare("UPDATE subscriptions SET status='inactive',statusReason=:r,info=:i,endDate=:d,active=0 WHERE subscription_id=:id");
                $query->execute([
                    ":id" => $row_id,
                    ":d" => date("Y-m-d"),
                    ":i" => "Deactivated by " . Session::CurrentUsername(),
                    ":r" => "deactivated"
                ]);
                Redirect::to("/admin/users/special/");
                break;

            case "add":
                $user_id = AccountModel::quick_create_account_id(Request::post("email"));
                $endDate = Request::post("enddate");
                $ed = empty($endDate) ? null : date("Y-m-d", strtotime($endDate));
                $obj = new dbRow("subscriptions");
                $obj->user_id = $user_id;
                $obj->endDate = empty($endDate) ? null : date("Y-m-d", strtotime($endDate));
                $obj->referenceId = Request::post("reference");
                $obj->subscriptionUrl = null;
                $obj->status = 'active';
                $obj->active = 1;
                $obj->product_id = Request::post("product");
                $obj->save();
                Redirect::to("/admin/users/special/");
                break;

            default:
                $model->enddates = ["", "tomorrow", "+3 days", "+1 week", "+2 weeks", "+1 month", "first day of next month", "+3 months", "+1 year", "first day of next year", "+2 years"];
                $model->products = ProductBundleModel::get_all_models(false);
                $id = strtoupper(UUID::uniqid_base36(true));
                $pos = strlen($id)/2;
                list($beg, $end) = preg_split('/(?<=.{'.$pos.'})/', $id, 2);
                $model->reference = "CUSTOM-$end-$beg";
        }
        $this->View->renderHandlebars('admin/subscriptions/hax', $model, "_admin", true);
    }

    public function users($tab = "recent")
    {

        $database = DatabaseFactory::getFactory()->getConnection();
        $users = array();
        $subsql = "";

        $model = $this->model;
        $model->tab = $tab;
        $model->search = Request::post("q", false, FILTER_SANITIZE_STRING);
        if (strpos($model->search, '*') === false) $model->search .= "%";
        $model->tableheaders = ["id","Email","Last login","Login Count","Browser","Status","Starts","Ends","Reference","Product"];
        switch ($tab) {
            case "special":
                $users = Model::Read("users", "user_id IN (SELECT user_id FROM subscriptions WHERE subscriptionUrl IS NULL) ORDER BY user_last_login_timestamp DESC, user_email");
                $subsql = "SELECT subscription_id, status, added, endDate, referenceId, product_id FROM subscriptions WHERE user_id=:user AND subscriptionUrl IS NULL ORDER BY endDate DESC, added DESC LIMIT 1";
                break;

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
                if (isset($subresult->subscription_id)) $user->subscription_id = $subresult->subscription_id;
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
                    "pricing_description" => Request::post("pricing_description"),
                    "price" => Request::post("price", false, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                    "concurrency" => Request::post("concurrency", false, FILTER_SANITIZE_NUMBER_INT),
                    "app_ids" => implode(',', Request::post("app_ids")),
                    "icon" => Request::post("icon", false, FILTER_SANITIZE_STRING),
                );
                $model->id = Model::Update("product_bundle", "id", $bundle);
                $model->method = "index";
                break;

            case "update-pricing":
                $models = ProductBundleModel::get_all_models(false);
                foreach ($models as $pb) {
                    $key = $pb->product_key;
                    $payload = FastSpringModel::get("/products/{$key}");
                    $json = reset($payload->products); // json.products.0.
                    if ($json->result === "success") {
                        ProductBundleModel::set_price($pb->id,$json->pricing->price->USD);
                    }
                }
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
                        "appSlides" => Text::iif(Request::post("appSlides"), "uk-section cs-app-slides cs-bgcolour-" . Request::post("app_key")),
                        "appLinks" => Text::iif(Request::post("appLinks"), "uk-section cs-app-links"),
                        "appBox" => trim(Request::post("appBox"))
                    ),
                    "documents" => Request::post("documents"), // markdown of urls, downloads, bookmarks
                    "box" => Request::post("box"), //  ALTER TABLE `apps` CHANGE `tutorials` `box` TEXT  CHARACTER SET utf8  COLLATE utf8_general_ci  NULL  DEFAULT NULL;
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

    public function hooks($action = "list", $id = 0)
    {
        $model = $this->model;
        switch ($action) {
            case "subscribe":
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $headers[] = "Authorization: Bearer " . Config::get("CLOUDCONVERT_API_KEY");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_URL, "https://api.cloudconvert.com/hook");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, [
                    "url" => $param,
                    "event" => "finished",
                ]);
                curl_exec($ch);
                curl_close($ch);
                break;

            case "unsubscribe":
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $headers[] = "Authorization: Bearer " . Config::get("CLOUDCONVERT_API_KEY");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_URL, "https://api.cloudconvert.com/hook/" . $id);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                $data = curl_exec($ch);
                curl_close($ch);
                break;

        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers[] = "Authorization: Bearer " . Config::get("CLOUDCONVERT_API_KEY");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, "https://api.cloudconvert.com/hooks");
        $model->list = json_decode(curl_exec($ch),true);
        curl_close($ch);
        $model->stats = HooksModel::stats();
        $this->View->renderHandlebars('admin/hooks/index', $model, "_admin", true);
    }

    public function storeSettings($action = "")
    {
        switch ($action) {
            case "update":
                KeyStore::find("homepage_intro")->put(Request::post("homepage_intro"));

                KeyStore::find("mailchimp_subscribe")->put(Request::post("mailchimp_subscribe"));
                KeyStore::find("mailchimp_stylesheet")->put(Request::post("mailchimp_stylesheet"));

                KeyStore::find("page_footer")->put(Request::post("page_footer"));
                // KeyStore::find("footer_col1")->put(Request::post("footer_col1"));
                // KeyStore::find("footer_col2")->put(Request::post("footer_col2"));
                // KeyStore::find("footer_col3")->put(Request::post("footer_col3"));
                KeyStore::find("freeTrialDays")->put(Request::post("freeTrialDays"));
                KeyStore::find("emailTemplate")->put(Request::post("emailTemplate"));
                KeyStore::find("customcss")->put(Request::post("customcss"));
                KeyStore::find("volumelicence")->put(Request::post("volumelicence"));
                KeyStore::find("apikey_text")->put(Request::post_html("apikey_text"));
                KeyStore::find("sitemap_template")->put(Request::post("sitemap_template"));

                KeyStore::find("pricing_text")->put(Request::post("pricing_text", false));
                $pricing = trim(Request::post("pricing_products"));
                $pricing = str_replace(' ', '', $pricing);
                KeyStore::find("pricing_products")->put($pricing);

                KeyStore::find("head_javascript")->put(Request::post("head_javascript", false));

                Redirect::to("admin/storeSettings"); // to ensure it reloads
                break;
        }

        // $footer_1 = KeyStore::find("footer_col1")->get() ?: Config::get("GLOBAL_FOOTER_COLUMN_1");
        // $footer_2 = KeyStore::find("footer_col2")->get() ?: Config::get("GLOBAL_FOOTER_COLUMN_2");
        // $footer_3 = KeyStore::find("footer_col3")->get() ?: Config::get("GLOBAL_FOOTER_COLUMN_3");

        $model = $this->model;
        $model->page_footer = KeyStore::find("page_footer")->get();
        // $model->footer = [$footer_1, $footer_2, $footer_3];
        $model->homepage_intro = KeyStore::find("homepage_intro")->get();
        $model->emailTemplate = KeyStore::find("emailTemplate")->get();
        $model->freetrialdays = KeyStore::find("freeTrialDays")->get(3);
        $model->customcss = KeyStore::find("customcss")->get();
        $model->volumelicence = KeyStore::find("volumelicence")->get("");
        $model->apikey_text = KeyStore::find("apikey_text")->get();
        $model->head_javascript = KeyStore::find("head_javascript")->get();
        $model->sitemap_template = KeyStore::find("sitemap_template")->get();
        $model->mailchimp_subscribe =KeyStore::find("mailchimp_subscribe")->get();
        $model->mailchimp_stylesheet = KeyStore::find("mailchimp_stylesheet")->get();
        $model->pricing_text = KeyStore::find("pricing_text")->get();
        $model->pricing_products = KeyStore::find("pricing_products")->get();

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

    // uploading the same image twice should yield the same path
    public function uploadFDImage() {
        $rootpath = Config::get("PATH_IMG_MEDIA");
        $fold = md5(file_get_contents('php://input')); // uniqid();
        $fpath = $rootpath . $fold . '/';
        $fname = urldecode($_SERVER['HTTP_X_FILE_NAME']);
        $fname = preg_replace('/\.(?=.*\.)/', '', $fname); // remove all dots except the last
        $fname = preg_replace('/[^a-z0-9_.]/', '', strtolower($fname)); // normalise remaining name
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

    public function blog($method = "index", $entry = 0) {
        $model = $this->model;
        $model->method = $method;
        $model->id = $entry;
        switch ($method) {
            case "delete":
                Model::Destroy("blogentries", "entry_id=:id", [":id"=>$entry]);
                $model->method = "index";
                break;

            case "save":
                $db = new dbRow("blogentries", ["entry_id=:id", [":id"=>$entry]]);
                $db->title = Request::post("title");
                $db->slug = Request::post("slug");
                $db->short_entry = Request::post("short_entry");
                $db->long_entry = Request::post("long_entry");
                $db->meta_description = Request::post("meta_description");
                $db->entry_date = Request::post("entry_date");
                $db->published = Request::post("published");

                $db->card_description = Request::post("card_description");
                $cardIcon = trim(Request::post("card_icon"));
                if (!empty($cardIcon)) {
                    if (strpos($cardIcon,"://")===false) $cardIcon = substr($cardIcon,1);
                    $db->card_icon = Config::get('URL') . $cardIcon;
                } else {
                    $db->card_icon = null;
                }
                $db->card_title = Request::post("card_title");

                $db->save();

                $model->formdata = (new BlogModel($entry))->get_model();
                $model->method = "index";
                break;

            case "new":
                $model->formdata = (new BlogModel())->get_model();
                $model->formdata->entry_date = date_create("now")->format('Y-m-d 00:00:00');
                $model->method = "edit";
                break;

            case "edit":
                $model->formdata = (new BlogModel($entry))->get_model();
                $model->formdata->entry_date = date_create($model->formdata->entry_date)->format('Y-m-d 00:00:00');
                break;

        }

        if ($model->method === "index") {
            $model->index = BlogModel::get_all_models();
        }
        $this->View->renderHandlebars('admin/blog/' . $model->method, $model, "_admin", true);
    }

    public function encoder($type = "view") {
        $value = Request::post("value");
        $model = $this->model;
        $model->enc = "";
        $model->dec = "";
        switch ($type) {
            case "decode":
                $model->enc = $value;
                $model->dec = Encryption::decrypt(Text::base64dec($value));
                break;
            case "encode":
                $model->enc = Text::base64enc(Encryption::encrypt($value));
                $model->dec = $value;
                break;
        }
        $model->method = $type;
        $this->View->renderHandlebars("admin/encoder", $model, "_admin", true);
    }

    public function hasher($type = "view") {
        $model = $this->model;
        switch ($type) {
            case "generate":
                $model->raw = (new Sayable(9))->generate(true);
                $model->hash = password_hash($model->raw, PASSWORD_DEFAULT);
                break;

            case "hash":
                $model->raw = Request::post("value");
                $model->hash = password_hash($model->raw, PASSWORD_DEFAULT);
                break;
        }
        $model->method = $type;
        $this->View->renderHandlebars("admin/hasher", $model, "_admin", true);
    }

    public function sitemap($action = "view") {
        $model = $this->model;
        $model->feedback = "😶 I don't know what you are doing";
        switch ($action) {
            case "rebuild":
                date_default_timezone_set('UTC');
                $json = new stdClass();
                $json->last_timestamp_date = gmdate("Y-m-d\TH:i:s\Z", strtotime(str_replace(['/js/main.','.js'], "", APP_JS)));

                foreach (StaticPageModel::get_all_models() as $content) {
                    $iter = new stdClass();
                    $iter->page = $content->page_key;
                    $iter->lastmod = $json->last_timestamp_date;
                    $contents[] = $iter;
                }
                $json->content = $contents;

                foreach(BlogModel::get_all_models() as $entry) {
                    if ($entry['published']===1) {
                        $iter = new stdClass();
                        $iter->slug = $entry['slug'];
                        $iter->lastmod = gmdate("Y-m-d\TH:i:s\Z", strtotime($entry['entry_date']));
                        $entries[] = $iter;
                    }
                }
                $json->blogentry = $entries;

                $rows = Model::raw("select s.route, a.app_key, a.added from store_sections s join apps a on find_in_set(cast(a.app_id as char), s.app_ids) > 0 order by route, added desc");
                foreach ($rows as $row) {
                    $loop[] = $row->route;
                }
                $loop = array_unique($loop);
                foreach($loop as $iter) {
                    $section = new stdClass();
                    $section->route = $iter;
                    $section->lastmod = $json->last_timestamp_date;
                    foreach ($rows as $row) {
                        if ($row->route === $iter) {
                            $app = new stdClass();
                            $app->app_key = $row->app_key;
                            $app->lastmod = gmdate("Y-m-d\TH:i:s\Z", strtotime($row->added));
                            foreach ((new FilesModel("app",$row->app_key))->get_model() as $file) {
                                $inst = new stdClass();
                                $inst->filename = $file['name'];
                                $inst->lastmod = gmdate("Y-m-d\TH:i:s\Z", strtotime($file['modified']));
                                $app->files[] = $inst;
                            }
                            $section->app[] = $app;
                        }
                    }
                    $sections[] = $section;
                }
                $json->section = $sections;
                $template = KeyStore::find("sitemap_template")->get();
                $xml = $this->View->getHandlbarsString($template, $json);
                file_put_contents(Config::get("PATH_PUBLIC_ROOT") . "sitemap.xml", $xml);
                $model->feedback = "rebuilt <code>sitemap.xml</code> in the root folder";
            $action = "view";
            break;
        }
        $this->View->renderHandlebars("admin/sitemap/{$action}", $model, "_admin", true);

    }


    public function browser() {
        include (Config::get("PATH_VIEW") . "admin/files/image_browser.php");
    }

    public function testemail($action = "view") {
        $model = new stdClass();
        $model->formdata = new stdClass();
        switch ($action) {
            case "send":
                $action = "view";
                $to = Request::post("to");
                $model->formdata->to = $to;
                $mail = new Mail;
                $mail->emailUser($to, "CourseSuite Admin Test Email", "If you can read this, then email is working the way it should be.");

                break;
        }
        $model->action = $action;
        $this->View->renderHandlebars("admin/testing/email", $model, "_admin", true);


    }

}