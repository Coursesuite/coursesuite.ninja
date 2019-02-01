<?php

/**
 * Class Api
 * This controller is meant to be used programatically by external services and sites
 * Requests are authenticated using digest authentication
 * like a proper 404 response with an additional HTML page behind when something does not exist.
 */
class ApiController extends Controller
{

	public function __construct()
	{
		parent::__construct(false); // passing false avoids initialising a session object
	}

	public function validateorder($reference) {
		parent::requiresAjax();
		$json = json_decode(Text::base64dec($reference));
		$data = array("ready"=>false);
		if (Model::exists("subscriptions", "referenceId=:ref", [":ref"=>$json->reference])) {
			(new AccountModel("id", Model::ReadColumn("subscriptions", "user_id", "referenceId=:ref", [":ref"=>$json->reference])))->log_on();
			$data = array("ready"=>true);
		}
		$this->View->renderJSON($data);
	}

	/* this method exists because EXZA can't get their shit together
	* if this method doesn't exist, their their portal BREAKS and shows a white page. OMGROFLNÜBS
	*/
	public function generateApiKey() {
		$username = parent::requiresAuth();
		LoggingModel::logMethodCall(__METHOD__, $username, file_get_contents("php://input"), "Depreciated method call");
		//if ($username === "apianmf") {
		//	$hash = md5("api-anmf-special-order");
		//	$token = ApiModel::generate_token_for_subscription($hash);
		//} else

		if ($username === "apikaplan") {
			$hash = md5("api-kaplan-special-order");
			$token = ApiModel::generate_token_for_subscription($hash);

		} elseif ($username === "apimatrix") {
			$hash = md5("api-matrix-special-order");
			$token = ApiModel::generate_token_for_subscription($hash);

		// } elseif ($username === "apiwvphn") {
		// 	$hash = md5("api-wvphn-special-order");
		// 	$token = ApiModel::generate_token_for_subscription($hash);

		} else {
			$data = array("error" => "This method is depreciated.");
			$this->View->renderJSON($data);
			return false;
		}

		$data = array("token" => Text::base64enc($token), "reminder" => "This method is depreciated and will be removed on December 1, 2017.");
		$this->View->renderJSON($data);
		return true;

	}

	/**
	* @apiDefine digest
	* 	This method requires DIGEST authentication. Your apikey is the username, and secret_key is your password.
	*/

	/**
	* @apiDefine bearer
	* 	This method requires an Authoirization header to be present.
	*/

