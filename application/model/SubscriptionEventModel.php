<?php
class SubscriptionEventModel extends Model {

    CONST TABLE_NAME = "subscription_event";
    CONST ID_ROW_NAME = "id";

    protected $data_model;
    protected $account_model;
    protected $subscription_model;

    public function __get($property) {
    	switch (strtolower($property)) {
    		case "account":
    			if (!isset($this->account_model)) {
    				$this->account_model = new AccountModel("id", $this->$user_id);
    			}
    			return $this->account_model;
    			break;
    		case "subscription":
    			if (!isset($this->subscription_model)) {
    				$this->subscription_model = new SubscriptionModel("id", $this->$subscription_id);
    			}
    			return $this->subscription_model;
    			break;
    		case "payload":
    			return json_decode($this->data_model->payload);
    			break;
    		default:
    			return $this->data_model->property;
    	}
    }

    public function __set($property,$value) {
        var_dump($this->data_model,$property,$value);
    	switch (strtolower($property)) {
    		case "account":
    			$this->account_model = $value;
    			$this->data_model->user_id = $value->user_id;
    			break;
    		case "subscription":
    			$this->subscription_model = $value;
    			$this->data_model->subscription_id = $value->subscription_id;
    			break;
    		case "payload":
    			$this->data_model->payload = json_encode($value, JSON_NUMERIC_CHECK);
    			break;
    		default:
    			if ($property !== self::ID_ROW_NAME) $this->data_model->$property = $value;
    	}
    }

    public function __construct($id = 0)
    {
    	if ($id > 0) {
    		self::load($id);
    	} else {
    		self::make();
    	}
        return $this;
    }

    public function delete()
    {
		$idname = self::ID_ROW_NAME;
		parent::Destroy(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $data_model->$idname));
    }

    public function load($id)
    {
    	$this->data_model = parent::Read(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $id),"*",true);
    	return $this;
    }

    public function make()
    {
    	$this->data_model = parent::Create(self::TABLE_NAME);
    	return $this;
    }

    public function save()
    {
        $model = $this->data_model;
        return parent::Update(self::TABLE_NAME, self::ID_ROW_NAME, $model);
    }
}