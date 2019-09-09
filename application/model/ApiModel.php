<?php
/**
 * undocumented class
 *
 * @package default
 * @author
 **/
class ApiModel
{

	/*
	 * API MODS
	 * These are functions that the user can perform on the API that affect the way apps work
	 * For instance, White Labelling, Custom scorm templates, etc
	 */
	public static function get_api_mods($data = null) {
		$model = array(
			"whitelabel" => array(
				"label" => "White Label",
				"enabled" => true
			),
			"customtemplate" => array(
				"label" => "Custom template",
				"enabled" => false
			),
			"publishurl" => array(
				"label" => "Publish to Url",
				"enabled" => true
			),
		);
		if (isset($data)) {
			foreach ($data as $key => $value) {
				$model[$key]["enabled"] = $value["enabled"];
			}
		}
		return $model;
	}

	public static function publicApi($user_id) {
		$api = [];
		$subs = SubscriptionModel::get_current_subscribed_apps_model($user_id);
		foreach ($subs as $sub) {
			if ($sub["app_key"] === "api") {
				$api = $sub["subs"][0]; // i hope there's only one
				break;
			}
		}
		if ($api) {
			$model = array();
			$model["apikey"] = md5($api['referenceId']);
			$account = new AccountModel("id",$user_id);
			$secret_key = $account->get_property("secret_key");
			if (!isset($secret_key) || empty($secret_key)) {
				$secret_key = uniqid();
				$data = $account->get_model(); // todo refactor using new model from coursebuildr
				$data["secret_key"] = Text::base64enc(Encryption::encrypt($secret_key));
				$account->set_model($data);
				$account->save();
			} else {
				$secret_key = Encryption::decrypt(Text::base64dec($secret_key));
			}
			$model["secret_key"] = $secret_key;
			$model["subscription"] = $api;
			return $model;
		}
		return null;
	}

	public static function validate_app_is_in_subscription($subscription_hash, $app_key) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$query = $database->prepare("
			SELECT count(1) FROM subscriptions
			WHERE md5(referenceId) = :hash
			AND status = 'active'
			AND product_id IN (
				SELECT pb.id FROM apps a JOIN product_bundle pb ON 1 = (find_in_set(cast(a.app_id AS nchar),pb.app_ids) > 0)
				WHERE a.app_key = :app_key
			)
		");
		$query->execute(array(":hash" => $subscription_hash, ":app_key" => $app_key));
		return ($query->fetchColumn(0) > 0);
	}

	// the parner function for "find_model_for_token"
	// this generates the token by starting with the md5 (which is a referenceId) and just hashing it... there nothing more to do
	public static function generate_token_for_subscription($md5_referenceId) {
		$hash = password_hash($md5_referenceId, PASSWORD_BCRYPT, array("cost" => 10));

		return $hash;
	}

	public static function find_hash_for_token($token) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$query = $database->prepare("select md5(referenceId) ref from subscriptions order by subscription_id desc");
		$query->execute();
		foreach ($query->fetchAll() as $row) {
			if (password_verify($row->ref, $token)) { // kinda slow since it's a bcrypt hash function
				return $row->ref;
				break;
			}
		}
		return null;
	}

	// find the subscription and user info for an app token (used by /api/validate/)
	// returns the row values if found, otherwise false
	public static function find_model_for_token($hash) {
		LoggingModel::logMethodCall(__METHOD__, $hash);
		$database = DatabaseFactory::getFactory()->getConnection();
		$query = $database->prepare("
			select s.subscription_id, md5(s.referenceId) refId, u.user_id, s.product_id from subscriptions s
			inner join users u on u.user_id = s.user_id
			where (s.endDate is null or s.endDate > now())
			and s.status = 'active'
			and u.user_deleted = 0
			and u.user_active = 1
			order by s.added desc
		");
		$query->execute();
		foreach ($query->fetchAll() as $row) {
			if (password_verify($row->refId, $hash)) { // kinda slow since it's a bcrypt hash function
// error_log("API MODEL\nfind_model_for_token\n{$hash}\nrow\n" . print_r($row,true), 1,"debug@coursesuite.com.au");

				$result = new stdClass();
				$result->subscription = $row->subscription_id;
				$result->product = $row->product_id;
				$result->user = $row->user_id;
				$result->hash = $row->refId;
				return $result;

				break;
			}
		}
		return false;
	}

	// grab the details of the whitelabelling
	// you only want to grab if the template column is set rather than its value
	public static function get_white_label($app_key, $subscription_model) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$query = $database->prepare("
			SELECT html, css, publish_to
			FROM whitelabel
			WHERE subscription_id=:id
			AND app_key=:key
			LIMIT 1
		");
		$query->execute(array(
			":key"=>$app_key,
			":id"=>$subscription_model->subscription
		));
		$result = $query->fetch(PDO::FETCH_ASSOC);
		if (is_array($result)) {
			$result["template"] = file_exists(Config::get("PATH_ATTACHMENTS") . md5($subscription_model->hash . Config::get("HMAC_SALT")) . "/" . $app_key . "/template.zip");
			return $result;
		} else {
			return [
				"html" => "",
				"css" => "",
				"publish_to" => "",
				"template" => false
			];
		}
	}

	// set the values of a white labelled app. if html and css is null, erase the record.
	public static function set_white_label($app_key, $subscription_id, $html = null, $css = null) {
		$database = DatabaseFactory::getFactory()->getConnection();
		if (is_null($html) && is_null($css)) {
			$query = $database->prepare("
				DELETE from whitelabel
				WHERE app_key=:key
				AND subscription_id=:sub
			");
			$query->execute();
		} else {
			$query = $database->prepare("
				INSERT INTO whitelabel (app_key, subscription_id, html, css)
				VALUES (:key, :id, :html, :css)
				ON DUPLICATE KEY UPDATE html=:html, css=:css
			");
			$query->execute(array(
				":id" => $subscription_id,
				":key" => $app_key,
				":html" => $html,
				":css" => $css
			));
		}
	}

	public static function get_publish_url($hash, $token) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$query = $database->prepare("
			SELECT publish_url
			FROM api_requests
			WHERE digest_user = :hash
			AND token = :token
			LIMIT 1
		");
		$query->execute(array(
			":hash" => $hash,
			":token" => $token
		));
		return $query->fetchColumn();
	}

	public static function record_api_request($hash, $token, $publish_url) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$query = $database->prepare("
			INSERT INTO api_requests (digest_user, token, publish_url, month)
			VALUES (:hash, :token, :url, MONTH(CURRENT_DATE()))
		");
		$url =  is_null($publish_url) ? "" : $publish_url;
		$query->execute(array(
			":hash" => $hash,
			":token" => $token,
			":url" => $url
		));
		return true;
	}

	public static function usage_cap_remaining($hash) {
		$database = DatabaseFactory::getFactory()->getConnection();

		// get usage limit (launches per month)
		$query = $database->prepare("
			SELECT usage_cap
			FROM api_limits
			WHERE digest_user = :hash
			LIMIT 1
		");
		$query->execute(array(
			":hash" => $hash
		));
		$cap = intval($query->fetchColumn(), 10); // how many ues per month this user can have
		if ($cap < 1) $cap = 65535; // todo: write this value back to the db

		// get usage limit (launches per month)
		$query = $database->prepare("
			SELECT count(1)
			FROM api_requests
			WHERE digest_user = :hash
			AND `month` = MONTH(CURRENT_DATE())
		");
		$query->execute(array(
			":hash" => $hash
		));
		$usage = $query->fetchColumn();

		return ($usage < $cap);
	}

} // END class ApiModel
