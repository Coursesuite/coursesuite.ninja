<?php

class ProductModel extends Model
{
	public static function save($table, $idrow_name, $data_model){
		return parent::update($table, $idrow_name, $data_model);
	}
    public static function make($table){
    	return parent::create($table);
	}

	public static function getAllProducts(){
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT product_id, display_name, description, link_id, type FROM products";
		$query = $database->prepare($sql);
		$query->execute();
		return $query->fetchAll();
	}

	public static function getProductById($product_id){
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT product_id, display_name, description, link_id, type, category FROM products WHERE product_id = :product_id LIMIT 1";
		$query = $database->prepare($sql);
		$query->execute(array(":product_id" => $product_id));
		return $query->fetch();
	}
}