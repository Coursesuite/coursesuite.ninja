<?php
/**
 * undocumented class
 *
 * @package default
 * @author
 **/
class AppModel {

    /**
     * get a list of the (active) appkeys (that have an api)
     */
    public static function getAppKeys() {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT app_key, launch FROM apps WHERE active = :active AND apienabled=1";
        $query = $database->prepare($sql);
        $query->execute(array(':active' => TRUE));
        return $query->fetchAll();
    }

    /**
     * get an associative array of all records in the apps table
     */
    public static function getAllApps($all_fields = true) {
        $database = DatabaseFactory::getFactory()->getConnection();
        if ($all_fields) {
	        $sql = "SELECT app_id, app_key, name, icon, url, launch, auth_type, added, active, status, apienabled, tagline, description, media FROM apps ORDER BY name";
        } else {
	        $sql = "SELECT app_id, name FROM apps ORDER BY name";
        }
        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }
    
    /* save the media record for a given app as JSON */
    public static function saveAppMedia($app_id, $data) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("UPDATE apps SET media = :media WHERE app_id = :app_id LIMIT 1");
        $query->execute(array(':app_id' => $app_id, ':media' => json_encode($data)));
    }


    /**
     * get an app by its key (string)
     */
    public static function getAppByKey($app_key) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT app_id, app_key, name, icon, url, launch, auth_type, added, active, status, apienabled, tagline, description, media
                FROM apps WHERE app_key = :app_key LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':app_key' => $app_key));
        return $query->fetch();
    }

    public static function getAppById($app_id) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT app_id, app_key, name, icon, url, launch, auth_type, added, active, status, apienabled, tagline, description, media
                FROM apps WHERE app_id = :app_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':app_id' => $app_id));
        return $query->fetch();
    }

    // not all the record, only that which is related to the app-by-section view
    public static function getAppsByStoreSection($section_id) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT a.app_id, a.app_key, a.name, a.tagline, a.icon, a.launch, a.active, a.status, a.auth_type, a.url
                FROM apps a INNER JOIN store_section_apps s ON a.app_id = s.app
                WHERE s.section = :section ORDER BY s.sort";
        $query = $database->prepare($sql);
        $query->execute(array(':section' => $section_id));
        return $query->fetchAll();
    }
    
    public static function getLaunchUrl($app_id) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT launch
        		FROM apps WHERE app_id = :app_id";
        $query = $database->prepare($sql);
        $query->execute(array(':app_id' => $app_id));
        $url = $query->fetchColumn();
        return $url . "?token=" . ApiModel::encodeToken(Session::CurrentId());
    }

} // END class AppModel