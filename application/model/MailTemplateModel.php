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
		$sql = "SELECT id, name, subject, body, body_plain FROM mail_templates";
		$query = $database->prepare($sql);
		$query->execute();
		return $query->fetchAll();
	}

	// For the admin interface 
	public static function getTemplate($id) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT id, name, subject, body, body_plain FROM mail_templates WHERE id = :id";
		$query = $database->prepare($sql);
		$query->execute(array(":id"=>$id));
		return $query->fetch();
	}

	// get email template by name
	public static function getEmail($name) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT subject, body, body_plain FROM mail_templates WHERE name = :name";
		$query = $database->prepare($sql);
		$query->execute(array(":name"=>$name));
		return $query->fetch();
	}

	/* Gets the live version of the template from mail_templates_published */
	public static function getLiveTemplate($name) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT id, name, subject, body, body_plain FROM mail_templates_published WHERE name = :name";
		$query = $database->prepare($sql);
		$query->execute(array(":name"=>$name));
		return $query->fetch();
	}

	/* $template - array containing name, subject, body */
	public static function createTemplate($template) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "INSERT INTO mail_templates (name, subject, body, body_plain) VALUES (:name, :subject, :body, :body_plain)";
		$params = array(
			":name" => $template["name"],
			":subject" => $template["subject"],
			":body" => $template["body"],
			":body_plain" => $template["body_plain"]
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

	/* $template - array containing name, subject, body */
	public static function publishTemplate($template) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "INSERT INTO mail_templates_published (name, subject, body, body_plain) VALUES(:name, :subject, :body, :body_plain) ON DUPLICATE KEY UPDATE name=:name, subject=:subject, body=:body, body_plain=:body_plain";
		$params = array(
			":name" => $template["name"],
			":subject" => $template["subject"],
			":body" => $template["body"],
			":body_plain" => $template["body_plain"]
		);
		$query = $database->prepare($sql);
		$query->execute($params);
	}
	
	/* Saves the template if it hasnt already been. (used when publishing) 
	$template - array containing name, subject, body */
	public static function ifNotSaved($template) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "INSERT INTO mail_templates (name, subject, body) VALUES(:name, :subject, :body) ON DUPLICATE KEY UPDATE name=:name, subject=:subject, body=:body";
		$params = array(
			":name" => $template["name"],
			":subject" => $template["subject"],
			":body" => $template["body"],
			":body_plain" => $template["body_plain"]
		);
		$query = $database->prepare($sql);
		$query->execute($params);
	}

}