<?php

class CategoryModel extends Model
{
	public static function getAllCategories(){
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT category_id, name, description FROM categories";
		$query = $database->prepare($sql);
		$query->execute();
		return $query->fetchAll();
	}

	public static function getCategoryById($category_id){
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT category_id, name, description FROM categories WHERE category_id = :category_id";
		$query = $database->prepare($sql);
		$query->execute(array(":category_id" => $category_id));
		return $query->fetch();		
	}
}