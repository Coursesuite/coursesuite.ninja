<?php
class Model
{

    public function __construct()
    {
    }

//  protected abstract function definition();

    /**
     * return an empty row containing all fields of a table
     * set default on fields that are nullable
     * relies on the db user having describe capability
     */
    public static function Create($table, $as_array = true)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->query("DESCRIBE $table");
        $rows = $query->fetchAll(); // PDO::FETCH_COLUMN));
        $results = new stdClass();
        foreach ($rows as $row) {
            // if (!($row->Null == "NO" && $row->Default > "")) {
            $key = $row->Field;
            if ($row->Key == "PRI") {
                // assuming numerical keys
                $value = 0;
            } else if ($row->Null == "YES") {
                $value = null;
            } else {
                $value = "";
            }
            if (is_numeric($row->Default)) {
                $value = intval($row->Default, 10);
            }
            $results->$key = $value;
            // }
        }
        return $as_array ? (array) $results : $results;
    }

    public static function Columns($table) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->query("DESCRIBE $table");
        $cache = new CachedPDOStatement($query);
        $cols = array();
        foreach ($cache as $row) {
            $type = "text";
            $ins = null;
            $size = null;
            $editing = true;
            $visible = true;
            $primary = false;
            if ($row->Key === "PRI") {
                $type = substr($row->Type,0,3)==="int"?"number":"text";
                $primary = true;
                $editing = false;
            } else if ($row->Type === "text" || $row->Type === "varchar(255)") {
                $type = "textarea";
            } else if ($row->Type === "tinyint(1)") {
                $type = "checkbox";
            } else {
                if (stripos($row->Type,"int") !== false) {
                    $type = "number";
                } else {
                    $type = "text";
                    if( preg_match( '!\(([^\)]+)\)!', $row->Type, $match ) ) $size = $match[1];
                }
            }
            if (!empty($row->Default)) {
                if ($row->Default === "current_timestamp()") {
                    $visible = false;
                } else {
                    $ins = $row->Default;
                }
            }

            $col = array(
                "name" => $row->Field,
                "type" => $type,
                "primary" => $primary
            );
            if (!is_null($ins)) $col["insertValue"] = $ins;
            if (!is_null($size)) $col["size"] = $size;
            if ($visible !== true) $col["visible"] = false;
            if ($editing !== true) $col["editing"] = false;
            $cols[] = $col;
        }
        return $cols;
    }

    public static function Read($table, $where = "", $params = array(), $fields = array("*"), $fetchOne = false, $orderby = "")
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        /*if ($fields == array("*")) {
        $fields = array();
        $rs = $database->query("SELECT * FROM $table LIMIT 0");
        for ($i = 0; $i < $rs->columnCount(); $i++) {
        $col = $rs->getColumnMeta($i);
        $fields[] = $col['name'];
        }
        }*/
        if (!is_array($fields)) $fields = array($fields);
        $sql = "SELECT " . implode(",", $fields) . " FROM $table ";
        if (!empty($where)) {
            $sql .= "WHERE $where";
        }
        if ($orderby !== "") $sql .= " ORDER BY $orderby";
        $query = $database->prepare($sql);
