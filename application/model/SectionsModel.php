<?php

class SectionsModel extends Model
{

    CONST TABLE_NAME = "store_sections";
    CONST ID_ROW_NAME = "id";
    protected $data_model;

    public function get_model($include_apps = false)
    {
        $data = $this->data_model;
        if ($include_apps === true) {
            // to find out if an app is in a sectionsmodel, find the app_id in the app_ids column
            $data->Apps = AppModel::getAllApps(true);
        }
        return $data;
    }

    public function set_model($data)
    {
        if (is_array($data)) {
            throw new Exception("can't persist model array yet");
            return null;
        }
        $this->data_model = $data;
    }

    public function __construct($column = "id", $match = 0)
    {
        parent::__construct();
        $params = [":key"=>$match];
        $sort = "";
        $where = "";
        switch ($column) {
            case "id": // find by id
                if ($match === 0) { // create
                    $this->data_model = parent::Create(self::TABLE_NAME);
                } else {
                    $where = self::ID_ROW_NAME . "=:key";
                }
                $fetchOne = true;
                break;

            case "route": // find by route
                if ($match === "") { // create
                    $this->data_model = parent::Create(self::TABLE_NAME);
                } else {
                    $where = "route=:key";
                }
                $fetchOne = true;
                break;

            case "app_id": // find section that an app belongs to
                $where = "find_in_set(cast(:key as char), app_ids) > 0";
                $fetchOne = true;
                break;

        }
        if (!empty($where)) {
            $data = parent::Read(self::TABLE_NAME, $where, $params, '*', $fetchOne, $sort);
            if (!empty($data)) {
                $this->data_model = $data;
            }
        }
        return $this;
    }

    public static function Make()
    {
        return parent::Create(self::TABLE_NAME);
    }

    public static function Load($table, $where_clause, $fields)
    {
        return parent::Read($table, $where_clause, $fields);
    }

    public static function Save($table, $idrow_name, $data_model)
    {
        return parent::Update($table, $idrow_name, $data_model);
    }

    public static function getAllStoreSections($basic = false, $visible = false)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $where = ($visible === true) ? " WHERE visible=1 " : "";
        if ($basic) {
            $sql = "SELECT id, label, epiphet, route, routeLabel FROM store_sections $where ORDER BY sort";
        } else {
            $sql = "SELECT id, label, epiphet, cssclass, html_pre, html_post, visible, sort, route, routeLabel
                FROM store_sections
                $where
                ORDER BY sort";
        }
        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    public static function get_store_section_apps($section_id, $active = false) {
        $results = [];
        $app_ids = Model::ReadColumn(self::TABLE_NAME, 'app_ids', self::ID_ROW_NAME . "=:id", array(":id" => $section_id), true);
        foreach (explode(',',$app_ids) as $app_id) {
            $app = (new AppModel("app_id", $app_id))->get_model(false,false);
            if ($active === true && (int) $app->active > 1) {
                $results[] = $app;
            } else if ($active === false) {
                $results[] = $app;
            }
        }
        return $results;
    }

    public static function getStoreSection($id)
    {
        $id = intval($id, 10);
        if ($id < 1) {
            return null;
        }

        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT id, label, epiphet, cssclass, html_pre, html_post, visible, sort
            FROM store_sections
            WHERE id = :id";
        $query = $database->prepare($sql);
        $query->execute(array(":id" => $id));
        return $query->fetch();
    }

    public static function getStoreSectionByRoute($route, $include_apps = false)
    {
        if (trim($route) === "") {
            return null;
        }

        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT id, label, epiphet, cssclass, html_pre, html_post, visible, sort, route
            FROM store_sections
            WHERE route = :route";
        $query = $database->prepare($sql);
        $query->execute(array(":route" => $route));
        if ($include_apps === true) {
            $results = [];
            while ($record = $query->fetchObject()) {
                $record->Apps = self::get_store_section_apps($record->id, true);
                $results[] = $record;
            }
            return $results;
        } else {
            return $query->fetch();
        }
    }

    public static function setOrder($array)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $database->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1);
        $sql = array();
        foreach ($array as $id => $order) {
            $sql[] = "UPDATE store_sections SET sort = $order WHERE id = $id";
        }
        $query = $database->prepare(implode(";", $sql));
        $query->execute();
    }

    public static function label_for_route($route) {
        return Model::Read("store_sections", "route=:route", array(":route"=>$route),"label",true)->label;
    }
}
