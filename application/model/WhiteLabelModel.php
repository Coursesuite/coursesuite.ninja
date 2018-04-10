<?php

class WhiteLabelModel extends Model {
    CONST TABLE_NAME = "whitelabel";
    CONST ID_ROW_NAME = "id";

    protected $data_model;

    public function get_model()
    {
        $data = $this->data_model;
        return $data;
    }

    public function set_model($data)
    {
        $this->data_model = $data;
    }

    public function __construct($key, ...$params)
    {
        parent::__construct();
        $idname = self::ID_ROW_NAME;
        if ($key === $idname && count($params) === 1) {
        	$value = $params[0];
        	if ($value === 0) {
        		$this->data_model = parent::Create(self::TABLE_NAME);
        	} else {
				$data = parent::Read(self::TABLE_NAME, "{$key} = :key", array(":key"=>$match),'*',true);
				if (!empty($data)) {
					$this->data_model = $data;
				}
        	}
        } else if ($key === "get") {
            $where = [];
            $pwhere = [];
            foreach ($params[0] as $prop => $value) {
                $pwhere[":{$prop}"] = $value;
                $where[] = "{$prop}=:{$prop} AND ";
            }
            $data = parent::Read(self::TABLE_NAME, basename(implode('', $where), " AND "), $pwhere, '*', true);
            if (empty($data)) {
                $this->make();
                foreach ($params[0] as $prop => $value) {
                    $this->data_model->$prop = $value;
                }
                $this->data_model->$idname = $this->save();
            } else {
                $this->data_model = $data;
            }
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
        $this->data_model = parent::Create(self::TABLE_NAME, false); // FFS
        return $this;
    }

    public function save()
    {
		$idname = self::ID_ROW_NAME;
		$this->data_model->$idname = parent::Update(self::TABLE_NAME, $idname, $this->data_model);
    	return $this->data_model->$idname;
    }

    public function get_id() {
        if (isset($this->data_model)) {
            $idrowname = self::ID_ROW_NAME;
            return $this->data_model->$idrowname;
        }
    }


}