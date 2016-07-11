<?php

/**
 * undocumented class
 *
 * @package default
 * @author
 **/
class TierModel extends Model{

    function __construct() {
        parent::__construct();
    }

    public static function save($table, $idrow_name, $data_model) {
        return parent::update($table, $idrow_name, $data_model);
    }
    public static function make($table) {
        return parent::create($table);
    }

    /**
     * get the tier record for a row id
     optional: include app model
     */
    public static function getTierById($tier_id, $include_app_model = true, $include_pack_model = false) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT tier_level, name, description, added, store_url, active, price, currency, pack_id
                FROM tiers WHERE tier_id = :tier_id LIMIT 1";
        $query = $database->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $query->execute(array(':tier_id' => $tier_id));
        $result = $query->fetch();
        if (!empty($result)) {
            $return = new stdClass();
            $sql = "SELECT app_id FROM app_tiers WHERE tier_id=:tier_id";
            $query = $database->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $query->execute(array(":tier_id" => $tier_id));
            if ($include_app_model) {
	            $apps = array();
	            foreach ($query->fetchAll() as $app_id) {
	                $apps[$app_id] = AppModel::getAppById($app_id);
	            }
				$return->apps = $apps;
            } else {
	            $return->app_ids = $query->fetchAll(PDO::FETCH_COLUMN, 0);
            }
            if ($include_pack_model) {
	            $sql = "SELECT id, name, kind FROM tier_packs WHERE id=:pack_id";
	            $query = $database->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	            $query->execute(array(":pack_id" => $result->pack_id));
	            $return->pack = $query->fetchAll();
            }
            $return->tier_id = $tier_id;
            $return->tier_level = $result->tier_level;
            $return->name = $result->name;
            $return->description = $result->description;
            $return->added = $result->added;
            $return->store_url = $result->store_url;
            $return->active = ((int) $result->active === 1);
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

    public static function getTierIdByProductName($product_name) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT tier_id FROM tiers WHERE (product_names LIKE :left OR product_names LIKE :mid OR product_names LIKE :right)";
        $query = $database->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $query->execute(array(
        	':left' => $product_name . ',%',
        	':mid' => '%,' . $product_name . ',%',
        	':right' => '%,' . $product_name,
        ));
        return $query->fetchColumn();
    }
    
    
    
    public static function getTierPackByName($tier_name, $include_app_model = false) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT tier_id FROM tiers WHERE pack_id IN (SELECT id FROM tier_packs WHERE name=:name) ORDER BY tier_level";
        $query = $database->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $query->execute(array(':name' => $tier_name));
        $tiers = array();
        foreach ($query->fetchAll() as $row) {
	        $tiers[] = self::getTierById($row->tier_id, $include_app_model);
       }
       return $tiers;
    }

    /**
     * get the tier level (int) for a user by id
     */
    public static function getLevelForUser($user_id) {
	    if (intval($user_id) < 1) return;
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

    public static function getLevelForOrg($org) {
        return 4;
    }

    public static function getAllTiers($onlyactive = false) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT tier_id, tier_level, name, description, store_url, price, currency, period, pack_id
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
                    WHERE tier_id IN (SELECT tier_id FROM app_tiers WHERE app_id = :app_id)
                    $Active
                    ORDER BY tier_level, name
        ";

        $query = $database->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $query->execute(array(
            ":app_id" => (string)$app_id
        ));
        return $query->fetchAll();
    }
    
    public static function getAllAppTiersForPack($pack, $onlyactive = true) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $Active = ($onlyactive == true) ? "AND active = 1" : "";

        $sql = "SELECT tier_id, tier_level, name, description, store_url, price, period
                    FROM tiers
                    WHERE pack_id IN (SELECT id FROM tier_packs WHERE name = :pack)
                    $Active
                    ORDER BY tier_level, name
        ";

        $query = $database->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $query->execute(array(
            ":pack" => (string)$pack
        ));
        return $query->fetchAll();
    }

    public static function getAppFeatures($app_id) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT min_tier_level, feature, details, match_label, mismatch_label
                    FROM app_feature WHERE app_id = :app_id
                    ORDER BY min_tier_level, feature";
        $query = $database->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $query->execute(array(':app_id' => $app_id));
        return $query->fetchAll();
    }

}