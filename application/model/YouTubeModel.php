<?php

class YouTubeModel { // does not extend Model

	public static function precache($app_key, array $videos) {
		$cache = CacheFactory::getFactory()->getCache();
	    $cacheItem = $cache->getItem("{$app_key}_youtube");
		foreach ($videos as $url) {
			if (empty($url)) continue;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, "https://noembed.com/embed?url=" . urlencode($url));
			$result = curl_exec($ch);
			curl_close($ch);
			$json[$app_key] = json_decode($result);
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