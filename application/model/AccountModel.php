<?php
/**
 * AccountModel
 * Handles all the PRIVATE user stuff. Just a base model.
 */
class AccountModel extends Model
{

    CONST TABLE_NAME = "users";
    CONST ID_ROW_NAME = "user_id";

    protected $data_model;
    protected $product_model;

    public function found() {
        return (!empty($this->data_model));
    }

    public function get_property($name) {
        return is_numeric($this->data_model->$name) ? intval($this->data_model->$name,10) : $this->data_model->$name;
    }

    public function get_model($include_sub_accounts = false)
    {
        $data = $this->data_model;
        if ($include_sub_accounts === true) {
            $idname = self::ID_ROW_NAME;
            $data->SubAccounts = parent::Read(self::TABLE_NAME, "user_parent_id=:id", array(":id" => $data->$idname));
        }
        return $data;
    }

    public function set_model($data)
    {
        $this->data_model = $data;
    }

    public function __construct($lookup = "id", $match = "")
    {
        parent::__construct();
        if ($lookup === "id" && is_numeric($match) && intval($match,10) > 0) {
        	self::load($match);
        } else if ($lookup === "id" && is_numeric($match) && intval($match,10) === 0) {
            self::make();
        } else if ($lookup === "email" && trim($match) > "") {
            self::loadByEmail($match);
        }
        return $this;
    }

