<?php
/**
 * undocumented class
 *
 * @package default
 * @author
 **/
class AppModel {

    /**
     * get a list of the (active) appkeys
     */
    public static function getAppKeys() {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT app_key, launch FROM apps WHERE active = :active";
        $query = $database->prepare($sql);
        $query->execute(array(':active' => TRUE));
        return $query->fetchAll();
    }

    /**
     * get an associative array of all records in the apps table
     */
    public static function getAllApps() {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT app_id, app_key, name, icon, url, launch, auth_type, added, active FROM apps";
        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }


    public static function getAppById($app_id) {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT app_id, app_key, name, icon, url, launch, auth_type, added, active
                FROM apps WHERE app_id = :app_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':app_id' => $app_id));
        return $query->fetch();

    }

} // END class AppModel