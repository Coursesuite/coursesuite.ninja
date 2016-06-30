<?php

/*
 * Class to handle everything related to mail chimp
 */

class MailChimp
{
	/**
	*Subscribes the user to the mailchimp newsletter
	*
	*@param $user_email string
	*@param $user_name string
	*
	*/
	public static function subscribe($user_email, $user_name){
		$apiKey = config::get('MAILCHIMP_API_KEY');
		$memberId = md5($user_email);
		$dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
		$url = $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . config::get('MAILCHIMP_LIST_ID') . '/members/' . $memberId;

		$json = json_encode([
			'email_address'=>$user_email,
			'status'=>'subscribed',
			'merge_fields'=>[
					'FNAME'=>$user_name,
					'LNAME'=>'.'
				]
			]);

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);   

		return curl_exec($ch);
	}

	public static function unsubscribe($user_email){
		$apiKey = config::get('MAILCHIMP_API_KEY');
		$memberId = md5($user_email);
		$dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
		$url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . config::get('MAILCHIMP_LIST_ID') . '/members/' . $memberId;

		$json = json_encode([
				'status' => 'unsubscribed'
			]);	

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);  

	    return curl_exec($ch);
	}

	/**
	 *Checks if specified user is subscribed to the mailing list, returns true if they are
	 *
	 *@param $user_email string 
	 *
	 *@return bool
	 */

	public static function isUserSubscribed($user_email){
		$apiKey = config::get('MAILCHIMP_API_KEY');
		$dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
		$memberId = md5($user_email);
		$url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . config::get('MAILCHIMP_LIST_ID') . '/members/' . $memberId;

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$result = json_decode(curl_exec($ch));
		// Check if user is subbed to the list or not
		if ($result->status == 'subscribed') {
			return true;
		}
		if ($result->status == 'unsubscribed') {
			return false;
		}
		curl_close($ch);
		// other options are cleaned and pending, will add result for pending soon(tm)
		return false;
		
	}

	/**
	 * Returns a 2d array of the different categories and their ID's for the mailing list
	 *
	 *@return array
	 */

	public static function getListInterests(){
		$apiKey = config::get('MAILCHIMP_API_KEY');
		$dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
		$url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . config::get('MAILCHIMP_LIST_ID') . '/interest-categories/' . config::get('MAILCHIMP_INTEREST_ID') . '/interests';

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

	    $result = json_decode(curl_exec($ch));
	    $interestNames = array();

	    foreach ($result->interests as $interests){
	    	array_push($interestNames, array($interests->name, $interests->id));
	    }

	    return $interestNames;
		
	}

	/**
	 * Same as getListInterests but also tells you which interests catagories the user is subbed to
	 *
	 *@param user_email string
	 *
	 *@return array 
	 */

	public static function getUserInterests($user_email){
		$listInterests = MailChimp::getListInterests();

		$apiKey = Config::get('MAILCHIMP_API_KEY');
		$dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
		$memberId = md5($user_email);
		$url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . config::get('MAILCHIMP_LIST_ID') . '/members/' . $memberId;
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		
		$result = json_decode(curl_exec($ch))->interests;
		$final = array();
		foreach ($listInterests as $list) {
			$list[] = $result->$list[1];
			$final[] = $list;
		}

	    return $final;
	}

	/**
	 *Updates a variety of user info
	 *
	 *
	 */
	public static function updateUserInfo($user_email, $user_name = NULL, $user_interests = NULL, $subscribed = 'subscribed'){
		$apiKey = config::get('MAILCHIMP_API_KEY');
		$dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
		$memberId = md5($user_email);
		$url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . config::get('MAILCHIMP_LIST_ID') . '/members/' . $memberId;	
		$json = array();

		$json['status'] = $subscribed; //options are: subscribed, unsubscribed, cleaned, pending
		$json['merge_fields'] = array();
		if ($user_name){$json['merge_fields']['FNAME'] = $user_name;}
		if ($user_interests){$json['interests'] = $user_interests;}

		$json = json_encode($json);
		print_r($json);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);  
	    
		return curl_exec($ch);		
	}
	 

	//Just for testing 
	public static function mailchimptester(){
		$apiKey = config::get('MAILCHIMP_API_KEY');
		$dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
		$memberId = md5('ben@coursesuite.com.au');
		//specific interest
		$url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . config::get('MAILCHIMP_LIST_ID') . '/interest-categories/' . config::get('MAILCHIMP_INTEREST_ID') . '/interests';
		//$url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . config::get('MAILCHIMP_LIST_ID') . '/members/' . $memberId;

		$json = json_encode([
			'email_address'=>'ben@coursesuite.com.au',
			'status'=>'subscribed',
			'merge_fields'=>[
					'FNAME'=>'ben',
					'LNAME'=>'.'
				]
			]);

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
	    //curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    //curl_setopt($ch, CURLOPT_POSTFIELDS, $json);   

		$result = json_decode(curl_exec($ch));
		//print_r($result->interests[0]);
		foreach ($result->interests as $interests){
			echo($interests->name);
		}
    	curl_close($ch);
	}

}