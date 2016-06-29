<?php

/**
 * undocumented class
 *
 * @package default
 * @author
 **/
class SubscriptionModel
{

	/*
		tasks
		-----
		method 															| cooldown | lastrun | running
		SubscriptionModel::validateSubscription                         | 1440 	   | 0 	     | 0
	*/

	// ensure that user subscription active statuses are persisted properly
	public static function validateSubscriptions() {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->query("select count(id) from systasks where running=0 and task='validateSubscriptions' and lastrun < timestamp(date_add(now(), INTERVAL -1 DAY))");
        if ("1" == $query->fetchColumn()) {

	        // prevent duplicate processes
	        $database->query("update systasks set running=1 where task='validateSubscriptions'");

	        //  expire subscriptions older than today
	        $database->query("update subscriptions set active = 0 where datediff(endDate, now()) < 0");

			// begin subscriptions with a null end date (e.g. changed record)
	        $database->query("update subscriptions set active = 1 where endDate is null");

			// work out if the user has been broadcast an alert saying their subscription will expire (ignore if they have dismissed it)

			// sent a broadcast to the user

			// prevent re-running this function for a day
	        $database->query("update systasks set running=0, lastrun=timestamp(now()) where task='validateSubscriptions'");
	    }
	}

    public static function getAllSubscriptions($userid = 0, $include_app_model = false, $limit = false, $include_user = true, $include_pack = false) {

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT subscription_id, tier_id, user_id, added, endDate, referenceId, subscriptionUrl, status, statusReason, testMode, active FROM subscriptions";
        $params = array();
        if ($userid > 0) {
	        $sql .= " WHERE user_id = :user_id";
	        $params[":user_id"] = $userid;
	    }
	    if ($limit == true) $sql .= " LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute($params);
        $subscriptions = $query->fetchAll();
        foreach ($subscriptions as &$subscription) {
	        if ($include_user) $subscription->user = UserModel::getPublicProfileOfUser($subscription->user_id);
	        $subscription->tier = TierModel::getTierById($subscription->tier_id, $include_app_model, $include_pack);
	        $subscription->subscriptionUrl = Encryption::decrypt(Text::base64dec($subscription->subscriptionUrl));
        }
        if ($limit == true && count($subscriptions) > 0) return $subscriptions[0];
        return $subscriptions;
    }

    public static function addSubscription($userid, $tierid, $endDate, $referenceId, $subscriptionUrl, $status, $statusReason, $testMode) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "INSERT INTO subscriptions (user_id, tier_id, endDate, referenceId, subscriptionUrl, status, statusReason, testMode, active)
        	VALUES (:user_id, :tier_id, :end_date, :ref_id, :sub_id, :status, :status_reason, :test_mode, :active)";
        $query = $database->prepare($sql);
        $params = array(
	        ":user_id" => intval($userid),
	        ":tier_id" => intval($tierid),
	        ":end_date" => (isset($endDate)) ? $enddate : null,
	        ":ref_id" => $referenceId,
	        ":sub_id" => $subscriptionUrl,
	        ":status" => (isset($status)) ? $status : null,
	        ":status_reason" => (isset($statusReason)) ? $statusReason : null,
	        ":test_mode" => intval($testMode),
	        ":active" => ($status == 'active') ? 1 : 0
        );
		// LoggingModel::logInternal("addSubscription Query", $sql, print_r($params, true));
        return $query->execute($params);
    }

} // END class SubscriptionModel