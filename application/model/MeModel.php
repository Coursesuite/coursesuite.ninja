<?php

class MeModel {
	public static function LaunchPageModel($user_id) {
		$database =  DatabaseFactory::getFactory()->getConnection();
		$baseUrl = Config::get("URL");
		$results = [];

		$app_ids = SubscriptionModel::get_subscribed_app_ids_for_user($user_id);
		$all_launchable_app_ids = array_unique(explode(',',Model::ReadColumnRaw("select group_concat(app_ids) from product_bundle where active = 1 and product_key not like 'api%'")));
		$apps = implode(',',$all_launchable_app_ids);

		$query = $database->prepare("
			SELECT app_id, app_key, name, glyph, active
			FROM apps
			WHERE app_id in ({$apps})
			ORDER BY name
		");
		$query->execute();
		$i = 0;
		while (list($app_id, $app_key, $name, $glyph, $active) = $query->fetch(PDO::FETCH_NUM)) {  //, PDO::FETCH_ORI_NEXT)) {
			$launchable = in_array($app_id, $app_ids);
			$results[] =array(
				"app_key" => $app_key,
				"mask" => "/img/blobs/" . (($i++ % 5) + 1) . ".png",
				"name" => $name,
				"glyph" => $glyph, // 'data:image/svg+xml,' . rawurlencode($glyph),
				"launch" => $launchable ? $baseUrl . "launch/" . $app_key : "",
				"class" => $launchable ? "cs-gradient-{$app_key}" : "cs-gradient-unavailable",
				"active" => $active
			);
		}

		return $results;
	}
}