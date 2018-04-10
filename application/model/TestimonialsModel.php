<?php

class TestimonialsModel extends Model
{

    const TABLE_NAME = "testimonials";
    const ID_ROW_NAME = "id";
    protected $data_model;

    public function get_model()
    {
        return $this->data_model;
    }

    public static function get_all_models()
    {
        $results = [];
        $idrowname = self::ID_ROW_NAME;
        $rows = Model::Read(self::TABLE_NAME, "", [], self::ID_ROW_NAME, false);
        foreach ($rows as $row) {
            $results[] = (new TestimonialsModel("id", $row->$idrowname))->get_model();
        }
        return $results;
    }

    public function set_model($data)
    {
        $this->data_model = $data;
    }

    public function __construct($column = "id", $match = 0)
    {
        parent::__construct();
        $params = [":key"=>$match];
        $where = "";
        switch ($column) {
            case "id":
                if ($match === 0) { // create
                    $this->data_model = parent::Create(self::TABLE_NAME);
                } else {
                    $where = self::ID_ROW_NAME . "=:key";
                }
                $fetchOne = true;
                break;

        }
        if (!empty($where)) {
            $data = parent::Read(self::TABLE_NAME, $where, $params, '*', $fetchOne);
            if (!empty($data)) {
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

    public function make()
    {
        $this->data_model = parent::Create(self::TABLE_NAME);
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
