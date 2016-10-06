<?php

/**
 * Class RegistrationModel
 *
 * Everything registration-related happens here.
 */
class RegistrationModel
{
    /**
     * Handles the entire registration process for DEFAULT users (not for people who register with
     * 3rd party services, like facebook) and creates a new user in the database if everything is fine
     *
     * @return boolean Gives back the success status of the registration
     */
    public static function registerNewUser()
    {

        Session::set("feedback_area", "registration");

        // clean the input
        $user_name = strip_tags(Request::post('user_name'));
        $user_email = strip_tags(Request::post('user_email'));
        $user_email_repeat = strip_tags(Request::post('user_email_repeat'));
        $user_password_new = Request::post('user_password_new');
        $user_password_repeat = Request::post('user_password_repeat');
        $user_newsletter_subscribed = Request::post('user_newsletter_subscribed');

        // form data persistence
        Session::set('form_data', array(
            'user_name' => $user_name,
            'user_password_new' => $user_password_new,
            'user_password_repeat' => $user_password_repeat,
            'user_email' => $user_email,
            'user_email_repeat' => $user_email_repeat,
        ));

        // stop registration flow if registrationInputValidation() returns false (= anything breaks the input check rules)

        $validation_result = self::registrationInputValidation(Request::post('g-recaptcha-response'), $user_name, $user_password_new, $user_password_repeat, $user_email, $user_email_repeat);
        if (!$validation_result) {
            return false;
        }

        // crypt the password with the PHP 5.5's password_hash() function, results in a 60 character hash string.
        // @see php.net/manual/en/function.password-hash.php for more, especially for potential options
        $user_password_hash = password_hash($user_password_new, PASSWORD_DEFAULT);

        // make return a bool variable, so both errors can come up at once if needed
        $return = true;

        // check if username already exists
        if (UserModel::doesUsernameAlreadyExist($user_name)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_ALREADY_TAKEN'));
            $return = false;
        }

        // check if email already exists
        if (UserModel::doesEmailAlreadyExist($user_email)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USER_EMAIL_ALREADY_TAKEN'));
            $return = false;
        }

        // if Username or Email were false, return false before logic continues
        if (!$return) {
            return false;
        }

        // generate random hash for email verification (40 char string)
        $user_activation_hash = sha1(uniqid(mt_rand(), true));

        // Fix newsletter subscription variable
        $user_newsletter_subscribed = (!$user_newsletter_subscribed) ? 0 : 1;

        // Set account type - normal(1), free trial(3) this will need to be changed with the adition of new account types
        $user_account_type = (Session::get('free_trial')) ? 3 : 1;
        Session::remove('free_trial');

        // write user data to database
        if (!self::writeNewUserToDatabase($user_name, $user_password_hash, $user_email, time(), $user_activation_hash, $user_newsletter_subscribed, $user_account_type)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_CREATION_FAILED'));
            return false; // no reason not to return false here
        }

        // get user_id of the user that has been created, to keep things clean we DON'T use lastInsertId() here
        $user_id = UserModel::getUserIdByUsername($user_name);

        if (!$user_id) {
            Session::add('feedback_negative', Text::get('FEEDBACK_UNKNOWN_ERROR'));
            return false;
        }

        // Give trial users their subscription and remove their ability to get another free trial
        if ($user_account_type == 3) {
            SubscriptionModel::giveFreeSubscription($user_id, TierModel::getTierIdByName(Config::get('FREE_TRIAL_TIER')));
            UserModel::setTrialUnavailable($user_id);
        }

        // send verification email
        if (self::sendVerificationEmail($user_id, $user_email, $user_activation_hash, $user_newsletter_subscribed, $user_account_type)) {
            Session::add('feedback_positive', Text::get('FEEDBACK_ACCOUNT_SUCCESSFULLY_CREATED'));
            return true;
        }

        // if verification email sending failed: instantly delete the user
        self::rollbackRegistrationByUserId($user_id);
        Session::add('feedback_negative', Text::get('FEEDBACK_VERIFICATION_MAIL_SENDING_FAILED'));
        return false;
    }

