<?php
/**
 * undocumented class
 *
 * @package default
 * @author
 **/
class ApiModel
{

    const API_TIMEZONE = "UTC";
    const API_VALID_TIMEFRAME = "-1 day";

    // CREATE the url-compatible string that represents a token
    public static function encodeToken($key)
    {
        $enc = Encryption::encrypt($key); // the raw data we are verifying
        return bin2hex($enc); // urlencode(base64_encode($enc)) may still result in url values that break the controller/router logic in this framework
    }

    // GET the token from the url-compatible string value
    public static function decodeToken($token)
    {
        $bin = hex2bin($token);
        return Encryption::decrypt($bin);
    }

    // CREATE the url-compatible string that represents an Api Key
    public static function encodeApiToken($org, $app, $publish_url, $digest_user)
    {

        // the token itself is effectively nothing important - just the timestamp
        date_default_timezone_set(self::API_TIMEZONE);
        $str = implode("|", array($org, strtotime("now"), $app));
        $token = self::encodeToken($str);

        // a bearer token is the md5($token . Config::get("HMAC_SALT"));
        // so how do we safely get the salt to the end app?

        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "INSERT INTO api_requests (digest_user, org, app, publish_url, token, `month`) VALUES (:user, :org, :app, :url, :key, MONTH(CURRENT_DATE()))";
        $query = $database->prepare($sql);
        $query->execute(array(
            ":user" => $digest_user,
            ":org" => $org,
            ":app" => $app,
            ":url" => $publish_url,
            ":key" => $token,
        ));

        return $token;
    }

    // GET the object representation of the data in an Api Key
    public static function decodeApiToken($token, $digest_user)
    {
        date_default_timezone_set(self::API_TIMEZONE);
        $str = self::decodeToken($token);
        $arr = explode("|", $str);

        $timestamp = intval(isset($arr[1]) ? $arr[1] : 0);
        $valid = ($result->timestamp > strtotime(self::API_VALID_TIMEFRAME));

        $result = new stdClass();
        $result->org = OrgModel::getApiModel($arr[0]);
        $result->app = isset($arr[2]) ? $arr[2] : "";
        $result->timestamp = $timestamp;

        if ($valid) {
            $result->reason = "";

            $database = DatabaseFactory::getFactory()->getConnection();
            $query = $database->prepare("SELECT usage_cap WHERE digest_user = :user");
            $query->execute(array(":user" => $digest_user));
            $cap = intval($query->fetchColumn()); // how many ues per month this user can have

            $query = $database->prepare("SELECT COUNT(1) FROM api_requests WHERE `month` = MONTH(CURRENT_DATE()) and digest_user = :user");
            $query->execute(array(":user" => $digest_user));
            $used = intval($query - fetchColumn()); // how many times this month this user has generated an apitoken

            if ($used > $cap) {
                $valid = false; // exceeded usage cap
                $result->reason = Text::get("EXCEEDED_MONTHLY_CAP");
            }
        }

        $result->valid = $valid;
        return $result;
    }

} // END class ApiModel
