<?php

class LoggingModel
{

    public static function systemLog($filter = "", $value = "")
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $clause = "";
        $params = array();
        switch ($filter) {
            case "user":
                $clause = "WHERE digest_user = :digest_user";
                $params[":digest_user"] = $value;
                break;

            case "date":
                $clause = "WHERE added >= :added";
                $params[":added"] = (new DateTime($value))->format("Y-m-d");
                break;

        }
        $sql = "SELECT method_name, digest_user, added, message, param0, param1, param2, param3 FROM applog $clause ORDER BY id DESC";
        $query = $database->prepare($sql);
        $query->execute($params);
        return $query->fetchAll();
    }

    public static function uniqueDigestUsers()
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT DISTINCT(digest_user) as digest_user FROM applog WHERE NOT (digest_user IS NULL OR digest_user = '')";
        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();

    }

    /**
     * logs the class/method, digest user and up to 10 parameters sent in, typically 2 or 3
     * @static static
     * @param methodName - typically __METHOD__
     * @param digestUser - the digest auth user who logged on
     * @param ...$args - zero or more arguments
     * @example LoggingModel::logMethodCall(__METHOD__, $this->username, $org, $app);
     * @return boolean
     */
    public static function logMethodCall($methodName, $digestUser, ...$args)
    {

        $database = DatabaseFactory::getFactory()->getConnection();
        $params["method_name"] = $methodName;
        $params["digest_user"] = $digestUser;
        for ($i = 0; $i < count($args); $i += 1) {
            $params["param" . $i] = $args[$i];
        }
        $sql = "INSERT INTO applog(" . implode(", ", array_keys($params)) . ") VALUES (";
        $modded = array();
        foreach ($params as $param => $value) {
            $modded[":$param"] = is_array($value) ? serialize($value) : $value;
        }
        unset($params);
        $sql .= implode(", ", array_keys($modded)) . ")";
        $query = $database->prepare($sql);
        $query->execute($modded);
        if ($query->rowCount() == 1) {
            return true;
        }
        return false;
    }

    public static function logInternal($methodName, ...$args)
    {

        $database = DatabaseFactory::getFactory()->getConnection();
        $params["method_name"] = $methodName;
        for ($i = 0; $i < count($args); $i += 1) {
            $params["param" . $i] = $args[$i];
        }
        $sql = "INSERT INTO applog(" . implode(", ", array_keys($params)) . ") VALUES (";
        $modded = array();
        foreach ($params as $param => $value) {
            $modded[":$param"] = is_array($value) ? serialize($value) : $value;
        }
        unset($params);
        $sql .= implode(", ", array_keys($modded)) . ")";
        $query = $database->prepare($sql);
        $query->execute($modded);
        if ($query->rowCount() == 1) {
            return true;
        }
        return false;
    }

    /**
     * logs a raw message
     * @static static
     * @param message - a string, up tp 255 characters
     * @return boolean
     */
    public static function logMessage($message)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "INSERT INTO applog(message) VALUES (:message)";
        $query = $database->prepare($sql);
        $query->execute(array(":message" => $message));
        if ($query->rowCount() == 1) {
            return true;
        }
        return false;
    }

}
