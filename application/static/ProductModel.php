<?php

// a PRODUCT represents ONE or MORE apps at a particular TIER LEVEL
class ProductModel extends Model
{
    CONST TABLE_NAME = "product";
    CONST ID_ROW_NAME = "id";

    protected $data_model;

    public function get_model($include_app_model = false, $active_only = true)
    {
        $data = $this->data_model;
        if ($include_app_model) {
            $data->Apps = self::get_apps($active_only);
        }
        return (array) $data;
    }

    public function set_model($data)
    {
        $this->data_model = $data;
    }

    public function __construct(...$params)
    {
        parent::__construct();
        if (count($params) == 1 && (int) $params[0] > 0) { // new ProductModel(1)
            self::load($params[0]);
        } else if (count($params) == 2) { // new ProductModel("bundle", 3)
            $read = parent::Read(self::TABLE_NAME, "entity=:entity AND entity_id=:id", array(":entity" => $params[0], ":id" => $params[1]));
            if (!empty($read)) {
                $this->data_model = $read[0]; // 0th of a fetchall
            } else {
                return null;
            }
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

    public function load_by_productId($productId)
    {
        $this->data_model = parent::Read(self::TABLE_NAME, "product_id=:id", array(":id" => $productId))[0]; // 0th of a fetchall
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

    public function get_id() {
        if (isset($this->data_model)) {
            $idrowname = self::ID_ROW_NAME;
            return $this->data_model->$idrowname;
        }
    }

    public function get_description() {
        $result = "";
        if (!isset($this->data_model)) return "";
        if ($this->data_model->entity == "bundle") {
            $bm = (new BundleModel($this->data_model->entity_id))->get_model();
            $result = $bm["label"] . " (" . $bm["description"] . ")";
        } else if ($this->data_model->entity = "app_tiers") {
            $apt = (new AppTierModel($this->data_model->entity_id))->get_model();
            $app = AppModel::getAppById($apt["app_id"]);
            $result = $app->name;
            $result .= " (" . $apt["name"] . ")";
        }
        return $result;
    }

    // quick check to see if this product relates to an api
    public function is_api() {
        if (!isset($this->data_model)) return null; // not sure
        return (substr($this->data_model->product_id, 0, 4) === "api-");
    }

    // get the apps that are connected to this product
    public function get_apps($active_only = true) {
        $results = [];
        $vis = "";
        if (isset($this->data_model)) {
            $database = DatabaseFactory::getFactory()->getConnection();
            if ($active_only) $vis = "and at.visible = 1";
            if ($this->data_model->entity === "bundle") {
                $sql = "
                    SELECT at.app_id
                       FROM bundle_apps ba
                       INNER JOIN bundle b ON ba.bundle = b.id
                       INNER JOIN app_tiers at ON at.`id` = ba.`app_tier`
                       WHERE b.id = :entity_id $vis and b.active = 1
                ";
            } else if ($this->data_model->entity === "app_tiers") {
                $sql = "
                       SELECT at.app_id
                       FROM app_tiers at
                       WHERE at.id = :entity_id $vis
                ";
            }
            $query = $database->prepare($sql);
            $query->execute(array(
                ':entity_id' => $this->data_model->entity_id
            ));
            $results = [];
            foreach ($query->fetchAll() as $row) {
                $results[] = (new AppModel($row->app_id))->get_model();
            }
            return $results;

        };
        return $results;
    }

    // given a product and an app, find out the tier level (e.g. token logon, working out overlapping subscriptions, etc)
    public static function get_product_tier_level_for_app($product_id, $app_id)
    {
            $sql = "SELECT at.tier_level
                    FROM bundle_apps ba
                    INNER JOIN app_tiers at ON at.`id` = ba.`app_tier` AND at.`app_id` = :app_id
                    WHERE bundle IN (
                        SELECT entity_id FROM product WHERE entity = 'bundle' AND id = :product_id
                    )
                    UNION
                    SELECT at.tier_level FROM app_tiers at WHERE id IN (
                        SELECT entity_id FROM product WHERE entity = 'app_tiers' AND id = :product_id
                    ) AND at.`app_id` = :app_id
            ";
            $database = DatabaseFactory::getFactory()->getConnection();
            $query = $database->prepare($sql);
            $query->execute(array(
                ':app_id' => $app_id,
                ':product_id' => $product_id
            ));
            return $query->fetch(PDO::FETCH_COLUMN, 0);
    }

     // when validating a token, we know the APP and the USER, but not the product or products they are subscribed to;
    // return (int) highest Tier Level for this app for all active subscriptions
    public static function get_highest_subscribed_tier_for_app($app_id, $user_id)
    {
        $sql = "SELECT apt.tier_level
                    FROM app_tiers apt
                    INNER JOIN product p ON p.entity_id = apt.id AND p.entity = 'app_tiers'
                    INNER JOIN subscriptions s ON p.id = s.`product_id`
                    WHERE s.user_id = :user_id
                        AND apt.`app_id` = :app_id
                        AND s.status = 'active'
                    UNION
                    SELECT app_tiers.`tier_level`
                    FROM bundle_apps
                    INNER JOIN app_tiers ON bundle_apps.`app_tier` = app_tiers.`id` AND app_tiers.`app_id` = :app_id
                    WHERE bundle_apps.`bundle` IN (
                            SELECT product.`entity_id`
                            FROM product WHERE product.`id` IN (
                                SELECT `product_id` FROM subscriptions
                                    WHERE `user_id` = :user_id
                                    AND `status` = 'active'
                                )
                    )
                    ORDER BY tier_level DESC
                    LIMIT 1";
            $database = DatabaseFactory::getFactory()->getConnection();
            $query = $database->prepare($sql);
            $query->execute(array(
                ':app_id' => $app_id,
                ':user_id' => $user_id
            ));
            return $query->fetch(PDO::FETCH_COLUMN, 0);
    }

    public static function get_product_store_model($url_append = '') {
        $sql = "
            SELECT concat(at.`name`, ' (', at.`concurrency`, ' concurrent users) $', p.`price`, ' /quarter') label, concat(p.`store_url`, :append) url
            FROM product p INNER JOIN app_tiers at ON (p.`entity` = 'app_tiers' AND p.`entity_id` = at.`id`)
            WHERE p.`product_id` LIKE 'api-%' AND p.`active` = 1 AND p.`product_id` <> 'api-trial'
            ORDER BY at.`concurrency`
        ";
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare($sql);
        $query->execute([":append" => $url_append]);
        return $query->fetchAll();
    }

}
