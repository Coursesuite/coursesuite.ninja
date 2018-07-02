<?php
/*
 * a generic class for inserting and updating database rows and accessing fields
 *
 * $model = new dbRow(table, [whereClause, *paramArray, *orderBy]);
 *
 * $model = new dbRow(table, 6);
 * $title = $model->title; // getter
 * $model->version = 6.1; // setter
 *
 * model->format("config","serial"); // can be serial, json, or default
 * $model->config = (object); // serialises object as serial object
 *
 * $model->save(); // if primary key hasn't been set, performs an insert
 * echo $model->PRIMARY_KEY; // 6

 * $model = new dbRow(table, ["x=:x or y=:y", [":x"=>6,":y"=>5"]);

 * $model = new dbRow(table);
 * $model->title = "foo"; // setter

 */
class dbRow {
    private $_conn;
    private $_model;
    private $_table;
    private $_id_row_name;
    private $_loaded = false;

    // test to see if a string can convert to json
    private static function isJson($str) {
        if (is_null($str) || empty($str)) return false;
        $json = json_decode($str);
        return $json && $str != $json;
    }

    private static function isSerial($string) {
        return (@unserialize($string) !== false);
    }

    function __construct($table, $keyValue = null) {
        $this->_table = $table;
        $this->_conn = DatabaseFactory::getFactory()->getConnection();

        // should push this to a function you can run across all tables, once
        $query = $this->_conn->query("DESCRIBE $table");
        $cache = new CachedPDOStatement($query);
        foreach ($cache as $row) {
            if ($row->Key === "PRI") {
                $this->_id_row_name = $row->Field;
            }
            $this->_model[$row->Field] = [
                "value" => null,
                "type" => $row->Type,
                "default" => $row->Default,
                "nullable" => ($row->Null === "YES"),
                "format" => "default",
            ];

            // re-jig types as required
            if ($row->Type === "tinyint(1)") {
                $this->_model[$row->Field]["type"] = "boolint";
            } else if (strpos($row->Type,"int") !== false) { // e.g. tinyint, smallint, int, bigint
                $this->_model[$row->Field]["type"] = "int";
            } else if (strpos($row->Type,"char") !== false) { // e.g. char, varchar
                $this->_model[$row->Field]["type"] = "string";
                if( preg_match( '!\(([^\)]+)\)!', $row->Type, $match ) ) {
                    $this->_model[$row->Field]["size"] = intval($match[1],10);
                }
            } else if (strpos($row->Type,"decimal") !== false) { // e.g. decimal(8,2)
                $this->_model[$row->Field]["type"] = "float";
            }
        }
        $query->closeCursor();

        if (!is_null($keyValue)) {
            if (is_array($keyValue)) {
                list($where, $params, $order) = array_merge($keyValue, [null,null,null]);
                $query = $this->_conn->prepare("SELECT * FROM $table WHERE {$where} {$order} LIMIT 1");
                $query->execute($params);
            } else {
                $query = $this->_conn->prepare("SELECT * FROM $table WHERE `{$this->_id_row_name}` = :ROWID LIMIT 1");
                $query->execute([":ROWID" => $keyValue]);
            }
            $record = $query->fetch(PDO::FETCH_ASSOC);
            if (!empty($record)) {
                $this->_loaded = true;
                foreach ($record as $key => $value) {
                    $this->_model[$key]["value"] = $value;
                    if (in_array($this->_model[$key]["type"],["string","text"])) {
                        if (self::isJson($value)) {
                            $this->_model[$key]["format"] = "json";
                        } else if (self::isSerial($value)) {
                            $this->_model[$key]["format"] = "serial";
                        }
                    }
                }
            }
        }
        return $this;
    }

    // was this model loaded from the database?
    public function loaded() {
        return $this->_loaded;
    }

    // magic method
    public function __set($property, $value){

         // skip primary key
        if ($property === $this->_id_row_name) {
            return;
        }

        // skip empty properties that will get a default
        if (empty($value) && !empty($this->_model[$property]["default"])) {
            return;
        }

        // crop to length if supported / needed
        if (isset($this->_model[$property]["size"]) && strlen($value) > $this->_model[$property]["size"]) {
            $value = substr($value, 0, $this->_model[$property]["size"]);
        }

        // serialize objects into json
        if (is_object($value) || is_array($value)) {
            if ($this->_model[$property]["format"] === "serial") {
                $value = serialize($value);
            } else {
                $value = json_encode($value, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_NUMERIC_CHECK);
            }
        }

        // convert booleans to a number if they are stored in a tinyint, which we internally call a boolint
        if (is_bool($value) && $this->_model[$property]["type"] === "boolint") {
            $value = ($value===true) ? 1 : 0;
        }

        // store the property value
        return $this->_model[$property]["value"] = $value;
    }

