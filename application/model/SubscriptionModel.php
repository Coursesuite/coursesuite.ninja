<?php

class SubscriptionModel extends Model
{
	CONST TABLE_NAME = "subscriptions";
	CONST ID_ROW_NAME = "subscription_id";

	protected $data_model;
	protected $product_model;
	protected $account_model;

	public function get_model($include_product_model = false, $include_account_model = false, $include_apps_model = false)
	{
		$data = $this->data_model;
		if ($include_product_model) {
			$data->Product = self::get_product($include_apps_model);
		}
		if ($include_account_model) {
			$data->Account = self::get_account();
		}
		return (array) $data;
	}

	public function get_product($include_apps_model = false)
	{
		if (!isset($this->product_model)) {
			$this->product_model = (new ProductBundleModel("id", $this->data_model->product_id))->get_model($include_apps_model);
		}
		return $this->product_model;
	}

	public function get_account()
	{
		if (!isset($this->account_model)) {
			$this->account_model = (new AccountModel("id",$this->data_model->user_id))->get_model();
		}
		return $this->account_model;
	}

	public function set_model($data)
	{
		if (isset($data->Product)) unset($data->Product);
		if (isset($data->Account)) unset($data->Account);
		$this->data_model = $data;
	}

	public function __construct($row)
	{
		parent::__construct();
		if (preg_match('/^[a-f0-9]{32}$/', $row)) {
			// $this->data_model = parent::Read(self::TABLE_NAME, "md5(referenceId)=:hash OR md5(concat(referenceId,:salt))=:hash", array(":hash"=>$row,":salt"=>Config::get("HMAC_SALT")),"*",true); // [0]; // 0th of a fetchall
			$this->data_model = parent::Read(self::TABLE_NAME, "md5(referenceId)=:hash", [":hash"=>$row], "*", true); // [0]; // 0th of a fetchall
		} else if (is_numeric($row) && $row > 0) {
			self::load($row);
		} else if (is_numeric($row) && $row === 0) {
			self::create(self::TABLE_NAME);
		}
		return $this;
	}

