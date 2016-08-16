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
     * An Api call will create a sessionid, which puts a row in the session_data table. We don't want to persist this, so delete it afterwards
     */
    public function __destruct()
    {
        //    Session::clean();
        //   Session::destroy();
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

     * input is bin2hex/encrypted: 663762623633626661393032373166353964393935333862313135336463386537646230636138336133343861633062323163343631643039386439353461334812d03b8951695b359bfbb7b8833e23684aea24450339659f1d02842e7c65abd49ed70ca651bb871aafecb74fbc145e
     * output is a php session id, e.g. mog1suctkfbm5rii8fo2pla8j6

     */
    public function verifyToken($appkey, $token)
    {
        $session_id = ApiModel::decodeToken($token);
        $tokenIsValid = Session::isActiveSession($session_id, $appkey);
        $userObj = Session::UserDetailsFromSession($session_id);
        $result = array(
            'authuser' => $this->username,
            'appkey' => $appkey,
            'valid' => $tokenIsValid,
            'api' => false,
            'tier' => TierModel::getLevelForUser($userObj->user_id), // LEVEL (e.g. 0=bronze, 1=silver, etc) not ID; -1 means none
            'username' => $userObj->user_name,
            'useremail' => $userObj->user_email,
            'trial' => ($userObj->account_type == 3) ? true : false,
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

    public function verifyApiKey($key)
    {
        $data = ApiModel::decodeApiToken($key, $this->username);
        // org
        // app
        // timestamp
        // valid
        // reason
        $result = array(
            'authuser' => $this->username,
            'appkey' => $data->app,
            'valid' => $data->valid,
            'api' => true,
            'tier' => TierModel::getLevelForOrg($data->org),
            'error' => $data->reason,
            'org' => $data->org,
            'trial' => false,
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
    public function generateApiKey($org, $app = "", $publish_url = "")
    {
        if (!empty($app)) {
            if (!in_array($app, AppModel::getAppKeys())) {
                $this->View->renderJSON(array("error" => "App name not found"));
                return false;
            }
        }
        $token = ApiModel::encodeApiToken($org, $app, $publish_url, $this->username);
        $data = array("token" => $token);
        LoggingModel::logMethodCall(__METHOD__, $this->username, $org, $app, $data);
        $this->View->renderJSON($data);
    }

    /*
     * get a list of the app names
     */
    public function appNames()
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

    public function subscription(...$params)
    {
        LoggingModel::logMethodCall(__METHOD__, $this->username, $params, Request::post_debug());
        extract($_POST, EXTR_OVERWRITE, "security_"); // extract security_* as variables
        if (md5($security_data . Config::get('FASTSPRING_SECRET_KEY') == $security_hash)) {
            $tierid = (int) TierModel::getTierIdByProductName(Request::post("productName")); // short name of subscription in fastspring system
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
            $oldTier = SubscriptionModel::getCurrentSubscription($userid)->tier_id;
            switch ($params[0]) {
                case "activated":
                    SubscriptionModel::addSubscription($userid, $tierid, $endDate, $referenceId, $subscriptionUrl, $status, $statusReason, $testMode);
                    break;
                case "deactivated":
                    // deactivation will remove the subscription entry from the database
                    SubscriptionModel::updateSubscriptionStatus($referenceId, $status);
                    break;
                case "failed":
                case "changed":

                    // do we need to log any of these statuses?
                    if ($endDateSet) {
                        if ($status == "inactive") {} // has become inactive and will deactivate soon
                        if ($statusReason == "canceled") {} // we should next or soon see a deactivated
                    }
                    if ($status == "active") {
                        if ($tierid != $oldTier) {
                            // subscription upgraded/downgraded
                            SubscriptionModel::updateSubscriptionTier($referenceId, $tierid, $oldTier);
                            $mail = new Mail();
                            $mail->sendMail(Config::get('EMAIL_SUBSCRIPTION'), Config::get('EMAIL_VERIFICATION_FROM_EMAIL'), 'SubscriptionInfo', 'User: ' . $userid . ' Updated their subscription', 'User: ' . $userid . ' Changed their subscription from ' . TierModel::getTierNameById($oldTier) . ' to ' . TierModel::getTierNameById($tierid));
                            break;
                        }
                    }
                    break;
            }
        }
    }

}
