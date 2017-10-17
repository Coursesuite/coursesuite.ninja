<?php

	/* ------------------------------------------------------------------------------------------------------------------------------------------------------------

	STORE PAGE REGISTRATION AND ACCOUNT VALIDATION

	------------------------------------------------------------------------------------------------------------------------------------------------------------ */

class RegistrationModel
{

	// create or update a user account and set the password; email the user that password
	public static function send_one_time_password($email, $app_key)
	{
		$database = DatabaseFactory::getFactory()->getConnection();

		$model = Model::Read("users", "user_email=:email", array(":email" => $email));
		if (empty($model)) { // user with this email address was not found
			$model = (object) Model::Create("users");
			$model->user_email = $email;
			$model->user_active = 1;
			$model->user_creation_timestamp = time();
		} else {
			$model = $model{0};
		}

		$password = (new Sayable(6))->generate();
		$model->user_password_hash = password_hash($password, PASSWORD_DEFAULT);

		$model->last_browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
		$model->last_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";

		$user_id = Model::Update("users", "user_id", $model);

		if (empty($app_key)) {
			$linkUrl = Config::get('URL') . "login/";
		} else {
			$linkUrl = Config::get('URL') . "store/info/{$app_key}/";
		}

		$mail_sent = (new Mail)->sendOneTimePassword($email, $password, $linkUrl);
		return $mail_sent;

	}

}
