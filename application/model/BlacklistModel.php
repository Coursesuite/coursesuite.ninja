<?php

class BlacklistModel
{
	public static function isBlacklisted($email) {
		$database = DatabaseFactory::getFactory()->getConnection();
		if (empty($email)) return false;
		list($name, $domain) = explode('@', $email);
        if(!checkdnsrr($domain,'MX')) {
        	return true; // domain can't even receive email
        }
		$query = $database->prepare("
		      SELECT count(1)
		      from blacklist
		      where domain = :domain
		");
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