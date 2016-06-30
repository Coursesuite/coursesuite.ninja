<?php

class Curl
{
	/**
	 * Sets all the options for the mailchimp curl and retuns the result
	 * Returns true or false if returnTransfer is false, or returns json data from the request if returnTransfer is true
	 *
	 * @param url string
	 * @param apiKey string
	 * @param requestType string
	 * @param returnTransfer bool
	 * @param json JSONstring
	 *
	 * @return bool/JSONstring
	 */
	public static function mailChimpCurl($url, $apiKey, $requestType, $returnTransfer, $json = NULL){
		ob_start(); //Just dont look at the errors it'll be fine
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, $returnTransfer);
	    if($json){curl_setopt($ch, CURLOPT_POSTFIELDS, $json);}  
		ob_clean();
	    return curl_exec($ch);
	}
}