    /**
     * Validates the registration input
     *
     * @param $captcha
     * @param $user_name
     * @param $user_password_new
     * @param $user_password_repeat
     * @param $user_email
     * @param $user_email_repeat
     *
     * @return bool
     */
    public static function registrationInputValidation($captcha, $user_name, $user_password_new, $user_password_repeat, $user_email, $user_email_repeat)
    {
        $return = true;

        // perform all necessary checks
        if (!($captcha_check = CaptchaModel::checkCaptcha($captcha)) === true) {
            Session::add('feedback_negative', Text::get($captcha_check)); // text error codes
            // Session::add('feedback_negative', Text::get('FEEDBACK_CAPTCHA_WRONG'));
            $return = false;
        }

        // if username, email and password are all correctly validated, but make sure they all run on first sumbit
        if (self::validateUserName($user_name) and self::validateUserEmail($user_email, $user_email_repeat) and self::validateUserPassword($user_password_new, $user_password_repeat) and $return) {
            return true;
        }

        // otherwise, return false
        return false;
    }

    /**
     * Validates the username
     *
     * @param $user_name
     * @return bool
     */
    public static function validateUserName($user_name)
    {
        if (empty($user_name)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_FIELD_EMPTY'));
            return false;
        }

        // if username is too short (2), too long (64) or does not fit the pattern (aZ09)
        if (!preg_match('/^[a-zA-Z0-9]{2,64}$/', $user_name)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_DOES_NOT_FIT_PATTERN'));
            return false;
        }

        return true;
    }

    /**
     * Validates the email
     *
     * @param $user_email
     * @param $user_email_repeat
     * @return bool
     */
    public static function validateUserEmail($user_email, $user_email_repeat)
    {
        if (empty($user_email)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_FIELD_EMPTY'));
            return false;
        }

        if ($user_email !== $user_email_repeat) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_REPEAT_WRONG'));
            return false;
        }

