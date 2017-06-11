<?php

class SubscriptionModel extends Model
{
    CONST TABLE_NAME = "subscriptions";
    CONST ID_ROW_NAME = "subscription_id";

    protected $data_model;
    protected $product_model;

    public function get_model($include_product_model = false)
    {
        $data = $this->data_model;
        if ($include_product_model) {
            $data->Product = self::get_product();
        }
        return (array) $data;
    }

    protected function get_product()
    {
        if (!isset($this->product_model)) {
            $this->product_model = (new ProductModel($this->data_model->product_id))->get_model();
        }
        return $this->product_model;
    }

    public function set_model($data)
    {
    	if (isset($data->Product)) unset($data->Product);
	$this->data_model = $data;
    }

    public function __construct($row_id = 0)
    {
        parent::__construct();
        if ($row_id > 0) {
            self::load($row_id);
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
        $this->data_model = parent::Read(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $id))[0]; // 0th of a fetchall
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
        $query = $database->query("select count(1) from systasks where running=0 and task='validateSubscriptions' and lastrun < (CURRENT_TIMESTAMP - INTERVAL 1 DAY)");
        if (1 == intval($query->fetchColumn())) {
            $database->query("update systasks set running=1 where task='validateSubscriptions'");
            $database->query("update subscriptions set active = 0, status='expired' where datediff(endDate, now()) < 0");
            $database->query("update subscriptions set active = 1, status='active' where endDate is null");
        }
        $database->query("update systasks set running=0, lastrun=CURRENT_TIMESTAMP where task='validateSubscriptions'");
    }

    // a routine used by the store info page to know the models of each of a users' subscriptions, if any, for the app being rendered
    // returns array of models
    public static function get_subscriptions_for_user_for_app($user_id, $app_id) {
	$database = DatabaseFactory::getFactory()->getConnection();
	$idname = self::ID_ROW_NAME;
	$sql = "select $idname FROM subscriptions WHERE user_id = :user_id AND status = 'active' AND product_id IN (
		SELECT p.`id`
		FROM product p
		INNER JOIN bundle b ON (p.`entity` = 'bundle' AND p.`entity_id` = b.`id`)
		INNER JOIN bundle_apps ba ON b.`id` = ba.`bundle`
		INNER JOIN app_tiers at ON at.`id` = ba.`app_tier`
		WHERE at.`app_id` = :app_id
		UNION
		SELECT p.`id`
		FROM product p
		INNER JOIN `app_tiers` at ON (p.`entity` = 'app_tiers' AND p.`entity_id` = at.`id`)
		WHERE at.`app_id` = :app_id
	)";
            $query = $database->prepare($sql);
            $query->execute(array(
                ':app_id' => $app_id,
                ':user_id' => $user_id
            ));
	$results = [];
	foreach ($query->fetchAll() as $row) {
                $model = (new SubscriptionModel($row->$idname))->get_model(false);
                // TierLevel will be empty if the subscription instance is not for this app_id
                $model["AppTierLevel"] = ProductModel::get_product_tier_level_for_app($model["product_id"], $app_id);
                $results[] = $model;
	}
	return $results;
    }

    // a routine used by the store model to precache the app ids that the user is subscribed to for comparison in the tile renderer
    // returns a raw array of the ids
    public static function get_subscribed_app_ids_for_user($user_id)
    {
	$database = DatabaseFactory::getFactory()->getConnection();
	$sql = "SELECT at.`app_id`
		FROM product p
		INNER JOIN bundle b ON (p.`entity` = 'bundle' AND p.`entity_id` = b.`id`)
		INNER JOIN bundle_apps ba ON b.`id` = ba.`bundle`
		INNER JOIN app_tiers at ON at.`id` = ba.`app_tier`
		WHERE p.`id` IN (SELECT product_id FROM subscriptions WHERE user_id = :user_id AND status='active')
		UNION
		SELECT at.`app_id` appId
		FROM product p
		INNER JOIN `app_tiers` at ON (p.`entity` = 'app_tiers' AND p.`entity_id` = at.`id`)
		WHERE p.`id` IN (SELECT product_id FROM subscriptions WHERE user_id = :user_id AND status='active')";
            $query = $database->prepare($sql);
            $query->execute(array(
                ':user_id' => $user_id
            ));
            return $query->fetchAll(PDO::FETCH_COLUMN, 0); // FETCH_ASSOC);
    }

    // check if a user has an active subscription to the specified app_key
    // returns boolean
    public static function user_has_active_subscription_to_app($user_id, $app_key)
    {
        $ids = self::get_subscribed_app_ids_for_user($user_id); // an array
        $in = join(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT count(1)
            FROM apps WHERE app_key = ?
            AND app_id IN ($in)
        ";
        array_unshift($ids, $app_key);
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare($sql);
        $query->execute($ids);
        return ($query->fetchAll(PDO::FETCH_COLUMN, 0) > 0);
    }

    public static function get_user_subscription_history($user_id)
    {
        $idname = self::ID_ROW_NAME;
        $sql = "SELECT $idname FROM " . self::TABLE_NAME . " WHERE user_id = :user_id ORDER BY added DESC";
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare($sql);
        $query->execute(array(
            ':user_id' => $user_id
        ));
        $results = [];
        foreach ($query->fetchAll() as $row) {
            $results[] = (new SubscriptionModel($row->$idname))->get_model(true);
        }
        return $results;
    }

}