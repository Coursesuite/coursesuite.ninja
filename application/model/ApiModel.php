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
    public static function encodeApiToken($orgModel, $appModel, $publish_url, $digest_user)
    {

        // we need to store why and when we encoded this - so we can do request limiting
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("INSERT INTO api_requests (digest_user, org, app, publish_url, token, `month`) VALUES (:user, :org, :app, :url, :key, MONTH(CURRENT_DATE()))");
        $query->execute(array(
            ":user" => $digest_user,
            ":org" => $orgModel->org_id,
            ":app" => $appModel->app_id,
            ":url" => $publish_url,
            ":key" => $token,
        ));
        $rowId = $database->lastInsertId();

        // the token is the encrypted version of the rowId inserted above
        return self::encodeToken($rowId);
    }

    // GET the object representation of the data in an Api Key
    public static function decodeApiToken($token)
    {

        $result = new stdClass();
        $id = self::decodeToken($token);

        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("SELECT org, app, publish_url, digest_user FROM api_requests WHERE id=:id AND created<CURRENT_TIMESTAMP LIMIT 1");
        $query->execute(array(
            ":id" => $id
        ));
        $row = $query->fetch();
        if ($row) {
            $result->org = OrgModel::getRecord($row->org);

            // the digest_user to check is the one who CREATED the token; since we are now under the guise of tokenuser
            $digest_user = $row->digest_user;

            $query = $database->prepare("SELECT usage_cap WHERE digest_user = :user");
            $query->execute(array(":user" => $digest_user));
            $cap = intval($query->fetchColumn()); // how many ues per month this user can have

            $query = $database->prepare("SELECT COUNT(1) FROM api_requests WHERE `month` = MONTH(CURRENT_DATE()) and digest_user = :user");
            $query->execute(array(":user" => $digest_user));
            $used = intval($query->fetchColumn()); // how many times this month this user has generated an apitoken

            if ($used > $cap) {
                $result->valid = false; // exceeded usage cap
                $result->reason = Text::get("EXCEEDED_MONTHLY_CAP");
            } else {
                $result->valid = true;
            }
        }
        return $result;
    }

} // END class ApiModel
