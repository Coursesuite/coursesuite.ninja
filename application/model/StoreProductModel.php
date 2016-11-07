<?php

class StoreProductModel extends Model
{
	public function __construct() {
		parent::__construct();
	}
	public static function save($table, $idrow_name, $data_model) {
        return parent::update($table, $idrow_name, $data_model);
    }
    public static function make($table) {
        return parent::create($table);
    }

    public static function getStoreProductById($id) {
    	$database = DatabaseFactory::getFactory()->getConnection();
    	$sql = "SELECT purchase_url, active, name FROM store_product WHERE product_id = :id LIMIT 1";
    	$query = $database->prepare($sql);
    	$query->execute(array(':id'=>$id));
    	return $query->fetch();
    }



    public static function getProductsByAppId($id) {
    	$database = DatabaseFactory::getFactory()->getConnection();
    	$sql = "SELECT product_id FROM store_product_apps WHERE app_id = :id";
    	$query = $database->prepare($sql);
    	$query->execute(array(':id'=>$id));
    	return $query->fetchAll();
    }
}