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
    public function get($default = "")
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("SELECT value FROM keystore WHERE `key`=:key LIMIT 1");
        $query->execute(array(":key" => $this->lookup));
        $value = $query->fetchColumn();
        if ($query->rowCount() > 0) {
            return $value;
        }
        return $default;
    }

    public function exists() {
        return ($this->get() > "");
    }
    // PUT a value into the keystore
    // @param $value TEXT
    // @param optional $interval NUMBER of minutes to check before allowing the udpate
    public function put($value, $interval = null)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        if (!is_null($interval) && ((int) $interval > 0)) {
            $sql = "SELECT COUNT(1) FROM keystore WHERE `key`=:key AND lastupdated < TIMESTAMP(DATE_SUB(NOW(), INTERVAL :interval MINUTE))";
            $query = $database->prepare($sql);
            $query->execute(array(
                ":key" => $this->lookup,
                ":interval" => $interval,
            ));
            if (intval($query->fetchColumn()) == 0) {
                return false;
            }
        }
        $query = $database->prepare("insert into keystore (`key`,`value`) values (:key,:value) on duplicate key update `value` = :value");
        $query->execute(array(
            ":key" => $this->lookup,
            ":value" => $value,
        ));
    }
    // find out, in minutes, the age of the lastupdated column
    public function age()
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT TIMESTAMPDIFF(MINUTE, lastupdated, NOW()) FROM keystore WHERE `key`=:key";
        $query = $database->prepare($sql);
        $query->execute(array(":key" => $this->lookup));
        if ($query->rowCount() > 0) {
            return (int) $query->fetchColumn(0);
        }
        return PHP_INT_MAX;
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
