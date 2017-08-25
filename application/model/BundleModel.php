<?php

class BundleModel extends Model
{
    CONST TABLE_NAME = "bundle";
    CONST ID_ROW_NAME = "id";

    protected $data_model;
    protected $product_model;
    protected $bundledapps_model;

    public function get_model()
    {
        $data = $this->data_model;
        $data->Product = self::get_product();
        $data->BundleApps = self::get_apps();
        return (array) $data;
    }

    protected function get_product()
    {
        if (!isset($this->product_model)) {
            $idname = self::ID_ROW_NAME;
            $this->product_model = (new ProductModel(self::TABLE_NAME, $this->data_model->$idname))->get_model();
        }
        return $this->product_model;
    }

    protected function get_apps()
    {
        if (!isset($this->bundledapps_model)) {
            $idname = self::ID_ROW_NAME;
            $database = DatabaseFactory::getFactory()->getConnection();
            $sql = "SELECT at.name tier_name, a.app_key app_key, a.name app_name
                FROM bundle b
                INNER JOIN bundle_apps ba ON b.id = ba.bundle
                INNER JOIN app_tiers at ON at.id = ba.app_tier
                INNER JOIN apps a ON at.app_id = a.app_id
                WHERE b.id = :id
                AND b.active = 1
                ORDER BY at.tier_level, a.name
            ";
            $query = $database->prepare($sql);
            $query->execute(array(
                ':id' => $this->data_model->$idname
            ));
            $this->bundledapps_model = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return $this->bundledapps_model;
    }

    public function set_model($data)
    {

        if (isset($data->Product)) unset($data->Product);
        if (isset($data->BundleApps)) unset($data->BundleApps);
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

    public function make()
    {
        $this->data_model = parent::Create(self::TABLE_NAME);
        return $this;
    }

    public function save()
    {
        return parent::Update(self::TABLE_NAME, self::ID_ROW_NAME, $this->data_model);
    }

    public static function get_bundles($app_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT id
            FROM bundle
            WHERE id IN (
                SELECT bundle FROM bundle_apps WHERE app_tier IN (
                    SELECT id FROM app_tiers WHERE app_id = :id
                )
            )
            AND active = 1
            ORDER BY sequence
        ";
        $query = $database->prepare($sql);
        $query->execute(array(':id' => $app_id));
        $results = [];
        foreach ($query->fetchAll() as $row) {
            $results[] = (new BundleModel($row->id))->get_model();
        }
        return $results;
    }

}