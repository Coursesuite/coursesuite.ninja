<?php

class HelpdeskModel {

	public static function get_my_tickets($email) {
		$data = array('email' => $email);
		$helpdesk_apikey = Config::get("OST_APIKEY");
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL => "https://help.coursesuite.ninja/api/tickets.json",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => ['Expect:', 'X-API-Key: ' . $helpdesk_apikey],
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_POSTFIELDS => json_encode($data)
		));
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result);
	}

	public static function create_ticket($email, $fullname, $phone, $subject, $message) {
		$data = array(
			'name' => $fullname,
			'email' => $email,
			'phone' => $phone,
			'subject' => $subject,
			'message' => $message,
			'ip' => exec('curl http://ipecho.net/plain; echo'), // needs to be the IP of the web server, not the client IP
			'topicId' => '1', // general inquiry
			'attachments' => array()
		);
		$helpdesk_apikey = Config::get("OST_APIKEY");
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL => "https://help.coursesuite.ninja/api/tickets.json",
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_USERAGENT => 'osTicket API Client v1.8',
			CURLOPT_HEADER => false,
			CURLOPT_HTTPHEADER => ['Expect:', 'X-API-Key: ' . $helpdesk_apikey],
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_RETURNTRANSFER => true,
		));
		$result = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($code != 201) {
			die("Unable to create ticket: " . $result); // todo, warn user somehow
		}

		return (int) $result;
	}
}

