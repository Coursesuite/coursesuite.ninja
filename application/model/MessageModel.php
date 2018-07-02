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
		if (Session::userIsLoggedIn()) {
			return self::getUnreadMessagesByUserId(Session::CurrentUserId());
		}
	}

	public static function getUnreadMessagesByUserId($user_id)
	{
		$database = DatabaseFactory::getFactory()->getConnection();
		//  case user_id when 0 then false else true end dismissable
		$sql = "SELECT message_id, level, `text`, created, user_id
				FROM message
				WHERE (user_id = :user_id OR user_id = 0)
				AND (expires IS NULL OR expires >= CURRENT_TIMESTAMP)
				AND message_id NOT IN (SELECT message_id
						from messageread
						where user_id = :user_id)
				ORDER BY created";
		$query = $database->prepare($sql);
		$query->execute(array(':user_id' => $user_id));
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}

	public static function markAsRead($message_id)
	{
		if (Session::userIsLoggedIn()) {
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
				":usr" => Session::CurrentUserId(),
			));
		}
	}

	public static function notify_user($message, $level = MESSAGE_LEVEL_HAPPY, $user_id = 0, $expires = 0, $single_instance = true)
	{


		if ($user_id < 1) {
			$user_id = Session::CurrentUserId();
		}

		if ($single_instance === true) {

			$database = DatabaseFactory::getFactory()->getConnection();
			// look for messages with the same text/level/user
			//	  then filter out messages for the same text/level/user that we have read
			// if any records remain, then we have an unread copy, exit
			$query = $database->prepare("
				SELECT count(1) FROM message
				WHERE md5(`text`)=:txt
				AND level=:lvl
				AND user_id=:uid
				AND message_id NOT IN (
				    SELECT m.message_id FROM message m
				    INNER JOIN messageread r ON (m.message_id = r.message_id AND m.user_id = r.user_id)
					WHERE md5(m.`text`)=:txt
					AND m.level=:lvl
					AND m.user_id=:uid
				)
			");
			$query->execute(array(
				":txt"=>md5($message),
				":lvl"=>$level,
				":uid"=>$user_id,
			));
			if ((int) $query->fetchColumn() > 0) {
				// var_dump("nope, found it already");
				return;
			}
		}
		$message = new dbRow("message");
		$message->user_id = $user_id;
		$message->level = intval($level);
		$message->text = $message;
		if ($expires>0) {
			$message->expires = $expires;
		}
		$message->save();
		return $message->PRIMARY_KEY;
		// $model = self::Make();
		// $model["user_id"] = $user_id;
		// unset($model["created"]); // allow database default to apply
		// $model["level"] = intval($level); // e.g. MESSAGE_LEVEL_HAPPY
		// $model["text"] = $message;
		// if ($expires > 0) {
		// 	$model["expires"] = $expires;
		// }

		// return self::Save("message_id", $model);
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
