<?php

	/* ------------------------------------------------------------------------------------------------------------------------------------------------------------

	STORE PAGE REGISTRATION AND ACCOUNT VALIDATION

	------------------------------------------------------------------------------------------------------------------------------------------------------------ */

class RegistrationModel
{

	// is an account ready to be used?
	public static function user_account_already_exists_and_is_usable($email)
	{
		$database = DatabaseFactory::getFactory()->getConnection();
		$result = $database->prepare("SELECT COUNT(1)
			FROM users
			WHERE user_email=:email
			AND user_deleted = 0
			AND user_activation_hash IS NULL
			AND user_suspension_timestamp IS NULL");
		$result->execute(array(":email" => $email));
		return ($result->fetchColumn() > 0);
	}

	// returns TRUE if a password reset hash was sent
	public static function send_password_reset($email, $app_key = "")
	{
		$database = DatabaseFactory::getFactory()->getConnection();

		$result = $database->prepare("UPDATE users
			SET user_password_reset_hash = :hash,
			user_password_reset_timestamp = :ts
			WHERE user_email = :email
			LIMIT 1");
		$hash = sha1(uniqid(mt_rand(), true));
		$result->execute(array(":email" => $email, ":hash" => $hash, ":ts" => time()));

		// there's two actions - one from /login/ and one from /store/ since it's all about the destination
		if (empty($app_key)) {
			$linkUrl = Config::get('URL') . "email/confirmResetPassword/" . $hash . "/";
		} else {
			$linkUrl = Config::get('URL') . "store/info/$app_key/reset/" . $hash . "/";
		}

		$mail_sent = (new Mail)->sendPasswordReset($email, $linkUrl);
		return $mail_sent;
	}

	// if a user activation has matches what we thought it was
	// set the user record to active and reset any hashes on the account then send them a new password
	public static function validate_password_reset_hash_and_genetate_new_password_and_email_it($hash, $app_key, &$email = "")
	{
		$database = DatabaseFactory::getFactory()->getConnection();

		// validate the hash and find the user it matched to
		$query = $database->prepare("SELECT user_email
			FROM users
			WHERE user_password_reset_hash=:hash
			LIMIT 1");
		$query->execute(array(":hash" => $hash));
		if (($email = $query->fetchColumn()) > "") {

			// reset the various fields, including password
			$password = Text::generatePassword();
			$password_hash = password_hash($password, PASSWORD_DEFAULT);
			$query = $database->prepare("UPDATE users
				SET user_active = 1, user_activation_hash = NULL, user_password_reset_hash = NULL, user_password_reset_timestamp = NULL, user_password_hash = :password
				WHERE user_password_reset_hash=:hash
				LIMIT 1");
			$query->execute(array(":hash" => $hash, ":password" => $password_hash));

			if (empty($app_key)) {
				$linkTo = Config::get('URL') . "login/";
			} else {
				$linkTo = Config::get('URL') . "store/info/$app_key/";
			}
			$mail_sent = (new Mail)->sendPassword($email, $password, $linkTo);
			return $email;
		} else {
			return false;
		}
	}

	// return TRUE if a new account was registered and details sent
	// this only gets called after we determined that the email address does not yet exist
	public static function register_new_account_and_send_verification($email, $app_key = "")
	{
		$hash = sha1(uniqid(mt_rand(), true));
		$addr = explode('@', $email);
		$password = Text::generatePassword();
		$password_hash = password_hash($password, PASSWORD_DEFAULT);

		$account = (new AccountModel)->make();
		$model = $account->get_model();
		$model["user_email"] = $email;
		$model["user_activation_hash"] = $hash;
		$model["user_password_hash"] = $password_hash;
		$model["user_active"] = 0;
		$model["user_account_type"] = 1;
		$model["user_deleted"] = 0;
		$model["user_creation_timestamp"] = time();
		$account->set_model($model);
		$account->save();

		if (empty($app_key)) {
			$linkTo = Config::get('URL') . "email/validate/$hash/";
		} else {
			$linkTo = Config::get('URL') . "store/info/$app_key/validate/$hash/";
		}

		$mail_sent = (new Mail)->sendVerificationAndPassword($email, $password, $linkTo);

		return true;

	}

	// will probably need this eventually; would be called by cron
	public static function cleanup_unverified_new_accounts_after_a_while()
	{
		// find accounts with a user_activation_hash that has a user_last_login_timestamp of null
		// and a user_creation_timestamp > strtotime("INTERVAL 7 DAY") or however you do it
		// set a cron running task

	}

	public static function resend_the_existing_account_activation_hash($email, $app_key)
	{
		$database = DatabaseFactory::getFactory()->getConnection();
		$query->prepare("SELECT user_activation_hash
			FROM users
			WHERE user_email = :email
			AND user_activation_hash IS NOT NULL
			LIMIT 1");
		$query->execute(array(":email" => $email));
		if ($hash = $query->fetchColumn()) {
			if (empty($app_key)) {
				$linkTo = Config::get('URL') . "email/validate/$hash/";
			} else {
				$linkTo = Config::get('URL') . "store/info/$app_key/validate/$hash/";
			}
			$mail_sent = (new Mail)->resendVerification($email, $linkTo);
			return $mail_sent;
		}

	}

	// validate an account based on its verification hash; return true if it was updated
	public static function validate_user_activation_hash_and_reset_condition($user_verification_hash, &$email = "")
	{
		$account = UserModel::getAccountByHash($user_verification_hash, "activation");
		if (!is_null($account)) {
			$model = $account->get_model();
			$email = $model["user_email"];
			$model["user_active"] = 1;
			$model["user_activation_hash"] = null;
			$account->set_model($model);
			$account->save();

    			$mc = new MailChimp($email);
    			$mc->subscribe();

			if (!Session::userIsLoggedIn()) {
				LoginModel::loginWithAccount($account);
			}
			return true;
		}
		return false;
	}
}
