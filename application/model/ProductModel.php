<?php

// a PRODUCT represents ONE or MORE apps at a particular TIER LEVEL
class ProductModel extends Model
{
    CONST TABLE_NAME = "product";
    CONST ID_ROW_NAME = "id";

    protected $data_model;

    public function get_model()
    {
        return (array) $this->data_model;
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
            $this->data_model = parent::Read(self::TABLE_NAME, "entity=:entity AND entity_id=:id", array(":entity" => $params[0], ":id" => $params[1]))[0]; // 0th of a fetchall
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


}
