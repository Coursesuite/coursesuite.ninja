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
		method 									| cooldown | lastrun | running
		SubscriptionModel::validateSubscription | 1440 	   | 0 	     | 0
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

    public static function getUserCurrentSubscription($userid = 0, $limit = false)
    {
        if ($userid === 0) $userid = Session::get('user_id');
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT subscription_id, tier_id, user_id, added FROM subscriptions WHERE user_id = :userid and active=1 ORDER BY added DESC";
        if ($limit === TRUE) $sql .= " LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(":userid" => $userid));


        // don't see the point of adding user_id to this model, or UserModel::() since it's being user to look up the subscription
        $subscriptions = array();
        foreach ($query->fetchAll() as $subscription) {
            $subscriptions[] = array(
                "tier" => TierModel::getTierById($subscription->tier_id, false),
                "added" => $subscription->added
            );
        }
        return $subscriptions;
    }

    public static function getAllUsersSubscriptions() {

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT subscription_id, user_id, tier_id, added, active, data FROM subscriptions";
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
            $subscriptions[$subscription->subscription_id]->data = Encryption::decrypt($subscription->data);
        }
        return $subscriptions;

    }

    public static function getUserSubscriptions($userid) {

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT subscription_id, user_id, tier_id, added, active, data FROM subscriptions WHERE user_id = :userid LIMIT 1";
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
            $return->data = Encryption::decrypt($result->data);
            return $return;
        }
        return false;
    }
    
    public static function addSubscription($userid, $tierid, $endDate, $referenceId, $status, $statusReason, $testMode) {
	    // https://sites.fastspring.com/coursesuite/order/s/COU160407-9674-15278S

        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "INSERT INTO subscriptions (user_id, tier_id, endDate, referenceId, status, statusReason, testMode, active)
        	VALUES (:user_id, :tier_id, :end_date, :ref_id, :status, :status_reason, :test_mode, :active)";
        $query = $database->prepare($sql);
        return $query->exec(array(
	        ":user_id" => $userid,
	        ":tier_id" => $tierid,
	        ":end_date" => $endDate,
	        ":ref_id" => $referenceId,
	        ":status" => $status,
	        ":status_reason" => $statusReason,
	        ":test_mode" => $testMode,
	        ":active" => ($status == 'active') ? 1 : 0
        ));
	    
    }

} // END class SubscriptionModel