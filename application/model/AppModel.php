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

	/*
	*	app apps that this user is able to access
	*/
	public static function public_info_model() {
		$database = DatabaseFactory::getFactory()->getConnection();
		$query = $database->prepare("
			SELECT app_key, name, tagline, launch, replace(concat(:url, icon),'//','/') icon from apps
			where active = 1
			and app_id in (
				select app_id from app_tiers
				where name not like :api
			)
		");
		$query->execute(array(
			':api' => 'api-%',
			':url' => Config::get("URL")
		));
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

	public static function exists($app_key) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT count(1) FROM apps WHERE app_key = :key";
		$query = $database->prepare($sql);
		$query->execute(array(":key" => $app_key));
		return ($query->fetchColumn() > 0);
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
		$active = (Session::userIsAdmin()) ? "" : "AND a.active = 1";
		$sql = "SELECT a.app_id, a.app_key, a.name, a.tagline, a.whatisit, a.icon, a.launch, a.active, a.status, a.auth_type, a.url, a.popular
				FROM apps a INNER JOIN store_section_apps s ON a.app_id = s.app
				WHERE s.section = :section
				$active
				ORDER BY s.sort";
		$query = $database->prepare($sql);
		$query->execute(array(':section' => $section_id));
		return $query->fetchAll();
	}

	public static function app_requires_authentication($app_key) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$query = $database->prepare('
			SELECT count(1)
			FROM apps
			WHERE app_key = :app
			AND auth_type = :auth
		');
		$query->execute(array(':app' => $app_key, ':auth' => AUTH_TYPE_TOKEN));
		return ($query->fetch(PDO::FETCH_COLUMN, 0) == 1); // FETCH_ASSOC);
	}

	public static function getLaunchUrl($app_key, $subscription) {
		// does this app_key exist
		$database = DatabaseFactory::getFactory()->getConnection();
		$query = $database->prepare('
			SELECT url, launch, auth_type
			FROM apps
			WHERE app_key = :app
			LIMIT 1
		');
		$url = Config::get("URL");
		$query->execute(array(':app' => $app_key));
		if ($row = $query->fetch()) {
			switch ( intval($row->auth_type,10) ) {
				case AUTH_TYPE_TOKEN:
					// does the app_key match the subscription (or are we being fuddled)
					if (ApiModel::validate_app_is_in_subscription($subscription, $app_key)) {
						// the token is the reference id from subscription, we'll just use the hash since we can compute it later
						$token = password_hash($subscription, PASSWORD_BCRYPT, array("cost" => 10));
						$url = sprintf($row->launch, Text::base64enc($token));
					}
					break;

				case AUTH_TYPE_NONE:
					$url = $row->url;
					break;
			}
		}
		return $url;
	}

	public static function app_id_for_key($app_key)
	{
		$database = DatabaseFactory::getFactory()->getConnection();
		$query = $database->prepare("SELECT app_id FROM apps WHERE app_key = :app_key LIMIT 1");
		$query->execute(array(":app_key" => $app_key));
		return $query->fetch(PDO::FETCH_COLUMN, 0);
	}

}