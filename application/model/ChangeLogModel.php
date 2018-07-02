<?php

class ChangeLogModel extends Model {
    CONST TABLE_NAME = "changelog";
    CONST ID_ROW_NAME = "id";

    protected $data_model;

    public function get_model()
    {
        return $this->data_model;
    }

    public function set_model($data)
    {
        $this->data_model = $data;
    }

    public function load($id)
    {
        $this->data_model = self::Read(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $id), "*", true);
        return $this;
    }

    public function make()
    {
        $this->data_model = self::Create(self::TABLE_NAME);
        return $this;
    }

    public function save()
    {
        return self::Update(self::TABLE_NAME, self::ID_ROW_NAME, $this->data_model);
    }

    public function delete($id = 0)
    {
        if ($id > 0) {
            self::Destroy(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $id));
        } else {
            $idname = self::ID_ROW_NAME;
            self::Destroy(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $data_model->$idname));
        }
    }

    public function __construct($id = 0)
    {
        parent::__construct();
        if ($id > 0) {
        	self::load($id);
        }
        return $this;
    }

    public static function has_changelog($app_id) {
        return self::Exists(self::TABLE_NAME, "app_id=:app_id", array(":app_id" => $app_id));
    }

    public static function get_app_changelog($app_id) {
        $ids = self::ReadColumn(self::TABLE_NAME, "id", "app_id=:app_id", array(":app_id" => $app_id), false, "added desc");
    	$results = [];
    	foreach ($ids as $id) {
    		$results[] = (new ChangeLogModel((int) $id))->get_model();
    	}
    	return $results;
    }
}