	/**
	* @api {get} /api/validate/app_key/hash/
	* @apiPrivate
	* @apiName Validate
	* @apiGroup Authenication
	* @apiVersion 0.9.1
	* @apiDescription Internal method for validating the has value sent to apps. Requires a specific digest authentication user
	* @apiParam {String} app_key The keyname of the app being validated (e.g. docninja)
	* @apiParam {String} hash The hash to be validated
	* @apiSuccessExample {json} Success-Response:
	*     HTTP/1.1 200 OK
	*     {
	*	"valid": true,
	*	"code": {
	*		"minified": true,
	*		"debug": false
	*	},
	*	"licence": {
	*		"tier": 1,
	*		"seats": 5,
	*		"remaining": 4
	*	},
	*	"api": {
	*		"bearer": "1223b8c30a347321299611f873b449ad",
	*		"publish": "https://my.lms.edu/publish/url/",
	*		"header": {
	*			"html": null,
	*			"css": null
	*		}
	*	},
	*	"user": {
	*		"container": "bobsmith",
	*		"email": "bob.smith@gmail.com"
	*	},
	*	"app": {
	*		"addons": ["design-pack-1","quiz-player"],
	*		"socket": "wss://www.coursesuite.ninja/acbd18db4cc2f85cedef654fccc4a4d8",
	*		"layer": "(encoded javascript)",
	*		"guide": "https://guide.coursesuite.ninja/documentninja"
	*	}
	*     }
	*/
	public function validate($app_key, $token_encoded) {

		$authtoken = parent::requiresAuth();
//$authtoken = "timdebug";
		$token_raw = Text::base64dec($token_encoded);

		LoggingModel::logMethodCall(__METHOD__, $authtoken, $app_key, $token_raw, $token_encoded);

		$result = new stdClass();
		$result->valid = false;

		$result->code = new stdClass();
		$result->code->debug = Config::get("debug");
		$result->code->minified = (!$result->code->debug);

		$result->licence = new stdClass();
		$result->licence->tier = 99; // depreciated
		$result->licence->seats = 0;
		$result->licence->remaining = 0;

		$result->user = new stdClass();
		$result->user->container = "";
		$result->user->email = "";

		$result->app = new stdClass();
		$result->app->addons = array(); // TODO: might be extra products we purchased, such as theme-packs.
		$result->app->socket = "";
		$result->app->layer = "";
		$result->app->guide = "";

		$tokens = str_split($token_raw,60); // every 60 characters represents a new token
		$token = $tokens[0]; // first token is the most relevant hash
		unset($tokens[0]); // now we can check the remaining hashes
		$admins = AccountModel::get_admin_users(); // list of site admin salted md5's

		// when there are multiple tokens you check the 2nd, 3rd, etc to see if it trumps the first, or if the first has run out of seats
		$bypass_licencing = false;
		$seats = null;
		foreach ($tokens as $parent_token) {

			// hash might be the salted md5 of a user_id who is an admin - e.g. impersonation
			foreach ($admins as $admin_hash) {
				if (password_verify($admin_hash, $parent_token)) {
					$bypass_licencing = true;
					$seats = [5,5]; // enough
					break 2; // inner foreach
				}
			}

			// token might be a parent hash; model will be set if the token is valid and active
			if (($model = ApiModel::find_model_for_token($parent_token)) !== false) {
				$seats = [Licence::total_seats($model->hash), Licence::seats_remaining($model->hash)];
				break 1; // outer foreach
			}
		}

		if ($model = ApiModel::find_model_for_token($token)) {

			$result->valid = true;

			$app_id = Model::ReadColumn("apps","app_id","app_key=:key",array(":key"=>$app_key));
			$result->app->guide = Model::ReadColumn("apps","guide","app_key=:key",array(":key"=>$app_key));

			$user = new AccountModel("id",$model->user);
			$result->user->email = $user->get_property("user_email");
			if (!empty($user->get_property("user_container"))) {
				$result->user->container = $user->get_property("user_container");
			} else {
				list($name,$_) = explode('@',$result->user->email);
				$result->user->container = preg_replace('/[^a-zA_Z0-9]/', '', $name);
			}

			$result->licence->seats = Licence::total_seats($model->hash); // total seats you are licensed for
			$result->licence->remaining = Licence::seats_remaining($model->hash); // comes from from the concurrency database (redis)

			// in the case you are out of licences and there is a parent a
			if ($bypass_licencing || ($result->licence->remaining === 0 && $seats !== null)) {
				$result->licence->seats = $seats[0];
				$result->licence->remaining = $seats[1];
			}

			$product = new ProductBundleModel("id", $model->product); // what they bought, which we know includes this app_key (otherwise the has wouldn't have validated)

			if ($product->is_api() === true) {
				$result->api = new stdClass();
				$result->api->bearer = md5($model->hash . Encryption::decrypt(Text::base64dec($user->get_property("secret_key")))); // bearer = md5 of apikey + secret key
				$result->api->publish = ApiModel::get_publish_url($model->hash, $token); // since token is a unique version of hash this is an easy match
				$white_label = ApiModel::get_white_label($app_key, $model->subscription);
				$result->api->header = new stdClass();
				$result->api->header->html = $white_label["html"];
				$result->api->header->css = $white_label["css"];
			}

			// removed . ":" . Config::get("WEBSOCKET_PORT") - you want it to connect on the ssl port THEN have a proxy redirect it to the local port
				$result->app->socket = Config::get("WEBSOCKET_SCHEMA") . Config::get("WEBSOCKET_HOST") . "/" . $model->hash;
			if ($bypass_licencing) {
				$result->app->layer = "";
			} else {
				$result->app->layer = ($result->code->debug) ? Config::get("WEBSOCKET_LAYER") : Config::get("WEBSOCKET_LAYER_MINIFIED");
			}
			// ws://coursesuite.ninja.dev/"
		}
		$this->View->renderJSON((array) $result);
	}

