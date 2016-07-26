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

    public static function getAllSubscriptions($userid = 0, $include_app_model = false, $limit = false, $include_user = true, $include_pack = false, $order = 'added') {

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT subscription_id, tier_id, user_id, added, endDate, referenceId, subscriptionUrl, status, statusReason, testMode, active, info FROM subscriptions";
        $params = array();
        if ($userid > 0) {
	        $sql .= " WHERE user_id = :user_id";
	        $sql .= " ORDER BY :order";
	        $params[":user_id"] = $userid;
	        $params[":order"] = $order;
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

    // Gets the users currently active subscription 
    // Returns PDO Object
    public static function getCurrentSubscription($userid) {
    	$database = DatabaseFactory::getFactory()->getConnection();
    	$sql = "SELECT tier_id, referenceId, active FROM subscriptions WHERE user_id = :userid AND status = 'active' ";
    	$params = array(
    		":userid" => $userid
    		);
    	$query = $database->prepare($sql);
    	$query->execute($params);
        $result = $query->fetch();
        return $result;
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

    public static function removeSubscription($referenceId) {
    	$database = DatabaseFactory::getFactory()->getConnection();
    	$sql = "DELETE FROM subscriptions WHERE referenceId = :referenceId LIMIT 1";
    	$query = $database->prepare($sql);
    	$params = array(
    		":referenceId" => $referenceId
    		);
    	$query->execute($params);
    }

    public static function updateSubscriptionTier($referenceId, $newTier, $oldTier) {
    	$database = DatabaseFactory::getFactory()->getConnection();
    	$sql = "UPDATE subscriptions SET tier_id = :newTier, info = :oldTier WHERE referenceId = :referenceId";
    	$query = $database->prepare($sql);
    	$params = array(
    		":referenceId" => $referenceId,
    		":newTier" => $newTier,
    		":oldTier" => 'Changed from ' . TierModel::getTierNameById($oldTier)
		);
    	$query->execute($params);
    }

    public static function updateSubscriptionStatus($referenceId, $status) {
    	$database = DatabaseFactory::getFactory()->getConnection();
    	$sql = "UPDATE subscriptions SET status = :status WHERE referenceId = :referenceId";
    	$query = $database->prepare($sql);
    	$params = array(
    		":status" => $status,
    		":referenceId" => $referenceId
    		);
    	$query->execute($params);
    }

    public static function previouslySubscribed($user_id) {
    	$database = DatabaseFactory::getFactory()->getConnection();
    	$sql = "SELECT tier_id FROM subscriptions WHERE user_id = :user_id AND status = 'inactive'";
    	$query = $database->prepare($sql);
    	$params = array(
    		":user_id" => $user_id
    		);
    	$query->execute($params);
    	$result = $query->fetchAll();
    	if (count($result) > 0) {
			return $result[count($result)-1]->tier_id;
		}	
    }

} // END class SubscriptionModel