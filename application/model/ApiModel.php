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
        public static function encodeToken($key) {
            $enc = Encryption::encrypt($key); // the raw data we are verifying
            return bin2hex($enc); // urlencode(base64_encode($enc)) may still result in url values that break the controller/router logic in this framework
        }

        // GET the token from the url-compatible string value
        public static function decodeToken($token) {
            $bin = hex2bin($token);
            return Encryption::decrypt($bin);
        }

        // CREATE the url-compatible string that represents an Api Key
        public static function encodeApiToken($org, $app) {
            date_default_timezone_set(self::API_TIMEZONE);
            $str = implode("|", array($org, strtotime("now"), $app));
            return self::encodeToken($str);
        }

        // GET the object representation of the data in an Api Key
        public static function decodeApiToken($token) {
            date_default_timezone_set(self::API_TIMEZONE);
            $str = self::decodeToken($token);
            $arr = explode("|", $str);
            $result = new stdClass();
            $result->org = $arr[0];
            $result->timestamp = intval ( isset($arr[1]) ? $arr[1] : 0 );
            $result->app = isset($arr[2]) ? $arr[2] : "";
            $result->valid = ($result->timestamp > strtotime(self::API_VALID_TIMEFRAME));
            return $result;
        }

} // END class ApiModel