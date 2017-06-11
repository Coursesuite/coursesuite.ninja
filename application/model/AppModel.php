<?php
/**
 * undocumented class
 *
 * @package default
 * @author
 **/
class AppModel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public static function Make()
    {
        return parent::Create("apps");
    }

    public static function Load($table, $where_clause, $fields)
    {
        return parent::Read($table, $where_clause, $fields);
    }

    public static function Save($table, $idrow_name, $data_model)
    {
        return parent::Update($table, $idrow_name, $data_model);
    }


    public static function getAllAppKeys() {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT app_key, app_id
                FROM apps
                WHERE active = :active and trim(coalesce(launch, '')) <>''
        ";
        $query = $database->prepare($sql);
        $query->execute(array(':active' => true));
        return $query->fetchAll();
    }

    /**
     * get a list of the (active) appkeys (that have an api)
     */
    public static function getAppKeys()
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT app_key, launch
                FROM apps
                WHERE active = :active AND apienabled=1";
        $query = $database->prepare($sql);
        $query->execute(array(':active' => true));
        return $query->fetchAll();
    }

    /**
     * get an associative array of all records in the apps table
     */
    public static function getAllApps($all_fields = true)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        if ($all_fields) {
            $sql = "SELECT app_id, app_key, name, icon, url, launch, auth_type, added, active, status, tagline, whatisit, description, media, meta_keywords, meta_description, meta_title, popular
                    FROM apps
                    ORDER BY name";
        } else {
            $sql = "SELECT app_id, name
                    FROM apps
                    ORDER BY name";
        }
        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    // Get all active apps
    public static function getActiveApps() {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT app_key, name FROM apps WHERE active = 1";
        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    /**
     * get an app by its key (string)
     */
    public static function getAppByKey($app_key)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT app_id, app_key, name, icon, url, launch, auth_type, added, active, status, tagline, whatisit, description, media, meta_keywords, meta_description, meta_title, popular
                FROM apps
                WHERE app_key = :app_key
                LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':app_key' => $app_key));
        return $query->fetch();
    }

    public static function getAppById($app_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT app_id, app_key, name, icon, url, launch, auth_type, added, active, status, tagline, whatisit, description, media, meta_keywords, meta_description, meta_title, popular
                FROM apps
                WHERE app_id = :app_id
                LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':app_id' => $app_id));
        return $query->fetch();
    }

    // not all the record, only that which is related to the app-by-section view
    public static function getAppsByStoreSection($section_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $admin = (Session::get("user_account_type") == 7);
        $active = ($admin == true) ? "" : "AND a.active = 1";
        $sql = "SELECT a.app_id, a.app_key, a.name, a.tagline, a.whatisit, a.icon, a.launch, a.active, a.status, a.auth_type, a.url, a.popular
                FROM apps a INNER JOIN store_section_apps s ON a.app_id = s.app
                WHERE s.section = :section
                $active
                ORDER BY s.sort";
        $query = $database->prepare($sql);
        $query->execute(array(':section' => $section_id));
        return $query->fetchAll();
    }

    public static function getLaunchUrl($app_id, $method = "token", $token = "")
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        if (is_string($app_id)) {
            $sql = "SELECT launch, auth_type, app_key
                    FROM apps
                    WHERE app_key = :app_id LIMIT 1";
        } else {
            $sql = "SELECT launch, auth_type, app_key
                    FROM apps
                    WHERE app_id = :app_id LIMIT 1";
        }
        $query = $database->prepare($sql);
        $query->execute(array(':app_id' => $app_id));
        $row = $query->fetch();

        $url = $row->launch;
        $auth_type = intval($row->auth_type,10);
        $app_key = $row->app_key;
        $launchurl = "";
        $encoded_identity = ApiModel::encodeToken(Session::get('user_id'));

        // well, without extending the app table with some kind of formatter for the launch url ...
        if ($app_key === "coursebuildr") {
            $launchurl = "{$url}data/{$encoded_identity}/";

        } else if ($method == "apikey") { // logging on via portal
            $launchurl = $url . "?apikey=$token";

        } else { // ordinary app launch
            switch ($auth_type) {
                case AUTH_TYPE_DIGEST:
                    $launchurl = Config::get("URL") . "launch/app/" . $query->fetchColumn(2); // unfinished, DO NOT USE
                    break;
                case AUTH_TYPE_TOKEN:
                    // $launchurl = $url . "?token=" . ApiModel::encodeToken(Session::CurrentId());
                    $launchurl = $url . "?data={$encoded_identity}";
                    break;
                case AUTH_TYPE_NONE:
                    $launchurl = $url;
                    break;
            }
        }
        // unset($query);
        return $launchurl;
    }

    public static function app_id_for_key($app_key)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("SELECT app_id FROM apps WHERE app_key = :app_key LIMIT 1");
        $query->execute(array(":app_key" => $app_key));
        return $query->fetch(PDO::FETCH_COLUMN, 0);
    }

} // END class AppModel
