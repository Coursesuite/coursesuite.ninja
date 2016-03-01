<?php

/**
 * undocumented class
 *
 * @package default
 * @author
 **/
class SubscriptionModel
{

    public static function getUserCurrentSubscription($userid = 0, $limit = false)
    {
        if ($userid === 0)
                $userid = Session::get('user_id');
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT subscription_id, tier_id, user_id, added FROM subscriptions WHERE user_id = :userid and active=1 ORDER BY added DESC";
        if ($limit === TRUE) $sql .= " LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(":userid" => $userid));

        // don't see the point of adding user_id to this model, or UserModel::() since it's being user to look up the subscription
        $subscriptions = array();
        foreach ($query->fetchAll() as $subscription) {
            $result = new stdClass();
            $subscriptions[$subscription->subscription_id]->added = $subscription->added;
            $subscriptions[$subscription->subscription_id]->tier = TierModel::getTierById($subscription->tier_id);
        }
        return $subscriptions;
    }

    public static function getAllUsersSubscriptions() {

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT subscription_id, order_id, user_id, tier_id, added, active, data FROM subscriptions";
        $query = $database->prepare($sql);
        $query->execute();

        $subscriptions = array();
        foreach ($query->fetchAll() as $subscription) {
            $subscriptions[$subscription->subscription_id] = new stdClass();
            $subscriptions[$subscription->subscription_id]->order_id = $subscription->order_id;
            $subscriptions[$subscription->subscription_id]->user = UserModel::getPublicProfileOfUser($subscription->user_id);
            $subscriptions[$subscription->subscription_id]->tier = TierModel::getTierById($subscription->tier_id);
            $subscriptions[$subscription->subscription_id]->added = $subscription->added;
            $subscriptions[$subscription->subscription_id]->active = ($subscription->added === 1);
            $subscriptions[$subscription->subscription_id]->data = $subscription->data;
        }
        return $subscriptions;

    }

    public static function getUserSubscriptions($userid) {

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT subscription_id, order_id, user_id, tier_id, added, active, data FROM subscriptions WHERE user_id = :userid LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(":userid"=>$userid));

        $result = $query->fetch();
        if (!empty($result)) {
            $return = new stdClass();
            $return->order_id = $result->order_id;
            $return->user = UserModel::getPublicProfileOfUser($result->user_id);
            $return->tier = TierModel::getTierById($result->tier_id);
            $return->added = $result->added;
            $return->active = ($result->added === 1);
            $return->data = $result->data;
            return $return;
        }
        return false;
    }

} // END class SubscriptionModel