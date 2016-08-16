<?php

class KeyStoreInstance
{
    private $lookup;
    public function __construct($key)
    {
        if (empty($key)) {
            throw new InvalidArgumentException("KeyStore must be initialised with a key");
        }
        $this->lookup = $key;
    }
    public function get()
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("SELECT value FROM keystore WHERE `key`=:key LIMIT 1");
        $query->execute(array(":key" => $this->lookup));
        $value = $query->fetchColumn();
        if ($query->rowCount() > 0) {
            return $value;
        }
        return "";
    }
    public function put($value)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("insert into keystore (`key`,`value`) values (:key,:value) on duplicate key update `value` = :value");
        $query->execute(array(
            ":key" => $this->lookup,
            ":value" => $value,
        ));
    }
}

class KeyStore
{
    public static function find($key)
    {
        return new KeyStoreInstance($key);
    }
}

/*
usage:
$key = KeyStore::find("apple");
$key->put("RED");

KeyStore::find("lemon")->put("yellow"); // doesn't matter if it exists or not

echo KeyStore::find("apple")->get(); // outputs RED
 */
