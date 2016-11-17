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

    public static function createStoreProduct($name, $active, $type, $purchase_url=null) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "INSERT INTO store_product (purchase_url, active, name, type) VALUES(:purchase_url, :active, :name, :type)";
        $query = $database->prepare($sql);
        $params = array(
            ':purchase_url' => $purchase_url,
            ':active' => $active,
            ':name' => $name,
            ':type' => $type
        );
        $query->execute($params);
    }

    public static function getStoreProductById($id) {
    	$database = DatabaseFactory::getFactory()->getConnection();
    	$sql = "SELECT product_id, purchase_url, active, name, type, price, tier FROM store_product WHERE product_id = :id LIMIT 1";
    	$query = $database->prepare($sql);
    	$query->execute(array(':id'=>$id));
    	return $query->fetch();
    }

    public static function getStoreProductByName($name) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT product_id, purchase_url, active, name, type FROM store_product WHERE name = :name LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':name'=>$name));
        return $query->fetch();
    }

    public static function getProductsByAppId($id) {
    	$database = DatabaseFactory::getFactory()->getConnection();
    	$sql = "SELECT product_id FROM store_product_apps WHERE app_id = :id";
    	$query = $database->prepare($sql);
    	$query->execute(array(':id'=>$id));
    	return $query->fetchAll();
    }

    public static function createProductAppLink($app_id, $product_id) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "INSERT INTO store_product_apps (app_id, product_id) VALUES(:app_id, :product_id)";
        $query = $database->prepare($sql);
        $params = array(
            ':app_id' => $app_id,
            ':product_id' => $product_id
        );
        $query->execute($params);
    }

    public static function getPrice($product_id) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT price FROM store_product WHERE product_id = :product_id";
        $query = $database->prepare($sql);
        $query->execute(array(':product_id' => $product_id));
        return $query->fetch();
    }
}