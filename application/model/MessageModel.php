<?php

class MessageModel extends Model
{

	const TABLE = "message";

	public function __construct()
	{
		parent::__construct();
	}

	public static function Make()
	{
		return parent::Create(self::TABLE);
	}

	public static function Save($idrow_name, $data_model)
	{
		return parent::Update(self::TABLE, $idrow_name, $data_model);
	}

	public static function getRecord($id)
	{
		return parent::Read(self::TABLE, "message_id=:id", array(":id" => $id))[0]; // 0th item of fetchAll()
	}

	public static function getAll()
	{
		return parent::Read(self::TABLE);
	}

	public static function getMyUnreadMessages()
	{
		$user_id = Session::CurrentUserId();
		if ($user_id < 1) {
			return;
		}

		return self::getUnreadMessagesByUserId($user_id);
	}

	public static function getUnreadMessagesByUserId($user_id)
	{
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

	public static function markAsRead($message_id)
	{
		$user_id = Session::CurrentUserId();
		if ($user_id < 1) {
			return;
		}

		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT COUNT(1) FROM message WHERE message_id = :mid";
		$query = $database->prepare($sql);
		$query->execute(array(
			":mid" => $message_id,
		));
		if ($query->fetchColumn() < 1) {
			return;
		}

		$sql = "INSERT INTO messageread (message_id,user_id) VALUES (:msg,:usr)";
		$query = $database->prepare($sql);
		return $query->execute(array(
			":msg" => $message_id,
			":usr" => $user_id,
		));
	}

	public static function notify_user($message, $level = MESSAGE_LEVEL_HAPPY, $user_id = 0, $expires = 0, $single_instance = true)
	{
		if (intval($user_id) < 1) {
			$user_id = Session::get('user_id');
		}

		if ($single_instance === true) {
			// check to see if we have already notified the user with this exact message/level and they haven't yet read it. If that's the case, exit
			$database = DatabaseFactory::getFactory()->getConnection();
			$query = $database->prepare("
				SELECT message_id FROM message
				WHERE `text` = :txt
				AND `level` = :lvl
				AND `user_id` = :uid
			");
			$query->execute(array(
				":txt" => $message,
				":lvl" => $level,
				":uid" => $user_id
			));
			if ($msg_id = $query->fetchColumn()) { // 0 is falsey, so >0 === found
				$query = $database->prepare("
					SELECT count(1) FROM messageread
					WHERE user_id = :uid
					AND message_id = :mid
				");
				$query->execute(array(
					":uid" => $user_id,
					":mid" => $msg_id,
				));
				if ($query->fetchColumn() === 0) return; // exact notification has already been added and not yet read
			}
		}

		$model = self::Make();
		$model["user_id"] = $user_id;
		unset($model["created"]); // allow database default to apply
		$model["level"] = intval($level); // e.g. MESSAGE_LEVEL_HAPPY
		$model["text"] = $message;
		if ($expires > 0) {
			$model["expires"] = $expires;
		}

		return self::Save("message_id", $model);
	}

	public static function notify_all($message, $level, $expires = 0)
	{
		$model = self::Make();
		$model["user_id"] = 0;
		unset($model["created"]); // allow database default to apply
		$model["level"] = intval($level); // e.g. MESSAGE_LEVEL_MEH
		$model["text"] = $message;
		if ($expires > 0) {
			$model["expires"] = $expires;
		}

		return self::Save("message_id", $model);
	}

}
