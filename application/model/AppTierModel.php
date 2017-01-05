<?php

class AppTierModel Extends Model
{

    CONST TABLE_NAME = "app_tiers";
    CONST ID_ROW_NAME = "id";

    protected $data_model;
    protected $product_model;

    public function get_model()
    {
        $data = $this->data_model;
        $data->Product = self::get_product();
        return (array) $data;
    }

    protected function get_product()
    {
        if (!isset($this->product_model)) {
            $idname = self::ID_ROW_NAME;
            $this->product_model = (new ProductModel(self::TABLE_NAME, $this->data_model->$idname))->get_model();
        }
        return $this->product_model;
    }

    public function set_model($data)
    {
        if (isset($data->Product)) unset($data->Product);
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

    public static function get_tiers($app_id)
    {
    	$database = DatabaseFactory::getFactory()->getConnection();
	$sql = "SELECT id
		FROM app_tiers
		WHERE app_id = :id
		ORDER BY tier_level
	";
	$query = $database->prepare($sql);
	$query->execute(array(':id' => $app_id));
	$results = [];
	foreach ($query->fetchAll() as $row) {
		$results[] = (new AppTierModel($row->id))->get_model();
	}
	return $results;
    }

}