        // validate the email with PHP's internal filter
        // side-fact: Max length seems to be 254 chars
        // @see http://stackoverflow.com/questions/386294/what-is-the-maximum-length-of-a-valid-email-address
        if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_DOES_NOT_FIT_PATTERN'));
            return false;
        }

        return true;
    }

    /**
     * Validates the password
     *
     * @param $user_password_new
     * @param $user_password_repeat
     * @return bool
     */
    public static function validateUserPassword($user_password_new, $user_password_repeat)
    {
        if (empty($user_password_new) or empty($user_password_repeat)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_PASSWORD_FIELD_EMPTY'));
            return false;
        }

        if ($user_password_new !== $user_password_repeat) {
            Session::add('feedback_negative', Text::get('FEEDBACK_PASSWORD_REPEAT_WRONG'));
            return false;
        }

        if (strlen($user_password_new) < 6) {
            Session::add('feedback_negative', Text::get('FEEDBACK_PASSWORD_TOO_SHORT'));
            return false;
        }

        return true;
    }

    /**
     * Writes the new user's data to the database
     *
     * @param $user_name
     * @param $user_password_hash
     * @param $user_email
     * @param $user_creation_timestamp
     * @param $user_activation_hash
     *
     * @return bool
     */
    public static function writeNewUserToDatabase($user_name, $user_password_hash, $user_email, $user_creation_timestamp, $user_activation_hash, $user_newsletter_subscribed, $user_account_type = 1)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        // write new users data into database
        $sql = "INSERT INTO users (user_name, user_password_hash, user_email, user_account_type, user_creation_timestamp, user_activation_hash, user_provider_type, user_newsletter_subscribed)
                    VALUES (:user_name, :user_password_hash, :user_email, :user_account_type, :user_creation_timestamp, :user_activation_hash, :user_provider_type, :user_newsletter_subscribed)";
        $query = $database->prepare($sql);
        $query->execute(array(':user_name' => $user_name,
            ':user_password_hash' => $user_password_hash,
            ':user_email' => $user_email,
            ':user_account_type' => $user_account_type,
            ':user_creation_timestamp' => $user_creation_timestamp,
            ':user_activation_hash' => $user_activation_hash,
            ':user_provider_type' => 'DEFAULT',
            ':user_newsletter_subscribed' => $user_newsletter_subscribed));
        $count = $query->rowCount();
        if ($count == 1) {
            return true;
        }

        return false;
    }

    /**
     * Deletes the user from users table. Currently used to rollback a registration when verification mail sending
     * was not successful.
     *
     * @param $user_id
     */
    public static function rollbackRegistrationByUserId($user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("DELETE FROM users WHERE user_id = :user_id");
        $query->execute(array(':user_id' => $user_id));
    }

    /**
     * Sends the verification email (to confirm the account).
     * The construction of the mail $body looks weird at first, but it's really just a simple string.
     *
     * @param int $user_id user's id
     * @param string $user_email user's email
     * @param string $user_activation_hash user's mail verification hash string
     *
     * @return boolean gives back true if mail has been sent, gives back false if no mail could been sent
     */
    public static function sendVerificationEmail($user_id, $user_email, $user_activation_hash, $user_newsletter_subscribed, $user_account_type)
    {
        if ($user_account_type == 3) {
            $body = Text::get('EMAIL_COMMON_CONTENT_INTRO') .
            Text::get('EMAIL_TRIAL_VERIFICATION_CONTENT') . "\n\n" .
            Config::get('URL') . Config::get('EMAIL_VERIFICATION_URL') . '/' . urlencode($user_id) . '/' . urlencode($user_activation_hash) . '/' . urlencode($user_newsletter_subscribed) .
            Text::get('EMAIL_COMMON_CONTENT_SIG');
            $view = new View();
            $template = MailTemplateModel::getLiveTemplate('freeTrial')
            $body = $view->prepareString($template->body);


        } else {
            $body = Text::get('EMAIL_COMMON_CONTENT_INTRO') .
            Text::get('EMAIL_VERIFICATION_CONTENT') . "\n\n" .
            Config::get('URL') . Config::get('EMAIL_VERIFICATION_URL') . '/' . urlencode($user_id) . '/' . urlencode($user_activation_hash) . '/' . urlencode($user_newsletter_subscribed) .
            Text::get('EMAIL_COMMON_CONTENT_SIG');
        }
        $mail = new Mail;
        $mail_sent = $mail->sendMail($user_email, Config::get('EMAIL_VERIFICATION_FROM_EMAIL'),
            Config::get('EMAIL_VERIFICATION_FROM_NAME'), Config::get('EMAIL_VERIFICATION_SUBJECT'), $body
        );

        if ($mail_sent) {
            Session::add('feedback_positive', Text::get('FEEDBACK_VERIFICATION_MAIL_SENDING_SUCCESSFUL'));
            return true;
        } else {
            Session::add('feedback_negative', Text::get('FEEDBACK_VERIFICATION_MAIL_SENDING_ERROR') . $mail->getError());
            return false;
        }
    }

    public static function reSendVerificationEmail() {
        $formData = Session::get('form_data');
        Session::set("feedback_area", "login");
        $userInfo = UserModel::getUserDataByUsername($formData['user_name']);
        $user_activation_hash = sha1(uniqid(mt_rand(), true));
        if (UserModel::saveUserActivationHash($userInfo->user_id, $user_activation_hash)) {
            if (RegistrationModel::sendVerificationEmail($userInfo->user_id, $userInfo->user_email, $user_activation_hash, $userInfo->user_newsletter_subscribed, $userInfo->user_account_type  )) {
                Session::add('feedback_positive', Text::get('FEEDBACK_ACCOUNT_VERIFIFICATION_RESENT'));
            }
        }
    }

    /**
     * checks the email/verification code combination and set the user's activation status to true in the database
     *
     * @param int $user_id user id
     * @param string $user_activation_verification_code verification token
     * @param string $user_newsletter_subscribed
     *
     * @return bool success status
     */
    public static function verifyNewUser($user_id, $user_activation_verification_code, $user_newsletter_subscribed)
    {
        Session::set("feedback_area", "login");
        Session::remove('free_trial');
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE users
                                    SET user_active = 1, user_activation_hash = NULL
                                    WHERE user_id = :user_id AND user_activation_hash = :user_activation_hash
                                    LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(
            ':user_id' => $user_id,
            ':user_activation_hash' => $user_activation_verification_code,
        ));

        // Get user name and email
        $sql = "SELECT user_email, user_name, user_account_type
                                    FROM users
                                    WHERE user_id = :user_id
                                    LIMIT 1";
        $userInfoQuery = $database->prepare($sql);
        $userInfoQuery->execute(array(
            ':user_id' => $user_id,
        ));
        $userInfo = $userInfoQuery->fetchAll(); // PDO::FETCH_ASSOC

        if ($user_newsletter_subscribed == 1) {
            MailChimp::subscribe($userInfo[0]->user_email, $userInfo[0]->user_name);
        }

        if ($userInfo[0]->user_account_type == 3){
            $mail = new Mail;
            $mail->sendMail(Config::get('EMAIL_ADMIN'), Config::get('EMAIL_SUBSCRIPTION'), 'Coursesuite Admin', 'Free trial created', "User " . $user_id . ": " . $userInfo[0]->user_name . ", Just created a free trial account");
        }

        if ($query->rowCount() == 1) {
            Session::add('feedback_positive', Text::get('FEEDBACK_ACCOUNT_ACTIVATION_SUCCESSFUL'));
            return true;
        }

        Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_ACTIVATION_FAILED'));
        return false;
    }
}
