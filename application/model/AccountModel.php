<?php
/**
 * AccountModel
 * Handles all the PRIVATE user stuff. Just a base model.
 */
class AccountModel extends Model
{

    CONST TABLE_NAME = "users";
    CONST ID_ROW_NAME = "user_id";

    protected $data_model;
    protected $product_model;

    public function get_model()
    {
        $data = $this->data_model;
        return (array) $data;
    }


    public function set_model($data)
    {
        $this->data_model = $data;
    }

    public function __construct($row_id = 0)
    {
        parent::__construct();
        if ($row_id > 0) {
        	self::load($row_id);
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

    public function load($id)
    {
    	$this->data_model = parent::Read(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $id))[0]; // 0th of a fetchall
    	return $this;
    }

    public function make()
    {
    	$this->data_model = parent::Create(self::TABLE_NAME);
    	return $this;
    }

    public function save()
    {
        return parent::Update(self::TABLE_NAME, self::ID_ROW_NAME, $this->data_model);
    }

}