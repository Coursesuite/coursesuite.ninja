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

	public static function getAllTemplates() {
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

	public static function createTemplate($name, $subject, $body) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "INSERT INTO mail_templates (name, subject, body) VALUES (:name, :subject, :body)";
		$params = array(
			":name" => $name,
			":subject" => $subject,
			":body" => $body
		);
		$query = $database->prepare($sql);
		$query->execute($params);
	}

	public static function deleteTemplate($id) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "DELETE FROM mail_templates WHERE id = :id";
		$query = $database->prepare($sql);
		$query->execute(array(":id" => $id));
	}
}