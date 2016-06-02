<?php

class SectionsModel extends Model {

    function __construct() {
        parent::__construct();
    }

    public static function Make() {
        return parent::Create("store_sections");
    }

     public static function Load($table, $where_clause, $fields) {
        return parent::Read($table, $where_clause, $fields);
     }

     public static function Save($table, $idrow_name, $data_model) {
        return parent::Update($table, $idrow_name, $data_model);
     }

    public static function getAllStoreSections($basic = false) {
        $database = DatabaseFactory::getFactory()->getConnection();
        if ($basic) {
	        $sql = "SELECT id, label, epiphet FROM store_sections ORDER BY sort";
        } else {
	        $sql = "SELECT id, label, epiphet, cssclass, html_pre, html_post, visible, sort
                FROM store_sections
                ORDER BY sort";
        }
        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    public static function getStoreSection($id) {
	    $id = intval($id,10);
	    if ($id<1) return null;
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT id, label, epiphet, cssclass, html_pre, html_post, visible, sort
            FROM store_sections
            WHERE id = :id";
        $query = $database->prepare($sql);
        $query->execute(array(":id" => $id));
        return $query->fetch();
    }
/*
    public static function setStoreSection($model) {

        $database = DatabaseFactory::getFactory()->getConnection();

        $params["method_name"] = $methodName;
        $params["digest_user"] = $digestUser;
        for ($i = 0 ; $i < count($args) ; $i += 1) {
            $params["param" . $i] = $args[$i];
        }
        $sql = "INSERT INTO applog(" . implode(", ", array_keys($params)) . ") VALUES (";
        $modded = array();
        foreach ($params as $param => $value) {
            $modded[":$param"] = is_array($value) ? serialize($value) : $value;
        }
        unset($params);
        $sql .= implode(", ", array_keys($modded)) . ")";
        $query = $database->prepare($sql);
        $query->execute($modded);
        if ($query->rowCount() == 1) {
            return true;
        }


    }
  */
}