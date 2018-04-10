<?php

class NavModel {
	public static function products_nav($route) {
		$cache = CacheFactory::getFactory()->getCache();
	    $cacheItem = $cache->getItem("products_nav_" . $route);
	    $model = $cacheItem->get();
	    if (is_null($model)) {
			$database = DatabaseFactory::getFactory()->getConnection();
			$query = $database->prepare("
            	select a.app_key, a.name, a.tagline, a.glyph from store_sections ss
            	inner join apps a on find_in_set(cast(a.app_id as char), ss.app_ids)
				where ss.route = :route
				and a.`active` = 1
				order by find_in_set(cast(a.app_id as char), ss.app_ids)
			");
			$query->execute(array(":route" => $route));
			$model = $query->fetchAll();
			$cacheItem->set($model)->expiresAfter(86400); // 1 day
			$cache->save($cacheItem);
		}
		return $model;
	}

	public static function admin_examples() {
		$results = [];
		$folder = realpath(dirname(__FILE__) . '/../../') . '/application/config/examples';
// $directory = '/path/to/my/directory';
// $scanned_directory = array_diff(scandir($directory), array('..', '.'));
		foreach(glob("$folder/*.md") as $filename) {
			$result = new stdClass();
			$result->name = $filename;
			$result->path = realpath($filename);
			$results[] = $result;
		}
		return $results;
	}

}