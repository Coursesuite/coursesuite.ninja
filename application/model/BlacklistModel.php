<?php

class BlacklistModel extends Model
{
	public static function isBlacklisted($email) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$domain = array_pop(explode('@', $email));
		$sql = "select count(1) from blacklist where domain = :domain";
		$query = $database->prepare($sql);
		$query->execute(array(':domain' => $domain));
		if ($query->fetchColumn() > 0) {
			$sql = "update blacklist set attempts = (attempts+1) where domain = :domain";
			$query = $database->prepare($sql);
			$query->execute(array(':domain' => $domain));
			return true;
		}
		return false;
	}
}