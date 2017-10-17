<?php
/**
 * undocumented class
 *
 * @package default
 * @author
 **/
class ApiModel
{

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
			$account = new AccountModel($user_id);
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
		        	SELECT count(1) from apps where app_key = :app and app_id in (

			        	SELECT app_id
			            FROM apps
			            WHERE 0 < (
			            	SELECT count(1) from subscriptions s inner join product p
			            	ON p.id = s.product_id AND p.product_id LIKE 'api-%'
					WHERE md5(s.referenceId) = :hash
					AND s.status = 'active'
			            )
			            AND active = 1
			            AND auth_type = :tokenauth

			UNION

			        	SELECT apt.app_id
			            FROM app_tiers apt
			            INNER JOIN product p ON p.entity_id = apt.id AND p.entity = 'app_tiers'
			            INNER JOIN subscriptions s ON p.id = s.`product_id`
			            WHERE
			                 md5(s.`referenceId`) = :hash
			                 AND s.`status` = 'active'

		            UNION

			            SELECT app_tiers.app_id
			            FROM bundle_apps
			            INNER JOIN app_tiers ON bundle_apps.`app_tier` = app_tiers.`id`
			            WHERE bundle_apps.`bundle` IN (
			                    SELECT product.`entity_id`
			                    FROM product
			                    WHERE product.`id` IN (
			                        SELECT `product_id` FROM subscriptions
			                            WHERE md5(referenceId) = :hash
			                            AND `status` = 'active'
			                    )
			            )
		        	)
		");
		$query->execute(array(":hash" => $subscription_hash, ":app" => $app_key, ":tokenauth" => AUTH_TYPE_TOKEN));
		return ($query->fetch(PDO::FETCH_COLUMN, 0) > 0);
	}


	// the parner function for "find_model_for_token"
	// this generates the token by starting with the md5 (which is a referenceId) and just hashing it... there nothing more to do
	public static function generate_token_for_subscription($md5_referenceId) {
		return password_hash($md5_referenceId, PASSWORD_BCRYPT, array("cost" => 10));
	}

	// find the subscription and user info for an app token (used by /api/validate/)
	// returns the row values if found, otherwise false
	public static function find_model_for_token($hash) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$query = $database->prepare("
			select s.subscription_id, md5(s.referenceId) refId, u.user_id, s.product_id from subscriptions s
			inner join users u on u.user_id = s.user_id
			where s.endDate is null
			and s.status = 'active'
			and u.user_deleted = 0
			and u.user_active = 1
			order by s.added desc
		");
		$query->execute();
		foreach ($query->fetchAll() as $row) {
			if (password_verify($row->refId, $hash)) { // kinda slow since it's a bcrypt hash function

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

	public static function get_white_label($app_key, $subscription_id) {
		$database = DatabaseFactory::getFactory()->getConnection();
		$query = $database->prepare("
			SELECT html, css FROM whitelabel
			WHERE subscription_id=:id
			AND app_key=:key
			LIMIT 1
		");
		$query->execute(array(
			":key"=>$app_key,
			":id"=>$subscription_id
		));
		$result = $query->fetch(PDO::FETCH_ASSOC);
		if (is_array($result)) {
			return $result;
		} else {
			return array(
			             "html" => "",
			             "css" => ""
			);
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
