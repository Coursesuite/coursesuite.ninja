<?php
/* for talking to fastspring, which in general is a do-one-operation type of arrangement, so separate curl calls seem to be ok */
class FastSpringModel {

	private static $ch;

	private static function init($endpoint, $method = "GET") {
        self::$ch = curl_init();
        global $PAGE;
        $useragent = $PAGE->last_browser;
        if (empty($useragent)) { $useragent = "CourseSuite/1.0"; }
        curl_setopt(self::$ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json, text/javascript, */*'));
        curl_setopt(self::$ch, CURLOPT_USERPWD, Config::get("FASTSPRING_BASICAUTH_USERNAME") . ":" . Config::get("FASTSPRING_BASICAUTH_PASSWORD"));
        curl_setopt(self::$ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt(self::$ch, CURLOPT_TIMEOUT, 10);
		curl_setopt(self::$ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt(self::$ch, CURLOPT_SSL_VERIFYPEER, 2);
        curl_setopt(self::$ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(self::$ch, CURLOPT_URL, Config::get("FASTSPRING_BASICAUTH_ENDPOINT") . $endpoint);
        curl_setopt(self::$ch, CURLOPT_CUSTOMREQUEST, $method);
	}
	private static function destroy() {
		if (curl_errno(self::$ch)) {
			LoggingModel::logInternal("FastSpringModel Error " . curl_error(self::$ch));
	    } else {
	        curl_close(self::$ch);
	    }
	}
	private static function send() {
		return curl_exec(self::$ch);
	}

	/*
	 *
	 *	Public Shorthand Methods - e.g. ::get, ::post, ::delete
	 *
	 */
	public static function get($route) {
		self::init($route, "GET");
		$result = self::send();
		self::destroy();
        return json_decode($result);
	}

	public static function delete($route) {
		self::init($route, "DELETE");
		$result = self::send();
		self::destroy();
        return json_decode($result);
	}

	public static function post($route, $json) {
		self::init($route, "POST");
        curl_setopt(self::$ch, CURLOPT_POST, 1);
        curl_setopt(self::$ch, CURLOPT_POSTFIELDS, $json);
		$result = self::send();
		self::destroy();
        return json_decode($result);
	}


	// ---------------------------------------------

	public static function applyCoupon($code, $subId) {
		$sub = self::get('/subscriptions/'.$subId);
		// Check if subscription exists
		if (isset($sub->error)) {
			return array('status'=>'Error, subscription does not exist.');
		}
		// Check if subscription already has discount applied
		if (isset($sub->discounts)) {
			return array('status'=>'Error, subscription already has discount applied.');
		} 
		// Check if coupon is valid
		if (isset(self::get('/coupons/'.$code)->error)) {
			return array('status'=>'Error, invalid coupon code.');
		}
		// Apply coupon
		$apply = self::post('/subscriptions', "{'subscriptions': [
                {
                    'subscription': $subId,
                    'coupons': [$code]
                }
            ]
        }"
    	);
		return array('status'=>'Coupon code successfully applied. Your next charge will be discounted.');
	}

}