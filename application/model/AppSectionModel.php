<?php

// a PRODUCT represents ONE or MORE apps at a particular TIER LEVEL
class AppSectionModel extends Model
{
	CONST TABLE_NAME = "app_section";
	CONST ID_ROW_NAME = "id";

	protected $data_model;

	public function get_model($include_app_model = false, $active_only = true)
	{
		return $this->data_model;
	}

	public function set_model($data)
	{
		$this->data_model = $data;
	}

	public function __construct($record_id = -1)
	{
		parent::__construct();
		if ($record_id > 0) {
			$data = parent::Read(self::TABLE_NAME, self::ID_ROW_NAME . "=:key", array(":key"=>$record_id), '*', true);
			if (!empty($data)) {
			   $this->data_model = $data;
			}
		} else if ($record_id === 0) {
			$this->data_model = parent::Create(self::TABLE_NAME, false);
		}
		return $this;
	}

	public function delete($id = 0)
	{
		if ($id > 0) {
			parent::Destroy(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $id));
		} else {
			$idname = self::ID_ROW_NAME;
			parent::Destroy(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $data_model->$idname));
		}
	}

	public function make()
	{
		$this->data_model = parent::Create(self::TABLE_NAME, false);
		return $this;
	}

	public function save()
	{
		return parent::Update(self::TABLE_NAME, self::ID_ROW_NAME, $this->data_model);
	}

	public function get_id() {
		if (isset($this->data_model)) {
			$idrowname = self::ID_ROW_NAME;
			return $this->data_model->$idrowname;
		}
	}

}
