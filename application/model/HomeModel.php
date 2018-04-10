<?php

class HomeModel
{

	// index page model, cached for 1 day
	public static function get_model()
	{
		$cache = CacheFactory::getFactory()->getCache();
	    $cacheItem = $cache->getItem("home_model");
	    $model = $cacheItem->get();
	    if (is_null($model)) {
			$model = new stdClass();
			$popular = array();
			$apps = Model::Read("apps", "popular=1 AND active=1", [], "app_id, app_key, name, tagline, icon", false);
			foreach ($apps as $row) {
				$sm = (new SectionsModel("app_id", $row->app_id))->get_model();
				$row->route = $sm->route;
				$row->label = $sm->label;
				$popular[] = $row;
			}
			$model->Popular = $popular;
			$model->Testimonials = Model::Read("testimonials", "published=1");
			$model->Trust = preg_split('/\R/', KeyStore::find("trustedby")->get()); // explodes \n or \r or \r\n or whatever - in php, that's /\R/, see http://www.pcre.org/pcre.txt
			$cacheItem->set($model)->expiresAfter(86400); // 1 day
			$cache->save($cacheItem);
		}
		return $model;
	}
}
