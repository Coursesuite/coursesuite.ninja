<?php
class LicenceModel extends dbRow {

    CONST TABLE_NAME = "licence";

    // look up via id (internal) or licencekey (api validation)
    public function __construct($lookup = "id", $match = "")
    {
        if ($lookup === "id" && is_numeric($match) && intval($match,10) > 0) {
	        parent::__construct(self::TABLE_NAME, $match);
        } else if ($lookup === "id" && is_numeric($match) && intval($match,10) === 0) {
	        parent::__construct(self::TABLE_NAME, null);
        } else if ($lookup === "licencekey" && trim($match) > "") {
            parent::__construct(self::TABLE_NAME,["licencekey=:k", [":k"=>$match]]);
        } else {
        	parent::__construct(self::TABLE_NAME);
        }
        return $this;
    }

}