	/**
	* @api {post} /api/createToken/ Generate an access token for lauching apps
	* @apiName Generate
	* @apiGroup Authentication
	* @apiVersion 0.9.0
	* @apiPermission digest
	* @apiDescription In order to launch apps, you need to generate an access token.
	* 		It validates your access using your api key and secret, and stores the publish-url (if set).
	* 		When opening apps, this key will be validated for concurrency: you can only have as many
	* 		apps running at the same time as you are licensed for.
	*		NOTE: You don't have to generate this each time. The key generated is valid for as long as your
	*		subscription is active. Use caching!
	*
	* @apiParam {String} [publish_url] Url to enable publish-to feature (in supported apps).
	*					Apps will POST the generated zip package to this URL and attach
	*					the header Authorization: Bearer &lt;hash&gt;, where &lt;hash&gt; is
	*					the MD5 of your apikey appended with your secret key, which you can use to
	*					validate / accept the input.
	*
	* @apiSuccess {String} token App access token
	* @apiSuccess {Integer} seats Total number of concurrent users allowed for this subscription
	* @apiSuccess {Integer} remaining Total number of concurrent users remaining
	*					Note: it's possible this value may have changed by the time you
	*					launch the app. Launching will double-check it.
	* @apiSuccessExample {json} Success-Response:
	*     HTTP/1.1 200 OK
	*     {
	*	"token": "$2$10,f8ds98f8f8023iy43y32i8f7ds89f79",
	*	"seats": 5,
	*	"remaining": 4
	*     }
	*/
	public function createToken($context = null)
	{

// need to first get the token associated with the user that logged on using digest
		$hash = parent::requiresAuth();

// lets remember this
		LoggingModel::logMethodCall(__METHOD__, $hash, file_get_contents("php://input"));

		if (!preg_match('/^[a-f0-9]{32}$/', $hash)) {
			$this->View->renderJSON(array("error" => Text::get("INVALID_URL")));
			return false;
		}

// we want to return the number of seats this license is for
		$seats = Licence::total_seats($hash);

// we then check the number of license places (seats) remaining for this app; the username is the hash
		$remaining =  Licence::seats_remaining($hash);

// we need to check to see if the usage cap for this subscription
		if (false === ApiModel::usage_cap_remaining($hash)) {
			$this->View->renderJSON(array("error" => Text::get("EXCEEDED_MONTHLY_CAP")));
			return false;
		}

// the token itself can be generated now too. This will be different every time, so no point logging it
		$token = ApiModel::generate_token_for_subscription($hash);

// well, lets finally look up the user and see if it is a sub-account of a sub that has volume licensing .. whew!


// if they gave us a publish_url, grab and validate it ising the xss filter (I mean we trust them, right, but only so far)
		$publish_url = Request::post("publish_url", true);
		if (!empty($publish_url)) {
			$this->View->renderJSON(array("error" => Text::get("INVALID_URL")));
			return false;
		}

// record the api_request
		ApiModel::record_api_request($hash, $token, $publish_url);

// that's all we have to do
		$this->View->renderJSON(array(
			"token" => Text::base64enc($token),
			"seats" => $seats,
			"remaining" => $remaining
		));
	}

	/**
	* @api {get} /api/validate/app_key/hash/
	* @apiPrivate
	* @apiName HashInfo
	* @apiGroup Authenication
	* @apiVersion 0.9.1
	* @apiDescription Internal method for determining the abstract hash of a subsription, when given an auth token
	* @apiParam {String} token_encoded The hash to be validated
	*/
	public function hashinfo($token_encoded) {
		$authtoken = parent::requiresAuth();

		$token_raw = Text::base64dec($token_encoded);
		$tokens = str_split($token_raw,60); // every 60 characters represents a new token
		$token = $tokens[0]; // first token is the most relevant hash, only deal with it

		LoggingModel::logMethodCall(__METHOD__, $authtoken, $token, $token_raw);

		$result = new stdClass();
		$result->token = "";
		if ($model = ApiModel::find_model_for_token($token)) {
			$result->token = $model->hash;
		}
		$this->View->renderJSON($result);
	}

