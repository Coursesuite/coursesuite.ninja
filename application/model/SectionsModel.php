<?php

class SectionsModel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public static function Make()
    {
        return parent::Create("store_sections");
    }

    public static function Load($table, $where_clause, $fields)
    {
        return parent::Read($table, $where_clause, $fields);
    }

    public static function Save($table, $idrow_name, $data_model)
    {
        return parent::Update($table, $idrow_name, $data_model);
    }

    public static function getAllStoreSections($basic = false)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        if ($basic) {
            $sql = "SELECT id, label, epiphet, route, routeLabel FROM store_sections ORDER BY sort";
        } else {
            $sql = "SELECT id, label, epiphet, cssclass, html_pre, html_post, visible, sort, route, routeLabel
                FROM store_sections
                ORDER BY sort";
        }
        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
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

}
