<?php

/**
 * Class Api
 * This controller is meant to be used programatically by external services and sites
 * Requests are authenticated using digest authentication
 * like a proper 404 response with an additional HTML page behind when something does not exist.
 */
class ApiController extends Controller
{

    /*
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();

	    // LoggingModel::logMessage("ApiController::__construct");

        $digest = new \Rakshazi\Digestauth;
        $valid = $digest->setUsers(Config::get('DIGEST_USERS'))->setRealm("CourseSuite")->enable();
        if (!$valid) {
            header('HTTP/1.1 401 Unauthorized');
            // LoggingModel::logMethodCall(__METHOD__, $digest->user, "authentication", "failed", $_SERVER['PHP_AUTH_DIGEST']);
            die("Digest Authentication Failed");
        }
        $this->username = $digest->user;

	    // LoggingModel::logMessage("ApiController::__constructed properly; user was ". $this->username);

    }

    /*
     *  verify an access token for a given app (id); effectively this is the encrypted php session id - is it still active?
     *
     * @access public
     * @static false
     * @param appkey - the appkey column from the apps table
     * @param token - url encoded base64 string of the encrypted php sessionid
     * @return JSON
     * @see Session::isActiveSession()

    * input is urlencoded/base64/encrypted: YWUxYjU4NjhjMzFmMDZhYjFjN2VkNDViZmYyM2JmNDMwNzRmYjI4MjEwYWM0NTlmZjMyOTE2NWQxN2E2NzZhYWIPdmxq1G3BhkCE8wKCcKmr5E3%2BdJ89p1wvoX2PAPRivtX1BSmIVoQAMe6f5lV3UQ%3D%3D
    * output is a php session id, e.g. mog1suctkfbm5rii8fo2pla8j6

     */
    public function verifyToken($appkey, $token) {
            $session_id = ApiModel::decodeToken($token);
            $tokenIsValid = Session::isActiveSession($session_id, $appkey);
            $result = array(
                'authuser' => $this->username,
                'appkey' => $appkey,
                'valid' => $tokenIsValid,
                'api' => false,
                'tier' => TierModel::getLevelForUser(Session::UserIdFromSession($session_id))
            );
            LoggingModel::logMethodCall(__METHOD__, $this->username, $appkey, $token, $tokenIsValid, $result);
            $this->View->renderJSON($result);
    }

    /*
     *  verify an api token for a given app;
     *
     * @access public
     * @static false
     * @param key - base64 encoded encrypted string
     * @return JSON
     * @see ApiController::generateApiKey()
     */

    public function verifyApiKey($key) {
        $data = ApiModel::decodeApiToken($key);
        $result = array(
            'authuser' => $this->username,
            'appkey' => $data->app,
            'valid' => $data->valid,
            'api' => true,
            'tier' => 4,                             // some way of pulling this value?
            'org' => $data->org
        );
        LoggingModel::logMethodCall(__METHOD__, $this->username, $data->org, $data->app, $result);
        $this->View->renderJSON($result);
    }

    /*
     *  generate an api token, optionally for a given app;
     *
     * @access public
     * @static false

     * @return JSON
     * @see ApiController::generateApiKey()
     */
    public function generateApiKey($org, $app = "") {
        if (!empty($app)) {
            if (!in_array($app, AppModel::getAppKeys())) {
                $this->View->renderJSON(array("error" => "App name not found"));
                return false;
            }
        }
        $token = ApiModel::encodeApiToken($org, $app);
        $data = array("token" => $token);
        LoggingModel::logMethodCall(__METHOD__, $this->username, $org, $app, $data);
        $this->View->renderJSON($data);
    }

    /*
     * get a list of the app names
     */
    public function appNames() {
        $this->View->renderJSON(AppModel::getAppKeys());  // Config::get('APP_NAMES')
    }
    
    public function tasks() {
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

	public function subscription(...$params) {
        LoggingModel::logMethodCall(__METHOD__, $this->username, $params, Request::post_debug());
	    extract ($_POST, EXTR_OVERWRITE, "security_"); // extract security_* as variables
	    if (md5($security_data . Config::get('FASTSPRING_SECRET_KEY') == $security_hash)) {
		    
	    	$tierid = (int) TierModel::getTierIdByName(Request::post("productName")); // short name of subscription in fastspring system
	    	$userid = (int) Encryption::decrypt(Text::base64dec(Request::post("referrer"))); // passes back whatever we send it during checkout, same re-sent each re-bill
	    	$endDate = Request::post("subscriptionEndDate"); // date | empty
	    	$endDateSet = isset($endDate); // if set then the subscription is inactive after this date
	    	$referenceId = Encryption::encrypt(Request::post("referenceId")); // unique order id for this transaction
	    	$status = Request::post("status"); // active | inactive
	    	$statusReason = Request::post("statusReason"); // canceled-non-payment | completed | canceled | ""
	    	$testMode = (Request::post("testmode") == "true") ? 1 : 0; // true | false

		    switch ($params[0]) {
			    case "activated":
				case "deactivated":
			    	// normal new subscription or deactivation caused by cancellation, payment failure
					SubscriptionModel::addSubscription($userid, $tierid, $endDate, $referenceId, $status, $statusReason, $testMode);
			    	break;

				case "failed":
				case "changed":
					// do we need to log any of these statuses?
					if ($endDateSet) {
						if ($status == "inactive") {} // has become inactive and will deactivate soon
						if ($statusReason == "canceled") {} // we should next or soon see a deactivated
					} else if ($status == "active") { 
						// e.g. changed from failed back to active
					}
					break;
		    }

		}
	}

}