	/**
	* @api {post} /api/info/ List available apps and their launch points
	* @apiName Names
	* @apiGroup App
	* @apiVersion 0.9.2
	* @apiPermission bearer
	* @apiDescription List the app keys and their launch points that your subscription allows.
	*			NOTE: This value changes infrequently. Use appropriate caching.
	*
	* @apiHeader {String{32}} Bearer Your api-key
	* @apiHeaderExample {json} Header-Example:
	*     {
	*       "Authorization": "Bearer: 1223b8c30a347321299611f873b449ad"
	*     }
	* @apiSuccess {Array} app List of app properties
	* @apiSuccess {String} app.app_key Internal identifier of app
	* @apiSuccess {String} app.name Name of app
	* @apiSuccess {String} app.tagline Short descriptor for the app
	* @apiSuccess {String} app.guide URL for documentation
	* @apiSuccess {String} app.launch Launch url for app.
					You need to replace {token} with the value returned by the /api/createToken/ method.
	* @apiSuccess {String} app.icon Full url to icon used by CourseSuite for this app
	* @apiSuccess {String} app.glyph SVG representation of app icon
	* @apiSuccess {String} app.colour Base colour used by CourseSuite for this app
	* @apiSuccessExample {json} Success-Response:
	*	HTTP/1.1 200 OK
	*	[
	*	{
	*		"app_key": "docninja",
	*		"launch": "https://www.coursesuite.ninja/launch/docninja/{token}/",
	*		"icon": "https://www.coursesuite.ninja/img/apps/docninja/3114fbf5f4d83a87b7842d1aa561b34a.png",
	*		"guide": "https://guide.coursesuite.ninja/documentninja",
	*		"name": "Document Ninja",
	*		"tagline": "Add a SCORM wrapper to your existing content by converting it to modern, industry standard device-ready HTML5"
	*		"colour": "#7eaf5e",
	*		"glyph": "<svg xmlns=\"http://www.w3.org/2000/svg\"/>"
	*	}
	*	]
	*/

	public function info()
	{
		$subscription = parent::requiresBearer();
		$miss = null;

// todo: cache the product_key version that this info() relates to instead of the actual subscription
	    $cache = CacheFactory::getFactory()->getCache();
	    $cacheItem = $cache->getItem("api_info_{$subscription}");
	    $model = $cacheItem->get();
	    if (true || is_null($model)) {
// log a cache miss so we can later refine the expiresAfter
	    	$miss = "cache miss ";
			$model = AppModel::public_info_model($subscription);
			$cacheItem->set($model)->expiresAfter(3600)->addTags(["coursesuite","api"]); // 1 hour cache
			$cache->save($cacheItem);
		}
		LoggingModel::logInternal(__METHOD__, $subscription,$miss);
		$this->View->renderJSON($model);
	}

	/**
	* @api {post} /api/white_label/ Set the header style for a single app
	* @apiName White Label
	* @apiGroup Customisation
	* @apiVersion 0.9.2
	* @apiPermission digest
	* @apiDescription Set the header and css styles for a single app.
	*  		      HTML is injected between the &lt;header&gt; ... &lt;/header&gt; tags in the app.
	*		      CSS is included in a style tag before the &lt;/head&gt; tag.
	*		      If both HTML and CSS are empty, the white label is deleted.
	* @apiParam {String} app_key App Key to set.
	* @apiParam {String} [html] HTML header code (e.g. image or hyperlink). Content will be filtered using HTMLPurifier
	* @apiParamExample {string} HTML example
	* 					<div class='cs-banner'>
	*						<h1><img src='img/coursesuite.svg' width='40' height='40' title='another CourseSuite app'> Document <span>Scormification</span> Ninja</h1>
	*					</div>
	* @apiParam {String} [css] CSS style definitions
	* @apiParamExample {string} CSS example
	*					.cs-banner { background-color: #f4f4f4; padding: 0 20px; line-height: 62px; color: #777; }
	*					.cs-banner h1 {font-size: 32px; line-height: 62px; margin: 0; }
	* 					.cs-banner span { color: #000 }
	*					.cs-banner img { vertical-align: middle; }
	* @apiSuccessExample {json} Success-Response:
	*    HTTP/1.1 200 OK
	*    {
	*	"status": "ok",
	*	"message": ""
	*    }
	* @apiErrorExample {json} Error-Response:
	*    HTTP/1.1 200 OK
	*    {
	*	"status": "error",
	*	"message": "app_key was not found"
	*    }
	*/
	public function white_label() {

		$username = parent::requiresAuth();

		$html = Request::post_html("html");
		if (empty($html)) $html = null;
		$css = Request::post("css", true);
		if (empty($css)) $css = null;
		$app_key = Request::post("app_key", true);

		if (!Model::exists("apps", "app_key=:key", array(":key"=>$app_key))) {
		// if (!AppModel::exists($app_key)) {
			$this->View->renderJSON(array(
				"status" => "error",
				"message" => "app_key was not found"
			));
			die();
		}

		$sub_id = SubscriptionModel::get_subscription_id_for_hash($username);
		ApiModel::set_white_label($app_key, $sub_id, $html, $css);
	}

	public function tasks()
	{
		SubscriptionModel::validateSubscriptions();
	}

	public function dl($hash,$app_key,$file) {

	}

