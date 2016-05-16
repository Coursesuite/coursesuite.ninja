<?php

/**
 * undocumented class
 *
 * @package default
 * @author
 **/
class TierModel {

    /**
     * get the tier record for a row id
     optional: include app model
     */
    public static function getTierById($tier_id, $include_app_model = true) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT tier_level, app_ids, name, description, added, store_url, active
                FROM tiers WHERE tier_id = :tier_id LIMIT 1";
        $query = $database->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $query->execute(array(':tier_id' => $tier_id));
        $result = $query->fetch();
        if (!empty($result)) {
            $return = new stdClass();
            if ($include_app_model) {
	            $apps = array();
	            foreach (explode(",", $result->app_ids) as $app_id) {
	                $apps[$app_id] = AppModel::getAppById($app_id);
	            }
				$return->apps = $apps;
            }
            $return->tier_id = $tier_id;
            $return->tier_level = $result->tier_level;
            $return->name = $result->name;
            $return->description = $result->description;
            $return->added = $result->added;
            $return->store_url = $result->store_url;
            $return->active = ($result->active === 1);
            return $return;
        }
        return false;
    }
    
    public static function getTierIdByName($tier_name) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT tier_id FROM tiers WHERE name = :name";
        $query = $database->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $query->execute(array(':name' => $tier_name));
        return $query->fetchColumn();
	    
    }

    /**
     * get the tier level (int) for a user by id
     */
    public static function getLevelForUser($user_id) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT T.tier_level FROM
            tiers T INNER JOIN
            subscriptions S
            on T.tier_id = S.tier_id
            WHERE S.user_id=:user_id
            AND S.active = 1
            ORDER BY S.added DESC
            LIMIT 1";
        $query = $database->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $query->execute(array(':user_id' => $user_id));
        return $query->fetch()->tier_level;
    }

    public static function getAllTiers($onlyactive = false) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT tier_id, tier_level, app_ids, name, description, store_url
                    FROM tiers";
        if ($onlyactive == true) $sql .= " WHERE active = 1";
        $sql .= " ORDER BY tier_level, name";
        $query = $database->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $query->execute();
        return $query->fetchAll();
    }

    /**
    *   return tiers that contain a given app
     * the apps tied to a tier are stored in a comma-separated string, we need to LIKE match them
     * @param $app_id - an int, we need to cast as string since LIKE uses string matching
     */
    public static function getAllAppTiers($app_id, $onlyactive = true) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $Active = ($onlyactive == true) ? "AND active = 1" : "";
        $sql = "SELECT tier_id, tier_level, name, description, store_url, price, period
                    FROM tiers
                    WHERE (app_ids = :single
                    OR app_ids LIKE :first
                    OR app_ids LIKE :middle
                    OR app_ids LIKE :last)
                    $Active
                    ORDER BY tier_level, name
        ";
        $query = $database->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $query->execute(array(
            ":single" => (string)$app_id,
            ":first" => (string)$app_id . ',%',
            ":middle" => '%,' . (string)$app_id . ',%',
            ":last" => '%,' . (string)$app_id,
        ));
        return $query->fetchAll();
    }

    public static function getAppTierFeatures($app_id) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT level, feature, details, match_label, mismatch_label
                    FROM app_tier_feature WHERE app = :app_id
                    ORDER BY level, feature";
        $query = $database->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $query->execute(array(':app_id' => $app_id));
        return $query->fetchAll();
    }

}