<?php
	
class MessageModel extends Model {
	
	const TABLE = "message";

	function __construct() {
		parent::__construct();
	}
	
    public static function Make() {
        return parent::Create(self::TABLE);
    }
	
	public static function Save($idrow_name, $data_model) {
		return parent::Update(self::TABLE, $idrow_name, $data_model);
	}
	
	public static function getRecord($id) {
		return parent::Read(self::TABLE, "message_id=:id", array(":id"=>$id))[0]; // 0th item of fetchAll()
	}

	public static function getAll() {
		return parent::Read(self::TABLE);
	}
	
	public static function getMyUnreadMessages() {
		$user_id = Session::CurrentUserId();
		if ($user_id < 1) return;
		return self::getUnreadMessagesByUserId($user_id);
	}
	
	public static function getUnreadMessagesByUserId($user_id) {
		$database = DatabaseFactory::getFactory()->getConnection();
		//  case user_id when 0 then false else true end dismissable
        $sql = "SELECT message_id, level, text, created
        		FROM message
				WHERE (user_id = :user_id OR user_id = 0)
				AND (expires IS NULL OR expires >= CURRENT_TIMESTAMP)
				AND message_id NOT IN (SELECT message_id
						from messageread
						where user_id = :user_id)
				ORDER BY created";
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => Session::get('user_id')));
        return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public static function markAsRead($message_id) {
		$user_id = Session::CurrentUserId();
		if ($user_id < 1) return;

		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT COUNT(1) FROM message WHERE message_id = :mid";
		$query = $database->prepare($sql);
		$query->execute(array(
			":mid" => $message_id
		));
		if ($query->fetchColumn() < 1) return;

		$sql = "INSERT INTO messageread (message_id,user_id) VALUES (:msg,:usr)";
		$query = $database->prepare($sql);
		return $query->execute(array(
			":msg" => $message_id,
			":usr" => $user_id
		));
	}
	
	public static function notify_user($message, $level, $user_id, $expires = 0) {
		if (intval($user_id) < 1) return;
		$model = self::Make();
		$model["user_id"] = $user_id;
		unset ($model["created"]); // allow database default to apply
		$model["level"] = intval($level); // e.g. MESSAGE_LEVEL_HAPPY
		$model["text"] = $message;
		if ($expires > 0) $model["expires"] = $expires;
		return self::Save("message_id", $model);
	}
	
	public static function notify_all($message, $level, $expires = 0) {
		$model = self::Make();
		$model["user_id"] = 0;
		unset ($model["created"]); // allow database default to apply
		$model["level"] = intval($level); // e.g. MESSAGE_LEVEL_MEH
		$model["text"] = $message;
		if ($expires > 0) $model["expires"] = $expires;
		return self::Save("message_id", $model);
	}
	
}