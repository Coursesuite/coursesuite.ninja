<?php

class MailTemplateModel extends Model 
{
	public function __construct() {
		parent::__construct();
	}

	public static function Make() {
		return parent::Create('mail_templates');
	}

	public static function Save($table, $idrow_name, $data_model) {
		return parent::Update($table, $idrow_name, $data_model);
	}

	public static function getAllTemplates(){
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT id, name, subject, body FROM mail_templates";
		$query = $database->prepare($sql);
		$query->execute();
		return $query->fetchAll();
	}

	public static function getTemplate($id) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT id, name, subject, body FROM mail_templates WHERE id = :id";
		$query = $database->prepare($sql);
		$query->execute(array(":id"=>$id));
		return $query->fetch();
	}
}