    // magic method
    public function __get($property){

        // to get the primary key, you can ask for it like so
        if ($property === "PRIMARY_KEY") {
            $property = $this->_id_row_name;
        }

        // read the raw cached value
        $result = array_key_exists($property, $this->_model)
            ? $this->_model[$property]["value"]
            : null
            ;

        // return an object if the value parses from json
        if ($this->_model[$property]["format"] === "json") { // self::isJson($result)) {
            return json_decode($result);
        }

        if ($this->_model[$property]["format"] === "serial") { // self::isSerial($result)) {
            return unserialize($result);
        }

        // ctype values for an early return
        switch ($this->_model[$property]["type"]) {
            case "float":
                return floatval($result);
                break;
            case "int":
                return intval($result,10);
                break;
            case "boolint":
                return (intval($result,10)===1);
                break;
            case "timestamp":
                if (is_null($result) || empty($result)) return time();
                break;
            case "datetime":
            case "date":
                return new DateTime($result); // return date("r", strtotime($result, mktime(0, 0, 0)));
                break;
        }
        return $result;
    }

    // read the names of the properties
    public function properties() {
        return array_keys($this->_model);
    }

    // set the format of a property (text/varchar only)
    public function format($key,$value) {
        if (array_key_exists($key,$this->_model) && in_array($value, ["default","serial","json"]) && in_array($this->_model[$key]["type"],["string","text"])) {
            $this->_model[$key]["format"] = $value;
        }
    }

    public function delete() {
        // keyvalue is the value of the tables primary key
        $keyValue = $this->_model[$this->_id_row_name]["value"];
        $query = $this->_conn->prepare("DELETE FROM {$this->table} WHERE `{$this->_id_row_name}`=:ROWID LIMIT 1");
        $query->execute(array(":ROWID"=>$keyValue));
        return true;
    }

    public function save() {
        // keyvalue is the value of the tables primary key
        $keyValue = $this->_model[$this->_id_row_name]["value"];

        // create parameters and values based on model
        foreach ($this->_model as $key => $properties) {
            $field = sprintf('`%s`', $key);
            $param = sprintf(':%s', $key);
            $fields[] = $field;
            $params[] = $param;
            $value = $properties["value"];

            // update unset values with their default where appropriate
            if (is_null($value) && !$properties["nullable"] && $key!==$this->_id_row_name) {
                switch ($properties["type"]) {
                    case "float":
                        $value = floatval($properties["default"]);
                        break;
                    case "int":
                        $value = intval($properties["default"],10);
                        break;
                    case "boolint":
                        $value = (intval($properties["default"],10)===1) ? 1 : 0;
                        break;
                    case "timestamp":
                        if (is_null($properties["default"]) || empty($properties["default"])) $value = time();
                        break;
                    case "datetime":
                    case "date":
                        $value = new DateTime($properties["default"]); // return date("r", strtotime($result, mktime(0, 0, 0)));
                        break;
                    default:
                        $value = $properties["default"];
                }
                // echo "\nHad to set default of $value on $key of type " . $properties["type"];
            }
            $values[$param] = $value;
            $pairs[] = sprintf('%s = %s', $field, $param);
        }

        // if the keyValue is zero or empty, treat this as an insert, otherwise an update
        if (($this->_model[$this->_id_row_name]["type"] === "int" && $keyValue < 1) || ($this->_model[$this->_id_row_name]["type"] !== "int" && empty($keyValue))) {
            $sql = "INSERT INTO {$this->_table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ',$params) . ")";
            $query = $this->_conn->prepare($sql);
            $query->execute($values);

            // modify the id row value with the newly inserted id value
            $keyValue = $this->_conn->lastInsertId();
            $this->_model[$this->_id_row_name]["value"] = $keyValue;
        } else {
            $sql = "UPDATE {$this->_table} SET " . implode(', ', $pairs) . " WHERE `{$this->_id_row_name}` = :ROWID LIMIT 1";
            $query = $this->_conn->prepare($sql);
            $values[":ROWID"] = $keyValue;
            $query->execute($values);
        }

        // return the primary key value
        return $keyValue;
    }

    // get the view model (key => typed-value)
    public function view() {
        foreach (array_keys($this->_model) as $key) {
            $results[$key] = $this->$key;
        }
        return $results;
    }

}