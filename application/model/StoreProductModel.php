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
    	$sql = "SELECT product_id, purchase_url, active, name, bundle_description FROM store_product WHERE product_id = :id LIMIT 1";
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

    public static function getBundles() {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT product_id, purchase_url, active, name, bundle_description
                FROM store_product
                WHERE product_id IN
                (SELECT product_id from store_product_apps GROUP BY product_id HAVING COUNT(*) > 1)";
        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }
}