<?php

class DigestModel {
	/*
	*	This is an associative array of all the users who can log on using digest auth, including users from the config file
	*	if a particular apikey is specified, the list is limited to that key, which might not exist, which is fine
	*/
	public static function get_users ($apikey = null) {

		$results = Config::get('DIGEST_USERS'); // built-in users (e.g. internal validators, fastspring, etc)

		// if $apikey is set, look up users with subscription to an api product
		$sql = "select md5(s.referenceId) apikey, u.secret_key from users u
			inner join subscriptions s
			on u.user_id = s.user_id
			where s.product_id in (select id from product_bundle where product_key like 'api-%')
			and s.active = 1";
		$params = array();
		if (!is_null($apikey)) {
			$params = array(":key" => $apikey);
			$sql .= " and md5(s.referenceId) = :key";
		}
		$database = DatabaseFactory::getFactory()->getConnection();
		$query = $database->prepare($sql);
		$query->execute($params);
		foreach ($query->fetchAll() as $row) {
			$results[$row->apikey] = Encryption::decrypt(Text::base64dec($row->secret_key));
		}
		return $results;
	}
}