	/* -------------------------------- FASTSPRING -----------------------------------

	there are two versions - classic and contextual
	contextual is a webhook which can't send auth data, only a hmac sha256 secret and X-FS-Signature header with the json payload (post)
	classic sends auth and the following parameters as form post

	accountUrl
	email
	productName
	referenceId
	referrer - Text::base64dec(value)
	status
	statusReason
	subscriptionEndDate
	subscriptionUrl
	testmode

	security_data
	security_hash

	 */

	public function subscription(...$params)
	{
		$username = parent::requiresAuth();
		LoggingModel::logMethodCall(__METHOD__, $username, $params, Request::post_debug());
        extract($_POST, EXTR_OVERWRITE, "security_"); // extract security_* as variables; &security_foo=bob --> $security_foo=>"bob"
        if (md5($security_data . Config::get('FASTSPRING_SECRET_KEY') == $security_hash)) {

            // $tierid = (int) TierModel::getTierIdByProductName(Request::post("productName")); // short name of subscription in fastspring system

            $fastspringProductId = Request::post("productName");
            // fastspring product pages look like this: http://sites.fastspring.com/coursesuite/product/docninja-pro
            // the productPath look like this: /docninja-pro or /subscriptions/pro/docninja-pro
            // we just want the last element, which MUST match the product_id

            // TODO read the id column instead of the whole model
            $fastspringProductId = array_pop(explode("/", Request::post("productPath"))); //  /api-5-12 => api-5-12
            $product = (new ProductModel)->load_by_productId($fastspringProductId);
            $product_id = $product->get_id();
            unset($product);

            $referrer = Request::post("referrer");
            if (isset($referrer)) {
                $userid = (int) Encryption::decrypt(Text::base64dec(Request::post("referrer"))); // passes back whatever we send it during checkout, same re-sent each re-bill
            } else {
                $userid = -1; // so we have no definate way of tying this to a real user, maybe look at Request::post("email") ? wrong email entered during checkout could mess with this
            }

            $endDate = Request::post("subscriptionEndDate"); // date | empty
            $endDateSet = isset($endDate); // if set then the subscription is inactive after this date
            $referenceId = Request::post("referenceId"); // unique order id for fastspring dashboard use
            $subscriptionUrl = Text::base64enc(Encryption::encrypt(Request::post("subscriptionUrl"))); // unique user-facing transaction id for users personal reference
            $status = Request::post("status"); // active | inactive
            $statusReason = Request::post("statusReason"); // canceled-non-payment | completed | canceled | ""
            $testMode = (Request::post("testmode") == "true") ? 1 : 0; // true | false

            switch ($params[0]) {
                case "activated":
                    $subscription = (new SubscriptionModel)->make();
                    $model = $subscription->get_model();
                    $model->user_id = $userid;
                    unset($model->added); // allow database default to apply
                    $model->endDate = empty($endDate) ? null : $endDate;
                    $model->referenceId = $referenceId;
                    $model->subscriptionUrl = $subscriptionUrl;
                    $model->status = $status;
                    $model->statusReason = $statusReason;
                    $model->testMode = $testMode;
                    $model->active = 1;
                    $model->info = null;
                    $model->product_id = $product_id;
                    $subscription->set_model($model);
                    $subscription->save();
                    break;

                case "deactivated":
                    // deactivation will remove the subscription entry from the database
                    $subscription = (new SubscriptionModel)->loadByReference($referenceId);
                    $model = $subscription->get_model();
                    $model->active = 0;
                    $model->status = $status;
                    $model->statusReason = $statusReason;
                    $subscription->set_model($model);
                    $subscription->save();
                    break;

                case "failed":
                case "changed":
                    if ($endDateSet) {

                        $subscription = (new SubscriptionModel)->loadByReference($referenceId);
                        $model = $subscription->get_model();
                        $model->status = $status;
                        $model->statusReason = $statusReason;
                        $subscription->set_model($model);
                        $subscription->save();

                        if ($status == "inactive") { // has become inactive and will deactivate soon
                            MessageModel::notify_user("Your subscription has lapsed and will deactivate soon.", MESSAGE_LEVEL_MEH, $userid);
                        }
                        if ($statusReason == "canceled") { // we should next or soon see a deactivated
                            MessageModel::notify_user("Your subscription has been cancelled.", MESSAGE_LEVEL_MEH, $userid);
                        }
                    }
                    break;
            }
        }
	}