	public function delete($id = 0)
	{
		if ($id > 0) {
			parent::Destroy(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $id));
		} else {
			$idname = self::ID_ROW_NAME;
			parent::Destroy(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $data_model->$idname));
		}
	}

	public function load($id)
	{
		$this->data_model = parent::Read(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $id),"*",true); // [0]; // 0th of a fetchall
		return $this;
	}

	public function loadByReference($referenceId)
	{
		$this->data_model = parent::Read(self::TABLE_NAME, "referenceId=:id", array(":id" => $referenceId))[0]; // 0th of a fetchall
		return $this;
	}

	public function make()
	{
		$this->data_model = parent::Create(self::TABLE_NAME);
		return $this;
	}

	public function save()
	{
		return parent::Update(self::TABLE_NAME, self::ID_ROW_NAME, $this->data_model);
	}

	// a cron routine that expires old subscription records based on the date (can only be run once per day, so it's reasonably safe to poll)
	public static function validateSubscriptions()
	{
		$database = DatabaseFactory::getFactory()->getConnection();
		$database->query("update subscriptions set active = 1, status='active' where endDate is null");
		$database->query("update subscriptions set active = 0 where status <> 'active' and active = 1"); // fix mismatched booleans
		$database->query("update subscriptions set active = 0, status='inactive' where datediff(endDate, now()) < 0"); // deactivate
		$database->query("update subscriptions set active = 1, status='active' where datediff(endDate, now()) > 0"); // reactivate
		$database->query("update subscriptions set active = 0, status='inactive', endDate=now() where (statusReason = 'canceled' or statusReason = 'canceled-non-payment') and enddate is null and datediff(added,date_add(now(), interval -1 month)) < 0"); // cancelled but didn't expire after a month?
	}

	// get the refid of the subscription with the most concurrency, for an app/user
	/* EXPLANATION OF "THE JOIN"
	* find_in_set returns the INDEX of the value it finds using a string comparator
	* so if the index is zero, the joined app wasn't found on the subsequent where filter, therefore the join fails and invalidates the result
	* if the app matches on the filter, the index will be found and therefore will match on a 1=1 join - matches all, which is then filtered
	*/
	public static function get_refid_for_app_for_user($app_key, $user_id, $include_api = false) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$idname = self::ID_ROW_NAME;
		$filter = ($include_api === false) ? "AND pb.product_key NOT LIKE 'api-%'" : "";
		$query = $database->prepare("
		    SELECT s.referenceId FROM subscriptions s
		    INNER JOIN product_bundle pb ON s.product_id = pb.id
		    INNER JOIN apps a on 1 = (find_in_set(cast(a.app_id AS nchar), pb.app_ids) > 0)
		    WHERE a.app_key = :app_key
		    AND s.status = 'active'
		    AND s.user_id = :user_id
		    $filter
		    ORDER BY pb.concurrency DESC
		    LIMIT 1
		");
		$query->execute([":app_key"=>$app_key, ":user_id"=>$user_id]);
		return $query->fetchColumn(0);
	}

	// a routine used by the store info page to know the models of each of a users' subscriptions, if any, for the app being rendered
	// // returns array of models
	public static function get_subscriptions_for_user_for_app($user_id, $app_id, $include_api = false) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$idname = self::ID_ROW_NAME;
		$filter = ($include_api === false) ? "AND pb.product_key NOT LIKE 'api-%'" : "";
		$query = $database->prepare("
			SELECT $idname FROM subscriptions WHERE user_id = :user_id AND status='active' AND product_id IN (
				SELECT pb.id FROM apps a JOIN product_bundle pb ON 1 = (find_in_set(cast(a.app_id AS nchar),pb.app_ids) > 0)
				WHERE a.app_id = :app_id
				$filter
			)
		");
		$query->execute(array(":app_id"=>$app_id, ":user_id"=>$user_id));
		$results = [];
		foreach ($query->fetchAll() as $row) {
			$results[] = (new SubscriptionModel($row->$idname))->get_model(true,false);
		}
		return $results;
	}

	// $database = DatabaseFactory::getFactory()->getConnection();
	// $idname = self::ID_ROW_NAME;
	// $sql = "select $idname FROM subscriptions WHERE user_id = :user_id AND status = 'active' AND product_id IN (
	// 	SELECT p.`id`
	// 	FROM product p
	// 	INNER JOIN bundle b ON (p.`entity` = 'bundle' AND p.`entity_id` = b.`id`)
	// 	INNER JOIN bundle_apps ba ON b.`id` = ba.`bundle`
	// 	INNER JOIN app_tiers at ON at.`id` = ba.`app_tier`
	// 	WHERE at.`app_id` = :app_id
	// 	UNION
	// 	SELECT p.`id`
	// 	FROM product p
	// 	INNER JOIN `app_tiers` at ON (p.`entity` = 'app_tiers' AND p.`entity_id` = at.`id`)
	// 	WHERE at.`app_id` = :app_id
	// )";
 //            $query = $database->prepare($sql);
 //            $query->execute(array(
 //                ':app_id' => $app_id,
 //                ':user_id' => $user_id
 //            ));
	// $results = [];
	// foreach ($query->fetchAll() as $row) {
 //                $model = (new SubscriptionModel($row->$idname))->get_model(false);
 //                // TierLevel will be empty if the subscription instance is not for this app_id
 //                $model["AppTierLevel"] = ProductModel::get_product_tier_level_for_app($model["product_id"], $app_id);
 //                $prod = (new ProductModel($model["product_id"]))->get_model();
 //                $prodName = $prod["product_id"];
 //                unset ($prod);
 //                $prodName = ucwords(str_replace("-", " ", str_replace("ninja", " Ninja", $prodName)));
 //                $model["ProductName"] = $prodName;
 //                $results[] = $model;
	// }

	// public static function get_highest_tier_subscription_reference_for_user_for_app($app_key, $user_id) {
	// 	$database = DatabaseFactory::getFactory()->getConnection();

	// 	// this is just the highest tier, but it might be higher than what we subscribed to, so it's useless in this case
	// 	// $query = $database->prepare("select tier_level from app_tiers where app_id in (select app_id from apps where app_key = :appkey) order by tier_level desc limit 1");
	// 	// $query->execute(array(":appkey" => $app_key));
	// 	// $highest_tier_of_this_app = $query->fetch();

	// 	// this is the products that include this app - this is decent, since it's the products we need to search
	// 	// $query->prepare("
	// 	//     select id from product where
	// 	//     (entity = 'app_tiers' and entity_id in (select id from app_tiers where app_id in (select app_id from apps where app_key = :appkey)))
	// 	//     or (entity = 'bundle' and entity_id in (select bundle from bundle_apps where app_tier in (select id from app_tiers where app_id in (select app_id from apps where app_key = :appkey))))"
	// 	// )
	// 	// $query->execute(array(":appkey" => $app_key));
	// 	// $products_that_include_this_app = $query->fetchColumn();

	// 	// see, the problem with doing it this way is that product is "in" a list of products that include this app_key, so it might be any products tier, or even products we aren't subscribed to
	// 	// $query = $database->prepare("
	// 	//     select subscription_id, referenceId, product_id from subscriptions
	// 	//     where user_id = :userid and status = 'active' and product_id in (
	// 	//         select x.id from (
	// 	//             (
	// 	//                 select p.id, t.tier_level from product p
	// 	//                 inner join app_tiers t on (p.entity_id = t.id and p.entity = 'app_tiers')
	// 	//                 inner join apps a on t.app_id = a.app_id
	// 	//                 where a.app_key = :appkey
	// 	//                 order by t.tier_level desc
	// 	//                 limit 1
	// 	//             )
	// 	//             union
	// 	//             (
	// 	//                 select p.id, t.tier_level from product p
	// 	//                 inner join bundle b on (p.entity_id = b.id and p.entity = 'bundle')
	// 	//                 inner join bundle_apps ba on (b.id = ba.bundle)
	// 	//                 left outer join app_tiers t on (ba.app_tier = t.id)
	// 	//                 inner join apps a on t.app_id = a.app_id
	// 	//                 where a.app_key = :appkey
	// 	//                 order by t.`tier_level` desc
	// 	//                 limit 1
	// 	//             )
	// 	//         ) x ORDER by x.tier_level DESC
	// 	//     )
	// 	// ");
	// 	// $query->execute(array(
	// 	//     ':appkey' => $app_key,
	// 	//     ':userid' => $user_id
	// 	// ));

	// 	// so here are the active subscriptions that are for this user and the products they connect to that include this app
	// 	$query = $database->prepare("
	// 		select referenceId, product_id from subscriptions
	// 		where user_id = :userid
	// 		and status = 'active'
	// 		and product_id in (
	// 			select id from product where
	// 			(entity = 'app_tiers' and entity_id in (select id from app_tiers where app_id in (select app_id from apps where app_key = :appkey)))
	// 			or (entity = 'bundle' and entity_id in (select bundle from bundle_apps where app_tier in (select id from app_tiers where app_id in (select app_id from apps where app_key = :appkey))))
	// 		)
	// 	");
	// 	$query->execute(array(
	// 		":userid" => $user_id,
	// 		":appkey" => $app_key
	// 	));

	// 	$highest_tier = 0;
	// 	$referenceId = null;
	// 	// lets look at the actual tier_level connected to each of these products and index them
	// 	foreach ($query->fetchAll() as $subscription) {
	// 		$query = $database->prepare("
	// 			select tier_level from app_tiers where id in (
	// 				select entity_id from product where entity = 'app_tiers' and id = :pid
	// 				union
	// 				select app_tier from bundle_apps b inner join product p on b.bundle = p.entity_id and p.entity = 'bundle' where p.id = :pid
	// 			)
	// 			and app_id in (
	// 				select app_id from apps where app_key = :appkey
	// 			)
	// 			order by tier_level desc
	// 			limit 1
	// 		");
	// 		$query->execute(array(
	// 			":pid" => $subscription->product_id,
	// 			":appkey" => $app_key
	// 		));
	// 		$tier = intval($query->fetch(PDO::FETCH_COLUMN, 0),10);
	// 		// we could put this into an array then sort the array, but we can use simple compares as well. hopefully there's only one or two loops anyway (if the user has purchased the same thign multiple times that's their problem)
	// 		if ($tier > $highest_tier) {
	// 			$highest_tier = $tier;
	// 			$referenceId = $subscription->referenceId;
	// 		}
	// 	}

	// 	// well, if it's null then somehow we got to this codepath unexpectedly, otherwise it's the subscription reference for the highest tier of app we own
	// 	return $referenceId;
	// }

	// probably not needed, since you generate the hash using the api then ask to launch the app using that hash
	// so there's no need to find the record for that hash, since launch already tests to see if the hash is part of a sub that contains the app
	// public static function get_highest_tier_subscription_for_hash_for_app($app_key, $hash) {
	//     $database = DatabaseFactory::getFactory()->getConnection();
	//     $query = $database->prepare("
	//         select subscription_id, referenceId, product_id from subscriptions
	//         where md5(referenceId) = :hash and status = 'active' and product_id in (
	//             select x.id from (
	//                 (
	//                     select p.id, t.tier_level from product p
	//                     inner join app_tiers t on (p.entity_id = t.id and p.entity = 'app_tiers')
	//                     inner join apps a on t.app_id = a.app_id
	//                     where a.app_key = :appkey
	//                     order by t.tier_level desc
	//                     limit 1
	//                 )
	//                 union
	//                 (
	//                     select p.id, t.tier_level from product p
	//                     inner join bundle b on (p.entity_id = b.id and p.entity = 'bundle')
	//                     inner join bundle_apps ba on (b.id = ba.bundle)
	//                     left outer join app_tiers t on (ba.app_tier = t.id)
	//                     inner join apps a on t.app_id = a.app_id
	//                     where a.app_key = :appkey
	//                     order by t.`tier_level` desc
	//                     limit 1
	//                 )
	//             ) x ORDER by x.tier_level DESC
	//         )
	//     ");
	//     $query->execute(array(
	//         ':appkey' => $app_key,
	//         ':hash' => $hash
	//     ));
	//     return $query->fetch();
	// }

	// a routine used by the store model to precache the app ids that the user is subscribed to for comparison in the tile renderer
	// returns a raw array of the ids
	public static function get_subscribed_app_ids_for_user($user_id, $status = "active") {
		$database = DatabaseFactory::getFactory()->getConnection();
		$query = $database->prepare("
			SELECT group_concat(app_ids) FROM product_bundle
			WHERE id IN (
				SELECT product_id FROM subscriptions WHERE user_id = :user_id AND status = :status
			)
		");
		$query->execute(array(":user_id"=>$user_id, ":status"=>$status));
		return array_unique(explode(',', $query->fetchColumn(0)));
	}
	// $sql = "SELECT at.`app_id`
	// 	FROM product p
	// 	INNER JOIN bundle b ON (p.`entity` = 'bundle' AND p.`entity_id` = b.`id`)
	// 	INNER JOIN bundle_apps ba ON b.`id` = ba.`bundle`
	// 	INNER JOIN app_tiers at ON at.`id` = ba.`app_tier`
	// 	WHERE p.`id` IN (SELECT product_id FROM subscriptions WHERE user_id = :user_id AND status='$status')
	// 	UNION
	// 	SELECT at.`app_id` appId
	// 	FROM product p
	// 	INNER JOIN `app_tiers` at ON (p.`entity` = 'app_tiers' AND p.`entity_id` = at.`id`)
	// 	WHERE p.`id` IN (SELECT product_id FROM subscriptions WHERE user_id = :user_id AND status='$status')";
	// 		$query = $database->prepare($sql);
	// 		$query->execute(array(
	// 			':user_id' => $user_id
	// 		));
	// 		return $query->fetchAll(PDO::FETCH_COLUMN, 0); // FETCH_ASSOC);
	// }

	public static function get_current_subscribed_apps_model($user_id) {
		$app_ids = self::get_subscribed_app_ids_for_user($user_id);
		if (empty($app_ids)) return false;
		$database =  DatabaseFactory::getFactory()->getConnection();
		$ids = implode(",", $app_ids); // safe since the source is an internal function output,
		$baseUrl = Config::get("URL");
		// tim: removed active check until we can work out api
		// active=1 and
		$query = $database->prepare("
			SELECT app_id, app_key, name, icon
			FROM apps
			WHERE app_id IN ($ids)
			ORDER BY popular
		");
		$query->execute();
		$results = [];
		while (list($app_id, $app_key, $name, $icon) = $query->fetch(PDO::FETCH_NUM)) {  //, PDO::FETCH_ORI_NEXT)) {
			$results[] =array(
				"app_key" => $app_key,
				"name" => $name,
				"icon" => $icon,
				"launch" => $baseUrl . "launch/" . $app_key,
				"subs" => self::get_subscriptions_for_user_for_app($user_id, $app_id)
			);
		}
		return $results;
	}

	// check if a user has an active subscription to the specified app_key
	// optionally check other statuses (e.g. api demo request to check for previous now-inactive subscription to app)
	// returns boolean
	public static function user_has_active_subscription_to_app($user_id, $app_key, $status = "active")
	{
		$ids = self::get_subscribed_app_ids_for_user($user_id, $status); // an array
		$in = join(',', array_fill(0, count($ids), '?'));
		$sql = "SELECT count(1)
			FROM apps WHERE app_key = ?
			AND app_id IN ($in)
		";
		array_unshift($ids, $app_key);
		$database = DatabaseFactory::getFactory()->getConnection();
		$query = $database->prepare($sql);
		$query->execute($ids);
		return (intval($query->fetch(PDO::FETCH_COLUMN, 0),10) > 0);
	}

	public static function get_user_subscription_history($user_id)
	{
		$idname = self::ID_ROW_NAME;
		$sql = "SELECT $idname FROM " . self::TABLE_NAME . " WHERE user_id = :user_id ORDER BY active DESC, added DESC";
		$database = DatabaseFactory::getFactory()->getConnection();
		$query = $database->prepare($sql);
		$query->execute(array(
			':user_id' => $user_id
		));
		$results = [];
		foreach ($query->fetchAll() as $row) {
			$result = (new SubscriptionModel($row->$idname))->get_model(true,false, true);
			$rowClass = "cs-active";
			$orderNumber = $result["referenceId"]; // TODO revise security of exposing this
//			$result["order_history"] = "/me/orders/history/{$orderNumber}";
			$result["order_number"] = $orderNumber;
			$result["support_url"] = "mailto:accounts@coursesuite.com.au?subject=Order Support:- " . $orderNumber;
			if (!empty($result["endDate"])) {
				$result["ended"] = (strtotime($result["endDate"])<time());
				$rowClass = "cs-ended";
			}
			if (!empty($result["statusReason"])) {
				$rowClass = "cs-" . $result["statusReason"];
			}
			$result["rowClass"] = $rowClass;
			$results[] = $result;
		}
		return $results;
	}

	public static function get_subscription_id_for_hash($hash) {
		$idname = self::ID_ROW_NAME;
		$tablename = self::TABLE_NAME;
		$sql = "SELECT $idname FROM $tablename WHERE md5(referenceId) = :hash LIMIT 1";
		$database = DatabaseFactory::getFactory()->getConnection();
		$query = $database->prepare($sql);
		$query->execute(array(
			':hash' => $hash
		));
		return intval($query->fetchColumn(),10);
	}

	public static function get_user_api_subscription_records($user_id) {
		$idname = self::ID_ROW_NAME;
		$tablename = self::TABLE_NAME;
		$database = DatabaseFactory::getFactory()->getConnection();
		$query = $database->prepare("
			SELECT $idname FROM $tablename
			WHERE product_id IN (
				SELECT id FROM product_bundle
				WHERE product_key LIKE 'api-%'
			)
			AND user_id = :user_id
			ORDER BY added
		");
		$query->execute(array(
			':user_id' => $user_id
		));
		$results = [];
		foreach ($query->fetchAll() as $row) {
			$results[] = (new SubscriptionModel($row->$idname))->get_model(true);
		}
		return $results;
	}

	public static function user_has_active_subscription_to_launchable_app($user_id) {
		return Model::Exists("subscriptions", "user_id=:uid and product_id in (select id from product_bundle where active=1 and product_key not like 'api-%') and status='active'", [":uid"=>$user_id]);
	}

	// user has a record for a subscription that links to a particular product key (past or present)
	public static function user_has_subscription($user_id, $product_key = "api-trial") {
		return Model::Exists("subscriptions", "user_id=:uid and product_id in (select id from product_bundle where product_key=:pkey)", [":uid"=>$user_id,":pkey"=>$product_key]);
	}

	public static function create_trial_subscription($user_id) {

		// does this user already have a subscription for an API product?
		if (self::user_has_active_subscription_to_app($user_id, "api")) {
			return -1;
		}

		// TODO: does the user have more than some threshold of previous demo subscription products in recent weeks?
		//$subs = self::get_user_api_subscription_records($account->user_id);

		$am = new AccountModel("id",$user_id);
		if (is_null($am->get_property("secret_key"))) {
			$am->add_secret_key();
		}

		// fairly unique and presentable id
		$id = UUID::uniqid_base36(true);
		$pos = strlen($id) / 2;
		list($beg, $end) = preg_split('/(?<=.{'.$pos.'})/', $id, 2);
		$referenceId = "TRIAL-$beg-$end";

		// Create the subscription record
		$model = (object) Model::Create("subscriptions");
		$model->user_id = $user_id;
		unset($model->added); // allow database default to apply
		$model->endDate = date('Y-m-d', strtotime("+7 day"));
		$model->referenceId = $referenceId;
		$model->subscriptionUrl = null;
		$model->status = "active";
		$model->statusReason = null;
		$model->testMode = 0;
		$model->active = 1;
		$model->info = "User-generated trial";
		$model->product_id = Model::ReadColumn("product_bundle", "id", "product_key=:key", array(":key" => Config::get("API_TRIAL_PRODUCT_ID")));

		$record_id = Model::Update("subscriptions", "subscriptions_id", $model);

		return intval($record_id,10);

	}



}