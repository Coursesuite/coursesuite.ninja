<?php

/**
 * undocumented class
 *
 * @package default
 * @author
 **/
class TierModel
{

    public static function getTierById($tier_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT tier_level, app_ids, name, description, added, store_url, active
                FROM tiers WHERE tier_id = :tier_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':tier_id' => $tier_id));

        $result = $query->fetch();
        if (!empty($result)) {
            $return = new stdClass();
            $apps = array();
            foreach (explode(",", $result->app_ids) as $app_id) {
                $apps[$app_id] = AppModel::getAppById($app_id);
            }

            $return->tier_id = $tier_id;
            $return->tier_level = $result->tier_level;
            $return->name = $result->name;
            $return->description = $result->description;
            $return->added = $result->added;
            $return->store_url = $result->store_url;
            $return->active = ($result->active === 1);
            $return->apps = $apps;

            return $return;

        }

        return false;

    }
} // END class TierModel