    public function delete($id = 0)
    {
    	if ($id > 0) {
    		parent::Destroy(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $id));
    	} else {
    		$idname = self::ID_ROW_NAME;
    		parent::Destroy(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $data_model->$idname));
    	}
    }

    public function load($id)
    {
    	$this->data_model = parent::Read(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $id),"*",true);
    	return $this;
    }

    public function loadByEmail($id)
    {
        $this->data_model = parent::Read(self::TABLE_NAME, "user_email=:id", array(":id" => $id),"*",true);
        return $this;
    }

    public function make()
    {
    	$this->data_model = parent::Create(self::TABLE_NAME, false);
    	return $this;
    }

    public function save()
    {
        $model = $this->data_model;
        if (isset($model->SubAccounts)) {
            unset($model->SubAccounts);
        }
        return parent::Update(self::TABLE_NAME, self::ID_ROW_NAME, $model);
    }

    public function get_id() {
        if (isset($this->data_model)) {
            $idrowname = self::ID_ROW_NAME;
            return $this->data_model->$idrowname;
        }
    }

    public function get_apikeys() {
        $idname = self::ID_ROW_NAME;
        return self::get_api_keys($this->data_model->$idname);
    }

    public function add_secret_key () {
        if (isset($this->data_model)) {
            $idname = self::ID_ROW_NAME;
            self::generate_secret_key($this->data_model->$idname);
        }
    }

    // log on as this user account instance
    public function log_on() {
        if (self::found()) {
            $this->data_model->user_password_reset_hash = NULL;
            $this->data_model->user_last_login_timestamp = time();
            $this->data_model->user_last_failed_login = NULL;
            $this->data_model->user_suspension_timestamp = NULL;
            $this->data_model->user_password_reset_timestamp = NULL;
            $this->data_model->user_logon_count = (int) $this->data_model->user_logon_count + 1;
            $this->save();
            Auth::set_user_logon_cookie( $this->data_model->user_id);
        }
    }

    public function notify($message, $level = MESSAGE_LEVEL_MEH) {
        $uid = $this->get_id();
        if ($uid > 0) {
            MessageModel::notify_user($message, $level, $uid);
        }
    }




    /* -------------------------------- static functions ------------------------------- */

    public static function generate_secret_key($user_id) {
        $idname = self::ID_ROW_NAME;
        $model = new stdClass();
        $model->$idname = $user_id;
        $model->secret_key = Text::base64enc(Encryption::encrypt(uniqid()));
        return parent::Update(self::TABLE_NAME, self::ID_ROW_NAME, $model);
    }

    public static function get_api_keys($user_id) {
        $results = [];
        $database = DatabaseFactory::getFactory()->getConnection();

        // read the set of users we need to check, ensuring that the first record is the primary account
        $user_ids = Model::ReadColumn(self::TABLE_NAME, self::ID_ROW_NAME, self::ID_ROW_NAME . "=:id or user_parent_id=:id", array(":id" => $user_id), false, "case when user_parent_id = 0 then 0 else 1 end");

        // find any api subscription records for these users
        foreach ($user_ids as $id) {
            $result = [];
            $acc = Model::Read(self::TABLE_NAME, self::ID_ROW_NAME."=:id", array(":id"=>$id),"user_email,secret_key",true);
            $query = $database->prepare("
                SELECT s.added, s.endDate<CURDATE() ended, s.endDate, md5(s.referenceId) apikey, s.subscriptionUrl, s.referenceId, s.status, s.statusReason, pb.label, pb.price, pb.concurrency, pb.product_key
                FROM subscriptions s inner join product_bundle pb on s.product_id = pb.id
                WHERE s.user_id = :id
                AND pb.product_key LIKE 'api-%'
                ORDER BY case when status = 'active' then 0 else 1 end, added desc
                LIMIT 1
            ");
            $query->execute([":id" => (int) $id]);
            $result["id"] = $id;
            $result["primary_account"] = $id === $user_id;
            $result["email"] = $acc->user_email;
            $result["secret"] = $acc->secret_key;
            // $result["secret"] = empty($acc->secret_key) ? "" : Encryption::decrypt(Text::base64dec($acc->secret_key));
            if ($record = $query->fetch()) {
                // $result["order_url"] = empty($record->subscriptionUrl) ? "" : Encryption::decrypt(Text::base64dec($record->subscriptionUrl));
                $result["support_url"] = "mailto:accounts@coursesuite.com.au?subject=Order%20Support%20" . $record->referenceId;
                $result["subscriptionUrl"] = $record->subscriptionUrl;
                $result["apikey"] = $record->apikey;
                $result["added"] = $record->added;
                $result["ended"] = intval($record->ended,10) === 1;
                $result["endDate"] = $record->endDate;
                $result["status"] = $record->status;
                $result["statusReason"] = $record->statusReason;
                $result["name"] = $record->label;
                $result["referenceId"] = $record->referenceId;
                $result["price"] = $record->price;
                $result["concurrency"] = $record->concurrency;
                $result["product_key"] = $record->product_key;
                $result["incomplete"] = false;

            } else {
                $result["incomplete"] = true;
            }
            $result["referrer"] = "?referrer=" . Text::base64enc(Encryption::encrypt($id)) . Config::get('FASTSPRING_PARAM_APPEND');
            $results[] = $result;
        }
        return $results;
    }

    public static function modify_sub_account($sub_user_id, $sub_account_email) {

        $user_id = 0;
        $model = Model::Read("users", "user_id=:id", array(":id" => $sub_user_id), "*", true);
        if (!empty($model)) {

            // modify the email address
            $model->user_email = $sub_account_email;

            // ensure the old password is defunct
            $password = (new Sayable(6))->generate();
            $model->user_password_hash = password_hash($password, PASSWORD_DEFAULT);

            // save the model
            $user_id = Model::Update("users", "user_id", $model);

        }

        // return the id of the modified user, or zero if it was not found
        return $user_id;

    }

    // TODO implement this in a way that is reusable on models
    /*
     *  When posting back from the MeController, check the action is supplied only with a valid id value
     */
    public static function validate_action ($action, $id, $id_raw, $controller) {

        // var_dump([$action, $id, $id_raw, $controller]);

        // ensure id is in fact an number
        // $id = filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min-range' => -1, 'max_range' => PHP_INT_MAX)));

        // view is the default action, action performs no user-insertable actions
        if ($action === "view" || $action === "trial") return true;

        // save is ok if it's a new record
        if ($id === -1 && $controller->requiresPost() && $action === "save") return true;

        // user owns this subscription directly or as a sub-account?
        if ($action === "features" && Model::Exists("subscriptions", "(user_id=:uid OR user_id IN (SELECT user_id FROM users WHERE user_parent_id=:uid)) AND md5(referenceId)=:refid", [":uid"=>Session::CurrentUserId(), ":refid"=>$id_raw])) return true;

        // does the logged on user OWN the user_id?
        $valid_id = Model::Exists("users", "user_id=:them and user_parent_id=:us", [":them"=>$id, ":us"=>Session::CurrentUserId()]);

        // add is GET
        if ($controller->Method === "GET" && $action === "add") return true;

        // edit is GET
        if ($valid_id && ($controller->Method === "GET") && $action === "edit") return true;

        // save is POST
        if ($valid_id && ($controller->Method === "POST") && $action === "save") return true;

        return false;
    }

    public static function get_admin_users() {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("select md5(concat(user_id,:salt)) from users where user_account_type=:level");
        $query->execute([
            ":salt"=>Config::get('HMAC_SALT'),
            ":level"=>Config::get('ADMIN_ACCOUNT_LEVEL')
        ]);
        return $query->fetchAll(PDO::FETCH_COLUMN, 0);
    }

}