	// when an order goes through the checkout, before a subscription takes place
	public function orders(...$params) {
		$username = parent::requiresAuth();
		LoggingModel::logMethodCall(__METHOD__, $username, $params, Request::post_debug());
		extract($_POST, EXTR_OVERWRITE, "security_"); // extract security_* as variables
		if (md5($security_data . Config::get('FASTSPRING_SECRET_KEY') == $security_hash)) {
			// do something with the order
		}
	}

	public function licence(...$params)
	{
		$username = parent::requiresAuth();
		LoggingModel::logMethodCall(__METHOD__, $username, $params, Request::post_debug(), $_REQUEST);

		// this is a hash of all the values of the fields posted in plus a private key ...
		$security_request_hash = Request::post("security_request_hash");

		// we could concat the data in the request then md5 it and check it ...

		// $privatekey = '3e8466db46f05ed8056527a2174074aa';
		// $obj = unserialize('a:15:{s:3:"url";s:13:"/api/licence/";s:7:"company";s:11:"FooBar Inc.";s:5:"email";s:28:"vagif.samadoghlu@example.com";s:19:"internalProductName";s:14:"document-ninja";s:7:"periods";s:1:"1";s:7:"product";s:14:"document-ninja";s:11:"productName";s:14:"document-ninja";s:8:"quantity";s:1:"1";s:9:"reference";s:8:"TEST_REF";s:8:"sequence";s:1:"1";s:3:"sku";s:16:"TEST_PRODUCT_SKU";s:12:"subscription";s:11:"TEST_SUB_ID";s:4:"tags";s:2:"{}";s:4:"test";s:4:"true";s:21:"security_request_hash";s:32:"3b13478e187416b4279c7586cefc44e9";}');
		// $hash = $obj["security_request_hash"];
		// unset($obj["security_request_hash"]);
		// ksort($obj);
		// $implode = implode('', array_values($obj));
		// echo "\n", "implode=", $implode, md5($implode . $privatekey);

		$email = Request::post("email");
		$user = new AccountModel("email", $email);
		if ($user->get_property("user_id") > 0) {
			// a user exists
		} else {
			// create the user
			// then send the user their password
			// within a 'welcome to coursesuite you will need this to log in' message
		}


		// ... or just not do that, since security_request_hash already is unique to the user/product/subscription

		// so that's our licence key

        header("Content-Type: text/plain");
        echo $security_request_hash;
	}

	public function widgetcode($version = 1, $publickey = null) {
		if (is_null($publickey)) return;
		if (!Model::exists("subscriptions","md5(concat(referenceId,:salt))=:key",[":key"=>$publickey,":salt"=>Config::get("HMAC_SALT")])) return;

		$model = new stdClass();
		$model->version = $version;
		$model->publickey = $publickey;
		$model->options = [];

        header("Content-Type: text/plain");
		$this->View->renderHandlebars("api/widget/{$version}/runtime", $model,null,true);
	}

	public function widget($version = 1, $publickey = null) {
		if (is_null($publickey)) return;
		if (!Model::exists("subscriptions","md5(concat(referenceId,:salt))=:key",[":key"=>$publickey,":salt"=>Config::get("HMAC_SALT")])) return;

		Licence::refresh_licencing_info();

		// set some properties about the apps this key can access
		$model = new stdClass();
		$model->publickey = $publickey;

		$base_model = (new SubscriptionModel($publickey))->get_model(true, true, true);
		$subscription = new stdClass();
		$subscription->user_email = $base_model["Account"]->user_email;
		$subscription->user_id = md5($base_model["Account"]->user_id . Config::get("HMAC_SALT")); // abstract id
		$subscription->active = ($base_model["active"] !== "0");
		$subscription->trial = ($base_model["Product"]->product_key === "api-trial");
		$subscription->seats = Licence::seats_remaining(md5($base_model["referenceId"]));
		$subscription->theme = 1; // some kind of flag to switch clientside renderer
		foreach ($base_model["Product"]->Apps as $app) {
			$obj = new stdClass();
			$obj->name = $app->name;
			$obj->description = $app->tagline;
			$obj->icon = $app->glyph;
			$obj->key = $app->app_key;
			$obj->colour= $app->colour;
			$obj->guide = $app->guide;
			$subscription->apps[] = $obj;
		}
		$model->subscription = json_encode($subscription);

		// render the client facing library as a javascript function
        header("Content-Type: application/javascript");
		$this->View->renderHandlebars("api/widget/{$version}/applib", $model, null, true);
	}

	public function apps_colours_css() {
		 // AppModel::apps_colours_css() generates colours.less when it re-caches
		$this->View->output(AppModel::apps_colours_css(), "text/css");
	}
}