// echo $sql; var_dump($params);
        $query->execute($params);
        $count = $query->rowCount();
        if ($fetchOne === true) {
            return $query->fetch();
        }
        return $query->fetchAll();
    }

    /**
     * generic model save routine
     * saves as many fields as you supply, except the id column
     * if id column value is <1 then insert a new record and return the
     * @param table string - name of table
     * @param idrow_name string - name of the id column
     * @param data_model array - associative array of the columns you are updating (must include id column)
     * @return id value
     */
    public static function Update($table, $idrow_name, $data_model, $raw = false)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $values = array();
        $fields = array();
        $keys = array();
        $params = array();
        $idvalue = 0;
        foreach ($data_model as $key => $value) {

            if ($key == $idrow_name) {
                $idvalue = $raw === false ? abs(intval($value, 10)) : $value;
                continue; // can't set a key value anyway
            }

            // so you can set a property to the JSON object and not bother with encoding it
            if (is_object($value) || is_array($value)) {
                $value = json_encode($value, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_NUMERIC_CHECK);
            }

            $field = sprintf('`%s`',$key); // escaped sql field name
            $param = sprintf(':%s',$key); // pdo named parameter

            $fields[] = $field;
            $params[] = $param;
            $values[$param] = $value; // pdo key=>value pairs
            $pairs[] = sprintf('%s = %s', $field, $param); // sql field=:name pairs

        }

       //      if ($key == $idrow_name) {
       //          $idvalue = intval($value, 10);

       //      } else if ($idvalue < 1) {
       //          $keys[] = $key;
       //          $values[] = $value;

       //      } else if (is_object($value) || is_array($value)) {
       // //           // so you can set a property to the JSON object and not bother with encoding it

    			// $fields[] = sprintf('`%s`',$key); // escaped sql field name
    			// $params[] = sprintf(':%s',$key); // pdo named parameter
    			// $values[$param] = json_encode($value, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_NUMERIC_CHECK); // pdo key=>value pairs
    			// $keys[] = sprintf('%s = %s', $field, $param); // sql field=:name pairs

       //      } else {

       //          $fields[] = "$key=:$key";
       //          $params[":" . $key] = $value;
       //      }

        // }
        // if ($idvalue < 1) {
        //     $sql = "INSERT INTO $table (" . implode(",", $keys) . ") VALUES (:" . implode(",:", $keys) . ")";
        //     $query = $database->prepare($sql);
        //     $params = array_combine($keys, $values);
        //     $query->execute($params);
        //     $idvalue = $database->lastInsertId();
        // } else {
        //     $sql = "UPDATE $table SET " . implode(",", $fields) . " WHERE $idrow_name=:ROWID LIMIT 1";
        //     $query = $database->prepare($sql);
        //     $params[":ROWID"] = $idvalue;
        //     $query->execute($params); // PDO allows the params without colons in the paramarray
        // }

        if ($idvalue < 1 && $raw === false) {
            $sql = "INSERT INTO $table (" . implode(', ', $fields) . ") VALUES (" . implode(', ',$params) . ")";
            $query = $database->prepare($sql);
            $query->execute($values);
            $idvalue = $database->lastInsertId();
        } else {
            $sql = "UPDATE $table SET " . implode(', ', $pairs) . " WHERE `$idrow_name` = :ROWID LIMIT 1";
            $query = $database->prepare($sql);
            $values[":ROWID"] = $idvalue;
            $query->execute($values); // PDO allows the params without colons in the paramarray
        }

        return $idvalue;
    }

    // assumes you know what you are doing with your primary key values already
    public static function Insert($table, $data_model)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $values = array();
        $fields = array();
        $keys = array();
        $params = array();
        foreach ($data_model as $key => $value) {
            if (is_object($value) || is_array($value)) {
                $value = json_encode($value, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_NUMERIC_CHECK);
            }
            $field = sprintf('`%s`',$key); // escaped sql field name
            $param = sprintf(':%s',$key); // pdo named parameter
            $fields[] = $field;
            $params[] = $param;
            $values[$param] = $value; // pdo key=>value pairs
            $pairs[] = sprintf('%s = %s', $field, $param); // sql field=:name pairs
        }
        $sql = "INSERT INTO $table (" . implode(', ', $fields) . ") VALUES (" . implode(', ',$params) . ")";
        $query = $database->prepare($sql);
        $query->execute($values);
        return $database->lastInsertId();
    }

    public static function Destroy($table, $where, $params) // $where_clause, $fields)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "DELETE FROM $table WHERE $where";
        $query = $database->prepare($sql);
        $query->execute($params);
    }

    /* utility methods */

    final public static function ReadColumn($table, $column, $where = "", $params = array(), $fetchOne = true, $order = "") {
        $database = DatabaseFactory::getFactory()->getConnection();
        if ($order > "") $order = "ORDER BY $order";
        $query = $database->prepare("SELECT $column FROM $table WHERE $where $order");
        $query->execute($params);
        return ($fetchOne === true) ? $query->fetchColumn() : $query->fetchAll(PDO::FETCH_COLUMN);
    }

    final public static function Exists($table, $where, $params = array()) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("SELECT count(1) FROM $table WHERE $where");
        $query->execute($params);
        return ($query->fetch(PDO::FETCH_COLUMN, 0) > 0);
    }

    final public static function Raw($sql) {
        $database = DatabaseFactory::getFactory()->getConnection();
        // return $database->query($sql);

        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    final public static function ReadBlob($table, $column, $where = "", $params = array()) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("
            SELECT $column FROM $table
            WHERE $where
        ");
        $query->execute($params);
        $blob = null;
        $query->bindColumn(1, $blob, PDO::PARAM_LOB, 0, PDO::SQLSRV_ENCODING_BINARY);
        if ($query->fetch()) {
            return $blob;
        } else {
            return false;
        }
    }

    final public static function WriteBlob($table, $column, $filename, $where = "", $params = array()) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("
            UPDATE $table
            SET $column=:blob
            WHERE $where
        ");
        foreach ($params as $key => $value) {
            $query->bindParam($key, $value);
        }
        $file = new SplFileObject($filename,"r");
        $blob = $file->fread($file->getSize());
        $file = null;
        // $handle = fopen($filename, "rb");
        // $blob = fread($handle, filesize($filename));
        // fclose($handle);
        $query->bindParam(":blob", $blob, PDO::PARAM_LOB);
        return $query->execute();
    }

}
