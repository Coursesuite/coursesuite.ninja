<?php

class BundleModel extends Model 
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

    public static function createBundle($product_id, $display_name, $description, $media) {
		$database = DatabaseFactory::getFactory()->getConnection();  
		$sql = "INSERT INTO app_bundles (product_id, display_name, description, media) VALUES(:product_id, :display_name, :description, :media)";
		$query = $database->prepare($sql);
		$params = array(
			':product_id' => $product_id,
			':display_name' => $display_name,
			':description' => $description,
			':media' => $media,
		);
		$query->execute($params);
    }

    public static function getBundles() {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT app_bundles.product_id, bundle_id, display_name, description, media, purchase_url FROM app_bundles INNER JOIN store_product ON app_bundles.product_id = store_product.product_id";
        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    public static function getBundleById($product_id) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT bundle_id, product_id, display_name, description, media FROM app_bundles WHERE product_id=:product_id";
        $query = $database->prepare($sql);
        $query->execute(array(':product_id'=>$product_id));
        return $query->fetch();
    }

    public static function getBundleContents($product_id) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT app_id FROM store_product_apps WHERE product_id = :product_id";
        $query = $database->prepare($sql);
        $query->execute(array(':product_id'=>$product_id));
        return $query->fetchAll();
    }

    public static function deleteBundle($product_id) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "DELETE ab, sp, spa 
                FROM app_bundles AS ab 
                INNER JOIN store_product AS sp ON ab.product_id = sp.product_id 
                INNER JOIN store_product_apps AS spa ON ab.product_id = spa.product_id 
                WHERE ab.product_id=:product_id";
        $query = $database->prepare($sql);
        $query->execute(array(':product_id'=>$product_id));
    }
}