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
        if (Session::get("user_account_type") == 7) { // admin
	        foreach ($sections as &$section) {
		        $section->visible = 1; // can see all sections
		    }
        }
        $model->baseurl = Config::get("URL");

        if (Session::userIsLoggedIn()) { // associate the apps the user has subscribed to with the launch tokens for these apps
	        $token = ApiModel::encodeToken(Session::CurrentId()); // auth token for launching apps
	        $current_sub = SubscriptionModel::getAllSubscriptions(Session::CurrentUserId(), false, true);
	        if (count($current_sub) > 0) { // I have a subscription
		        if ($current_sub->status == "active") { // it is active
			        $appTokens = array();
					// match the ids in my current subscription on the apps we are rendering;
					// apply tokens for the app ids that we are subscribed to
					// TODO: crawl under a rock after coding this ...
					if (isset($current_sub->tier->app_ids)) { // unfortunately, you may have an active subscription which has detached from a tier somehow... this will break the model
				        foreach ($current_sub->tier->app_ids as $tier) {
					        foreach ($model->section as $sec) {
						        foreach($sec->apps as $ap) {
							        if ($ap->app_id == $tier) {
								        $appTokens[] = array(
								        	"app_id" => $tier,
								        	"token" => $token
								        );
							        }
						        }
					        }
				        }
			        }
			        $model->appTokens = $appTokens;
		        }
	        }
        }

        $model->upgrade = false;
        $model->storeurl = CONFIG::get('URL') . CONFIG::get('DEFAULT_CONTROLLER') . '/info/';
        return $model;
    }

}