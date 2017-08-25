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

	/**
	* @apiDefine digest
	* 	This method requires DIGEST authentication. Your apikey is the username, and secret_key is your password.
	*/

	/**
	* @apiDefine bearer
	* 	This method requires an Authoirization header to be present.
	*/

	/**
	* @api {get} /validate/app_key/hash/
	* @apiPrivate
	* @apiName Validate
	* @apiGroup Token
	* @apiVersion 0.1.0
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
	*		"addons": ["design-pack-1","quiz-player"]
	*	}
	*     }
	*/
	public function validate($app_key, $token_encoded) {

		$authtoken = parent::requiresAuth();
		$token = Text::base64dec($token_encoded);
		LoggingModel::logMethodCall(__METHOD__, $authtoken, $app_key, $token);

		$result = new stdClass();
		$result->valid = false;

		$result->code = new stdClass();
		$result->code->debug = Config::get("debug");
		$result->code->minified = (!$result->code->debug);

		$result->licence = new stdClass();
		$result->licence->tier = 0;
		$result->licence->seats = 0;
		$result->licence->remaining = 0;

		$result->user = new stdClass();
		$result->user->container = "";
		$result->user->email = "";

		$result->app = new stdClass();
		$result->app->addons = array();
		$result->app->socket = "";

		if ($model = ApiModel::find_model_for_token($token)) {

			$result->valid = true;
			$app_id = AppModel::app_id_for_key($app_key);
			// todo: $result->app->addons might be extra products we purchased, such as theme-packs.

			$user = new AccountModel($model->user);
			$result->user->email = $user->get_property("user_email");
			list($name,$_) = explode('@',$result->user->email);
			$result->user->container = preg_replace('/[^a-zA_Z0-9]/', '', $name); // todo: make this into a database field so we can edit it

			$result->licence->seats = Licence::total_seats($model->hash); // total seats you are licensed for
			$result->licence->remaining = Licence::seats_remaining($model->hash); // comes from from the concurrency database (redis)

			$product = new ProductModel($model->product); // what they bought, which we know includes this app_key (otherwise the has wouldn't have validated)
			if ($product->is_api() === true) {
				$tier = AppTierModel::get_highest_tier_level($app_id); // highest tier available for app

				$result->api = new stdClass();
				$result->api->bearer = md5($model->hash . Encryption::decrypt(Text::base64dec($user->get_property("secret_key")))); // bearer = md5 of apikey + secret key
				$result->api->publish = ApiModel::get_publish_url($model->hash, $token); // since token is a unique version of hash this is an easy match

				$result->api->header = new stdClass();
				$result->api->header->html = ""; // todo: this users white label for this app_key
				$result->api->header->css = ""; // todo: this users white label for this app_key

			} else {
				// highest tier available in the subscriptions for this user
				$tier = ProductModel::get_highest_subscribed_tier_for_app($app_id, $model->user);
			}
			$result->licence->tier = $tier;
			$result->app->socket = Config::get("WEBSOCKET_SCHEMA") . Config::get("WEBSOCKET_HOST") . ":" . Config::get("WEBSOCKET_PORT") . "/" . $model->hash;
			ws://coursesuite.ninja.dev/"
		}

		$this->View->renderJSON((array) $result);
	}

	/**
	* @api {post} /createToken/ Generate an access token for lauching apps
	* @apiName Generate
	* @apiGroup Token
	* @apiVersion 0.1.0
	* @apiPermission digest
	* @apiDescription In order to launch apps, you need to generate an access token.
	* 		It validates your access using your api key and secret, and stores the publish-url (if set).
	* 		When opening apps, this key will be validated for concurrency: you can only have as many
	* 		apps running at the same time as you paid for.
	*
	* @apiParam {String} [publish_url] Url to enable publish-to feature (in supported apps)
	*					Apps will POST the generated package to this URL and attach
	*					the header Authorization: Bearer <hash>, where <hash> is
	*					the MD5 of your apikey and secret key, which you can use to
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
	public function createToken()
	{

// need to first get the token associated with the user that logged on using digest
		$hash = parent::requiresAuth();

// lets remember this
		LoggingModel::logMethodCall(__METHOD__, $hash, file_get_contents("php://input"));

// only api users can create a token, so the username will be the the md5 of the referenceId in the subscription table. if it's not a md5, do nothing
		if (!preg_match('/^[a-f0-9]{32}$/', $hash)) return;

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

// if the gave us a publish_url, grab and validate it ising the xss filter (I mean we trust them, right, but only so far)
		if (!$publish_url = Request::post("publish_url", true)) {
			$this->View->renderJSON(array("error" => Text::get("INVALID_URL")));
			return false;
		}

// record the api_request
		ApiModel::record_api_request($hash, $token, $publish_url);

// that's all we have to do
		$this->View->renderJSON(array(
			"token" => $token,
			"seats" => $seats,
			"remaining" => $remaining
		));
	}

	/**
	* @api {post} /info/ List available apps and their launch points
	* @apiName Names
	* @apiGroup App
	* @apiVersion 0.1.0
	* @apiPermission bearer
	* @apiDescription List the app keys and their launch points that your subscription allows
	*
	* @apiHeader {String{32}} Bearer Your api-key
	* @apiHeaderExample {json} Header-Example:
	*     {
	*       "Authorization": "Bearer: 1223b8c30a347321299611f873b449ad"
	*     }
	* @apiSuccess {Array} app List of app properties
	* @apiSuccess {String} app.app_key Internal identifier of app
	* @apiSuccess {String} app.launch Launch url for app.
					You need to replace {token} with the value returned by the /createToken/ method
	* @apiSuccessExample {json} Success-Response:
	*     HTTP/1.1 200 OK
	*     [
	*       	{
	*		"app_key": "docninja",
	*		"launch": "https://www.coursesuite.ninja/launch/docninja/{token}/"
	*	}
	*     }
	*/

	public function info()
	{
		$this->View->renderJSON(AppModel::getAppKeys()); // Config::get('APP_NAMES')
	}

	public function tasks()
	{
		SubscriptionModel::validateSubscriptions();
	}

	/* -------------------------------- FASTSPRING -----------------------------------

	Request all have these named parameters (post):

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

	function subscription(...$params)
	{
		$username = parent::requiresAuth();
		LoggingModel::logMethodCall(__METHOD__, $username, $params, Request::post_debug());
		extract($_POST, EXTR_OVERWRITE, "security_"); // extract security_* as variables
		if (md5($security_data . Config::get('FASTSPRING_SECRET_KEY') == $security_hash)) {

			// $tierid = (int) TierModel::getTierIdByProductName(Request::post("productName")); // short name of subscription in fastspring system

			$fastspringProductId = Request::post("productName");
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
					$model["user_id"] = $userid;
					unset($model["added"]); // allow database default to apply
					$model["endDate"] = empty($endDate) ? null : $endDate;
					$model["referenceId"] = $referenceId;
					$model["subscriptionUrl"] = $subscriptionUrl;
					$model["status"] = $status;
					$model["statusReason"] = $statusReason;
					$model["testMode"] = $testMode;
					$model["active"] = 1;
					$model["info"] = null;
					$model["product_id"] = $product_id;
					$subscription->set_model($model);
					$subscription->save();
					break;

				case "deactivated":
					// deactivation will remove the subscription entry from the database
					$subscription = (new SubscriptionModel)->loadByReference($referenceId);
					$model = $subscription->get_model();
					$model["active"] = 0;
					$model["status"] = $status;
					$model["statusReason"] = $statusReason;
					$subscription->set_model($model);
					$subscription->save();
					break;

				case "failed":
				case "changed":
					if ($endDateSet) {

						$subscription = (new SubscriptionModel)->loadByReference($referenceId);
						$model = $subscription->get_model();
						$model["status"] = $status;
						$model["statusReason"] = $statusReason;
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
	function orders(...$params) {
		$username = parent::requiresAuth();
		LoggingModel::logMethodCall(__METHOD__, $username, $params, Request::post_debug());
		extract($_POST, EXTR_OVERWRITE, "security_"); // extract security_* as variables
		if (md5($security_data . Config::get('FASTSPRING_SECRET_KEY') == $security_hash)) {
			// do something with the order
		}
	}

}
