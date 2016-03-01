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

        $digest = new \Rakshazi\Digestauth;
        $valid = $digest->setUsers(Config::get('DIGEST_USERS'))->setRealm("CourseSuite")->enable();
        if (!$valid) {
            header('HTTP/1.1 401 Unauthorized');
            // LoggingModel::logMethodCall(__METHOD__, $digest->user, "authentication", "failed", $_SERVER['PHP_AUTH_DIGEST']);
            die("Digest Authentication Failed");
        }
        $this->username = $digest->user;

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
            $data = array('authuser' => $this->username, 'appkey' => $appkey, 'valid' => $tokenIsValid);
            LoggingModel::logMethodCall(__METHOD__, $this->username, $appkey, $token, $tokenIsValid, $data);
            $this->View->renderJSON($data);
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
        $result = array("valid" => $data->valid);
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


    /* -------------------------------- FASTSPRING ----------------------------------- */


    // FASTSPRING - User has bought a new subscription
    public function subscriptionBuy(...$params) {
        LoggingModel::logMethodCall(__METHOD__, $this->username, $params);

    }

    // FASTSPRING - User has cancelled their subscription
    public function subscriptionCancel(...$params) {
        LoggingModel::logMethodCall(__METHOD__, $this->username, $params);

    }

    // FASTSPRING - User monthly payment failed
    public function subscriptionUpdateFailed(...$params) {
        LoggingModel::logMethodCall(__METHOD__, $this->username, $params);

    }

    // FASTSPRING - User monthly payment succeded
    public function subscriptionUpdateSuccess(...$params) {
        LoggingModel::logMethodCall(__METHOD__, $this->username, $params);

    }

}