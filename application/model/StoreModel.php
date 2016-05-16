<?php

class StoreModel {

/*
    public static function getAllStoreSections($basic = false) {
        $database = DatabaseFactory::getFactory()->getConnection();
        if ($basic) {
	        $sql = "SELECT id, label, epiphet FROM store_sections ORDER BY sort";
        } else {
	        $sql = "SELECT id, label, epiphet, cssclass, html_pre, html_post, visible, sort
                FROM store_sections
                ORDER BY sort";
        }
        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }
*/

    /**
     *  data for the store front - sections contain apps
     */
    public static function getStoreViewModel() {
        $sections = SectionsModel::getAllStoreSections();
        foreach ($sections as &$section) {
            $section->apps = AppModel::getAppsByStoreSection($section->id);
        }
        $model = new stdClass();
        $model->section = $sections;

        if (Session::userIsLoggedIn()) {
            // the logon token is ONLY the session id. other details (tier, etc) are looked by by passing the token back through the authenticated api page
            $model->token = ApiModel::encodeToken(Session::CurrentId());
        }

        // for an app, if the app is in a tier that is higher than the tier that the user is subscribed to, then they need to
        // upgrade their plan before they can access the app.

        $model->upgrade = false;
        $model->storeurl = CONFIG::get('URL') . CONFIG::get('DEFAULT_CONTROLLER') . '/info/';

        return $model;
    }

}