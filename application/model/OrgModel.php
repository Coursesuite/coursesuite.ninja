<?php

class OrgModel extends Model
{

    const TABLE = "orgs";

    public function __construct()
    {
        parent::__construct();
    }

    public static function Make()
    {
        return parent::Create(self::TABLE);
    }

    public static function Save($data_model)
    {
        return parent::Update(self::TABLE, "org_id", $data_model);
    }

    public static function getRecord($id)
    {
        return parent::Read(self::TABLE, "org_id=:id", array(":id" => $id))[0]; // 0th item of fetchAll()
    }

    public static function getAll()
    {
        return parent::Read(self::TABLE);
    }

    public static function getApiModel($key, $active = null)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $field = "org_id";
        $active_sql = "";
        $params = array(":id" => $key);
        if (!is_numeric($key)) {
            $field = "name";
        }
        if ($active !== null) {
            $active_sql = "AND active = :active";
            $params[":active"] = intval($active);
        }

        $sql = "SELECT org_id, name, logo_url, tier, active FROM orgs WHERE $field = :id $active_sql LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute($params);
        return $query->fetch();
    }

}
