<?php

class Discourse
{
	// go to /users/admin-login to logon to discourse without SSO

	public static function login($userId, $userEmail, $userName, $payload, $signature){
		$sso = new Cviebrock\DiscoursePHP\SSOHelper();
		$sso->setSecret(Config::get('DISCOURSE_SSO_SECRET'));
		// Validate payload
		if (!($sso->validatePayload($payload,$signature))) {
		    header("HTTP/1.1 403 Forbidden");
		    echo("Bad SSO request");
		    die();
		}
		$nonce = $sso->getNonce($payload);

		$extraParameteres = array(
			'username' => $userName,
			'name' => $userName
		);

		$query = $sso->getSignInString($nonce, $userId, $userEmail, $extraParameteres);
		header('Location: http://forum.coursesuite.ninja/session/sso_login?' . $query);
		exit(0);
	}
}