<?php
class Model {

    function __construct() {
    }

//  protected abstract function definition();

    /**
     * return an empty row containing all fields of a table
     * set default on fields that are nullable
     * relies on the db user having describe capability
     */
    public static function Create($table) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->query("DESCRIBE $table");
        $rows = $query->fetchAll(); // PDO::FETCH_COLUMN));
        $results = array();
        foreach ($rows as $row) {
           // if (!($row->Null == "NO" && $row->Default > "")) {
                $key = $row->Field;
                if ($row->Key == "PRI") { // assuming numerical keys
                    $value = 0;
                } else if ($row->Null == "YES") {
                    $value = null;
                } else {
                    $value = "";
                }
                if (is_numeric($row->Default)) {
                    $value = intval($row->Default,10);
                }
                $results[$key] = $value;
           // }
        }
        return $results;
    }

    public static function Read($table, $where = "", $params = array(), $fields = array("*")) {
        $database = DatabaseFactory::getFactory()->getConnection();
        /*if ($fields == array("*")) {
	        $fields = array();
			$rs = $database->query("SELECT * FROM $table LIMIT 0");
			for ($i = 0; $i < $rs->columnCount(); $i++) {
			    $col = $rs->getColumnMeta($i);
			    $fields[] = $col['name'];
			}	        
        }*/
	    $sql = "SELECT ". implode(",",$fields) . " FROM $table ";
	    if (!empty($where)) {
		    $sql .= "WHERE $where";
	    }
        $query = $database->prepare($sql);
        $query->execute($params);
        $count = $query->rowCount();
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
    public static function Update($table, $idrow_name, $data_model) {
        $database = DatabaseFactory::getFactory()->getConnection();

        $values = array();
        $fields = array();
        $keys = array();
        $params = array();
        $idvalue = 0;
        foreach ($data_model as $key => $value) {
            if ($key == $idrow_name) {
                $idvalue = intval($value, 10);
            } else if ($idvalue < 1) {
                $keys[] = $key;
                $values[] = $value;
            } else {
                $fields[] = "$key=:$key";
                $params[":" . $key] = $value;
            }
        }
        if ($idvalue < 1) {
            $sql = "INSERT INTO $table (" . implode(",", $keys) . ") VALUES (:" . implode(",:", $keys) . ")";
            $query = $database->prepare($sql);
            $params = array_combine($keys,$values);
            $query->execute($params);
            $idvalue = $database->lastInsertId();
        } else {
            $sql = "UPDATE $table SET " . implode(",", $fields) . " WHERE $idrow_name=:ROWID LIMIT 1";
            $query = $database->prepare($sql);
            $params[":ROWID"] = $idvalue;
            $query->execute($params); // PDO allows the params without colons in the paramarray
        }
        return $idvalue;
    }

    public static function Destroy($table, $where_clause, $fields) {

    }


}