<?php

// a PRODUCT represents ONE or MORE apps at a particular TIER LEVEL
class ProductBundleModel extends Model
{
    CONST TABLE_NAME = "product_bundle";
    CONST ID_ROW_NAME = "id";

    protected $data_model;

    // in this instance, get_apps are the apps that this bundle references
    public function get_model($include_app_model = false, $active_only = true)
    {
        $data = $this->data_model;
        if (!empty($data) && $include_app_model) {
            if (is_array($data)) {
                foreach ($data as &$record) {
                    $record->Apps = self::get_connected_apps($record->app_ids, $active_only);
                }
            } else {
                $data->Apps = self::get_connected_apps($data->app_ids, $active_only);
            }
        }
        return $data;
    }

    public function set_model($data)
    {
        if (is_array($data)) {
            throw new Exception("can't persist model array");
            return null;
        } else if (isset($data->Apps)) {
            unset($data->Apps);
        }
        $this->data_model = $data;
    }

    public function __construct($column = "id", $match = 0)
    {
        parent::__construct();
        $params = [":key"=>$match];
        $where = "";
        $sort = "";
        switch ($column) {
            case "id":
                if ($match === 0) { // create
                    $this->data_model = parent::Create(self::TABLE_NAME, false);
                } else {
                    $where = self::ID_ROW_NAME . "=:key";
                }
                $fetchOne = true;
                break;
            case "product_key":
            case "key":
                $where = "product_key=:key";
                $fetchOne = true;
                break;
            case "app_id":
                $where = "find_in_set(cast(:key as nchar), app_ids) > 0 and product_key not like 'api-%'";
                $sort = "sort";
                $fetchOne = false;
                break;
        }
        if (!empty($where)) {
            $data = self::Read(self::TABLE_NAME, $where, $params, '*', $fetchOne, $sort);
            if (!empty($data)) {
                $this->data_model = $data;
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
            parent::Destroy(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $this->data_model->$idname));
        }
    }

    public function make()
    {
        $this->data_model = parent::Create(self::TABLE_NAME, false);
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

    // quick check to see if this product relates to an api
    public function is_api() {
        if (!isset($this->data_model)) return null; // not sure
        return (substr($this->data_model->product_key, 0, 4) === "api-");
    }

    // get the apps that are connected to this product
    public static function get_connected_apps($app_ids, $active_only = true) {
        $where = ($active_only === true) ? " and active=1" : "";
        $data = self::Read("apps", "find_in_set(cast(app_id as nchar),:set)>0 $where", array(":set" => $app_ids), "app_id");
        $results = [];
        foreach ($data as $record) {
            $results[] = (new AppModel("app_id", $record->app_id))->get_model();
        }
        return $results;

    }
    public function get_apps($active_only = true) {
        if (!is_array($this->data_model)) {
            return self::get_connected_apps($this->data_model->app_ids, $active_only);
        }
        return null;
    }

    public static function get_all_models($include_apps = false) {
        $idname = self::ID_ROW_NAME;
        $records = Model::Read(self::TABLE_NAME, "", [], $idname, false, "sort");
        $results = [];
        foreach ($records as $row) {
            $results[] = (new ProductBundleModel("id", $row->$idname))->get_model($include_apps,false);
        }
        return $results;
    }

    public static function get_store_dropdown_model() {
        $sql = "
            SELECT concat(label, ' (', concurrency, ' concurrent users) $', price, ' / quarter') label, store_url value
            FROM product_bundle
            WHERE product_key LIKE 'api-%'
            AND active = 1
            AND product_key <> 'api-trial'
            ORDER BY concurrency
        ";
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

}
