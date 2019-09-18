<?php
/**
 * undocumented class
 *
 * @package default
 * @author
 **/
class AppModel extends Model
{

    CONST TABLE_NAME = "apps";
    CONST ID_ROW_NAME = "app_id";

    protected $data_model;

    public function get_model($include_sections = false, $default_to_description_field = false)
    {
        $data = $this->data_model;
        if ($include_sections === true) {
        	$data->Sections = self::get_app_sections($data->app_id, ($default_to_description_field === true) ? $data->description : null);
        }
        return $data;
    }

    public function set_model($data)
    {
    	if (isset($data->Sections)) {
    		unset($data->Sections);
    	}
        $this->data_model = $data;
    }

    public function __construct($key = "app_id", $match = "")
    {
        parent::__construct();
		if (strval($match) === "0") {
			$this->data_model = parent::Create(self::TABLE_NAME, false);
		} else {
			$data = parent::Read(self::TABLE_NAME, "{$key} = :key", array(":key"=>$match), '*', true);
			if (!empty($data)) {
				$this->data_model = $data;
			}
		}
		return $this;
   	}

    public function delete($id = 0)
    {
        if ($id > 0) {
            parent::Destroy(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $id));
        } else {
            $idname = self::ID_ROW_NAME;
            parent::Destroy(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $data_model->$idname));
        }
    }

    public function load($id)
    {
        $this->data_model = parent::Read(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $id))[0]; // 0th of a fetchall
        return $this;
    }

    public function make()
    {
        $this->data_model = parent::Create(self::TABLE_NAME, true);
        return $this;
    }

    public function save()
    {
        return parent::Update(self::TABLE_NAME, self::ID_ROW_NAME, $this->data_model);
    }

    public function get_id() {
        if (isset($this->data_model)) {
            $idrowname = self::ID_ROW_NAME;
            return $this->data_model->$idrowname;
        }
    }

	public static function getAllAppKeys() {
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT app_key, app_id
				FROM apps
				WHERE active > 1 and trim(coalesce(launch, '')) <>''
		";
		$query = $database->prepare($sql);
		$query->execute(); // array(':active' => true));
		return $query->fetchAll();
	}

	public static function get_app_sections($app_id, $default = null)
	{
		$results = [];
		$records = Model::Read("app_section", "app_id=:key", array(":key"=>$app_id), "id", false, "sort");
		if (empty($records) && !empty($default)) {
			$results[] = [
				"id" => 0,
				"app_id" => $app_id,
				"sort" => 999,
				"classname" => "cs-section-notset",
				"content" => $default
			];
		} else {
			foreach($records as $record) {
				$results[] = (new AppSectionModel($record->id))->get_model();
			}
		}
		return $results;
	}

	/**
	 * get a list of the (active) appkeys (that have an api)
	 */
	// public static function getAppKeys()
	// {
	// 	$database = DatabaseFactory::getFactory()->getConnection();
	// 	$sql = "SELECT app_key, launch
	// 			FROM apps
	// 			WHERE active = :active AND apienabled=1";
	// 	$query = $database->prepare($sql);
	// 	$query->execute(array(':active' => true));
	// 	return $query->fetchAll();
	// }

	/*
	*	app apps that this user is able to access by their subscription
	*/
	public static function public_info_model($hash, $include_mods = false) {

		$matcher = " md5(s.referenceId) = :hash";
		if (is_numeric($hash)) {
			$matcher = " s.subscription_id=:hash";
		}

		$database = DatabaseFactory::getFactory()->getConnection();
		$extras = "";
		if ($include_mods === true) {
			$extras = ", a.mods";
		}
//			SELECT a.app_key, a.name, a.tagline, a.guide, concat(:url, 'launch/', a.app_key, '/{token}/') launch, concat(left(:url,length(:url)-1), a.icon) icon, a.colour, a.glyph
		$query = $database->prepare("
			SELECT a.app_key, a.name, a.tagline, a.guide, concat(:url, 'launch/', a.app_key, '/{token}/') launch, concat(:url, a.icon) icon, a.colour, a.glyph
			$extras
			FROM apps a
				INNER JOIN product_bundle pb ON find_in_set(cast(a.app_id AS CHAR), pb.app_ids)
				INNER JOIN subscriptions s on pb.id = s.product_id and {$matcher}
			WHERE a.active = 3
		");
		//	UNION
		//	SELECT app_key, name, tagline, guide, url AS launch, concat(left(:url,length(:url)-1), icon) icon, colour, glyph
		//	FROM apps
		//	WHERE app_key = :debug
		$query->execute(array(
		//	':api' => 'api-%',
			':url' => Config::get("URL"),
		//	':debug' => 'scodebug',
			':hash' => $hash
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
			$sql = "SELECT app_id, app_key, name, icon, guide, launch, auth_type, added, active, status, tagline, whatisit, description, media, meta_keywords, meta_description, meta_title, popular
					FROM apps
					ORDER BY name";
		} else {
			$sql = "SELECT app_id, name, active, app_key
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
		$sql = "SELECT app_key, name FROM apps
			WHERE active > 1
		"; // 0 = hidden, 1 = depricated, 2 = coming soon, 3 = active
		$query = $database->prepare($sql);
		$query->execute();
		return $query->fetchAll();
	}

	// Replaced with Model::exists(table, where, params)
	// public static function exists($app_key) {
	// 	$database = DatabaseFactory::getFactory()->getConnection();
	// 	$sql = "SELECT count(1) FROM apps WHERE app_key = :key";
	// 	$query = $database->prepare($sql);
	// 	$query->execute(array(":key" => $app_key));
	// 	return ($query->fetchColumn() > 0);
	// }

	/**
	 * get an app by its key (string)
	 */
	public static function getAppByKey($app_key)
	{
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT app_id, app_key, name, icon, launch, auth_type, added, active, status, tagline, whatisit, description, media, meta_keywords, meta_description, meta_title, popular, glyph, colour, guide
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
		$sql = "SELECT app_id, app_key, name, icon, launch, auth_type, added, active, status, tagline, whatisit, description, media, meta_keywords, meta_description, meta_title, popular, glyph, colour, guide
				FROM apps
				WHERE app_id = :app_id
				LIMIT 1";
		$query = $database->prepare($sql);
		$query->execute(array(':app_id' => $app_id));
		return $query->fetch();
	}

	// not all the record, only that which is related to the app-by-section view
	// public static function getAppsByStoreSection($section_id)
	// {
	// 	$app_ids = Model::Read("store_sections, self::ID_ROW_NAME . "=:id", array(":id" => $section_id), 'app_ids', true);
	// 	$results = [];
	// 	foreach ($app_ids as $app_id) {
	// 		$result[] = (new AppModel("app_id", $app_id))->get_model(false,false);
	// 	}
	// 	return $results;

	// 	// $database = DatabaseFactory::getFactory()->getConnection();
	// 	// $active = (Session::userIsAdmin()) ? "" : "AND a.active = 1";
	// 	// $sql = "SELECT a.app_id, a.app_key, a.name, a.tagline, a.whatisit, a.icon, a.launch, a.active, a.status, a.auth_type, a.url, a.popular, a.glyph, a.colour, a.guide
	// 	// 		FROM apps a INNER JOIN store_section_apps s ON a.app_id = s.app
	// 	// 		WHERE s.section = :section
	// 	// 		$active
	// 	// 		ORDER BY s.sort";
	// 	// $query = $database->prepare($sql);
	// 	// $query->execute(array(':section' => $section_id));
	// 	// return $query->fetchAll();
	// }

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

	public static function getLaunchUrl($app_key, $subscription, $token = null, $platform = null) {
		// does this app_key exist
		$database = DatabaseFactory::getFactory()->getConnection();
		$query = $database->prepare('
			SELECT launch, auth_type
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
						// we might have been passed it in if it came from /api/createToken - might as well reuse it
						if (is_null($token)) {
							$token = password_hash($subscription, PASSWORD_BCRYPT, array("cost" => 10));
						}

						// if I am being impersonated, we need to toss that hash onto the token too (before it's encoded) - for an admin logon
						if (($source = Auth::is_impersonated()) !== false) {
							$token .= password_hash($source, PASSWORD_BCRYPT, array("cost" => 10));
						}

						$url = sprintf($row->launch, Text::base64enc($token));
					}
					break;

				case AUTH_TYPE_NONE:
					$url = $row->launch;
					break;
			}
		}
		if (!empty($platform)) $url .= (substr($url,-1)!=='/' ? '/':'')."{$platform}/";
		return $url;
	}

	public static function app_id_for_key($app_key)
	{
		// $database = DatabaseFactory::getFactory()->getConnection();
		// $query = $database->prepare("SELECT app_id FROM apps WHERE app_key = :app_key LIMIT 1");
		// $query->execute(array(":app_key" => $app_key));
		// return $query->fetch(PDO::FETCH_COLUMN, 0);

        return Model::Read("apps", "app_key=:key", array(":key"=>$app_key),"app_id",true)->app_id;
	}

	public static function app_key_for_id($app_id)
	{
        return Model::Read("apps", "app_id=:id", array(":id"=>$app_id),"app_key",true)->app_key;
	}
	public static function app_name_for_key($app_key)
	{
        return Model::Read("apps", "app_key=:key", array(":key"=>$app_key),"name",true)->name;
	}

	// grab (+cache) the css for active app colours
	public static function apps_colours_css($force = false) {
		$cache = CacheFactory::getFactory()->getCache();
	    $cacheItem = $cache->getItem("app_colours_css");
	    $css = $cacheItem->get();
	    if ($force || is_null($css)) {
			$database = DatabaseFactory::getFactory()->getConnection();

			/*
			apps store their colour in the database
			we want the colours to be exposed to CSS VARIABLES as well as LESS VARIABLES
			we also want the colours to be used by LESS MIXINS (which compile before runtime)
			we use CONCAT on apps to build PRE-MINIFIED css rules based on the app names
			then use GROUP_CONCAT on the concats to flatten the rows into a single cell
			then pluck the cells using their array index from the resulting pdo::fetch

			The LESS code is embedded in a file called /css/colours.less which is included into the coursesuite.less file
			then compiled before the site is deployed. This means the less variables are available to the coursesuite.less file
			after this function is run (e.g. clear the css cache, let this file regenerate the colours.less, then compile the coursesuite less)

			note: the sql below is not readable code, here is an example

			    SELECT GROUP_CONCAT(
			    	CONCAT(
			    		'.cs-colour-',
			    		app_key,
			    		'{color:',
			    		case when colour is null
			    			then 'grey'
			    			else colour
			    		end,
			    		'}.cs-bgcolour-',
			    		app_key,
			    		'{background-color:',
			    		case when colour is null
			    			then 'grey'
			    			else colour
			    		end,
			    		'}'
			    	)
			    	SEPARATOR ''
			    )
			    FROM apps

			*/


			$query = $database->prepare("
					SELECT CONCAT(':root{',GROUP_CONCAT(CONCAT(
						'--', lower(app_key), ':',
						case when colour is null
						then 'grey'
						else colour
						end,';'
						) SEPARATOR ' '),'}',
						GROUP_CONCAT(CONCAT('.cs-colour-',app_key,'{color:var(--', lower(app_key), ')}.cs-bgcolour-',app_key,'{background-color:var(--', lower(app_key), ')}') SEPARATOR '')
					),
					GROUP_CONCAT(CONCAT('@',lower(app_key),':',
						case when colour is null
			    			then 'grey'
			    			else colour
			    		end,';') SEPARATOR ''),
			    	GROUP_CONCAT(CONCAT('.cs-gradient-', app_key, '{.cs-gradient-fn(@base:',
			    	    case when colour is null
			    			then 'grey'
			    			else colour
			    		end, ')}') SEPARATOR '\n'),
					GROUP_CONCAT(CONCAT('.cs-bg-', app_key, '{.cs-bg-fn(@base:',
							case when colour is null
								then 'grey'
								else colour
							end, ')}') SEPARATOR '\n')
					FROM apps
		    ");
			$query->execute();
			$data = $query->fetch(PDO::FETCH_NUM);
			$css = $data[0];//$query->fetchColumn(0);
			$less = $data[1] . PHP_EOL . $data[2] . PHP_EOL . $data[3];//$query->fetchColumn(1);
	        file_put_contents(Config::get("PATH_CSS_ROOT") . 'colours.less',$less);
			$cacheItem->set($css)->expiresAfter(86400)->addTags(["coursesuite","css"]); // 1 day
			$cache->save($cacheItem);
		}
		return $css;
	}

	// list of the app models for a comma seperate list or array of app ids (e.g. store_sections.app_ids)
	public static function get_apps($app_ids) {
		if (!is_array($app_ids)) $app_ids = explode(',', $app_ids);
		$results = [];
		foreach ($app_ids as $app_id) {
			$result[] = (new AppModel("app_id", $app_id))->get_model(false,false);
		}
		return $results;
	}


}