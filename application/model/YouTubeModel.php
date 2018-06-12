<?php

class YouTubeModel { // does not extend Model

	public static function precache($app_key, array $videos) {
		$cache = CacheFactory::getFactory()->getCache();
	    $cacheItem = $cache->getItem("{$app_key}_youtube");
		foreach ($videos as $url) {
			if (empty($url)) continue;
			$json[$app_key] = curl::get_json("https://noembed.com/embed?url=" . urlencode($url));
		}
		$cacheItem->set($json)->addTags(["persistent","youtubeinfo",$app_key]);
		$cache->save($cacheItem);
	}

	public static function getcache($app_key) {
		$cache = CacheFactory::getFactory()->getCache();
	    $cacheItem = $cache->getItem("{$app_key}_youtube");
	    return $cacheItem->get();
	}

}