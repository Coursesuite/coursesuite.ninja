<?php

class StaticPageModel extends Model
{

    const TABLE = "static_pages";

    public function __construct()
    {
        parent::__construct();
    }

    public static function Make()
    {
        return parent::Create(self::TABLE);
    }

    public static function Save($idrow_name, $data_model)
    {
        return parent::Update(self::TABLE, $idrow_name, $data_model);
    }

    public static function getRecordByKey($page_key)
    {
        $val = parent::Read(self::TABLE, "page_key=:id", array(":id" => $page_key));
        if (isset($val[0])) {
            return $val[0];
        }
        // since this was fetch-all'd
        return false;
    }

    public static function getRecord($id)
    {
        return parent::Read(self::TABLE, "id=:id", array(":id" => $id))[0]; // since this was fetch-all'd
    }

    public static function getAll()
    {
        return parent::Read(self::TABLE);
    }

}
