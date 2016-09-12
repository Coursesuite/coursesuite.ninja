<?php

/**
 * UserModel
 * Handles all the PUBLIC profile stuff. This is not for getting data of the logged in user, it's more for handling
 * data of all the other users. Useful for display profile information, creating user lists etc.
 */
class UserModel
{
    /**
     * Gets an array that contains all the users in the database. The array's keys are the user ids.
     * Each array element is an object, containing a specific user's data.
     * The avatar line is built using Ternary Operators, have a look here for more:
     * @see http://davidwalsh.name/php-shorthand-if-else-ternary-operators
     *
     * @return array The profiles of all users
     */
    public static function getPublicProfilesOfAllUsers($search = null, $mostRecent = true)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT user_id, user_account_type, user_name, user_email, user_active, user_has_avatar, user_deleted, user_logon_count, user_logon_cap, DATE_FORMAT(FROM_UNIXTIME(`user_last_login_timestamp`), '%e %b %Y') AS 'user_last_login_timestamp' FROM users";
        $params = array();
        if ($search != null) {
            $sql .= " WHERE (user_name like :search OR user_email like :search)";
            $params = array(
                ":search" => "%$search%",
            );
        }
        if ($mostRecent) {
            $sql .= " ORDER BY user_creation_timestamp DESC LIMIT 25";
        }
        $query = $database->prepare($sql);
        $query->execute($params);

        $all_users_profiles = array();

        foreach ($query->fetchAll() as $user) {

            // all elements of array passed to Filter::XSSFilter for XSS sanitation, have a look into
            // application/core/Filter.php for more info on how to use. Removes (possibly bad) JavaScript etc from
            // the user's values
            array_walk_recursive($user, 'Filter::XSSFilter');

            $all_users_profiles[$user->user_id] = new stdClass();
            $all_users_profiles[$user->user_id]->user_id = $user->user_id;
            $all_users_profiles[$user->user_id]->user_account_type = $user->user_account_type;
            $all_users_profiles[$user->user_id]->user_name = $user->user_name;
            $all_users_profiles[$user->user_id]->user_email = $user->user_email;
            $all_users_profiles[$user->user_id]->user_active = $user->user_active;
            $all_users_profiles[$user->user_id]->user_deleted = $user->user_deleted;
            $all_users_profiles[$user->user_id]->logon_cap = $user->user_logon_cap;
            $all_users_profiles[$user->user_id]->logon_count = $user->user_logon_count;
            $all_users_profiles[$user->user_id]->last_login = $user->user_last_login_timestamp;
            $all_users_profiles[$user->user_id]->user_avatar_link = (Config::get('USE_GRAVATAR') ? AvatarModel::getGravatarLinkByEmail($user->user_email) : AvatarModel::getPublicAvatarFilePathOfUser($user->user_has_avatar, $user->user_id));
        }



        return $all_users_profiles;
    }

    public static function getAllUsers()
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT user_id, user_name FROM users ORDER BY user_name";
        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    /**
     * Gets a user's profile data, according to the given $user_id
     * @param int $user_id The user's id
     * @return mixed The selected user's profile
     */
    public static function getPublicProfileOfUser($user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        if (intval($user_id) == 0) {
            return false;
        }

        $sql = "SELECT user_id, user_name, user_email, user_active, user_account_type, user_has_avatar,  DATE_FORMAT(FROM_UNIXTIME(`user_creation_timestamp`) , '%e %b %Y %T') AS 'user_creation_timestamp', DATE_FORMAT(FROM_UNIXTIME(`user_last_login_timestamp`), '%e %b %Y %T') AS 'user_last_login_timestamp', user_newsletter_subscribed, user_free_trial_available, user_deleted
                FROM users WHERE user_id = :user_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => $user_id));

        $user = $query->fetch();

        if ($query->rowCount() == 1) {
            if (Config::get('USE_GRAVATAR')) {
                $user->user_avatar_link = AvatarModel::getGravatarLinkByEmail($user->user_email);
            } else {
                $user->user_avatar_link = AvatarModel::getPublicAvatarFilePathOfUser($user->user_has_avatar, $user->user_id);
            }
        } else {
            Session::add('feedback_negative', Text::get('FEEDBACK_USER_DOES_NOT_EXIST'));
        }
        $user->subscription = SubscriptionModel::getCurrentSubscription($user_id);

        // all elements of array passed to Filter::XSSFilter for XSS sanitation, have a look into
        // application/core/Filter.php for more info on how to use. Removes (possibly bad) JavaScript etc from
        // the user's values
        array_walk_recursive($user, 'Filter::XSSFilter');

        return $user;
    }

    public static function getActiveUser($user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT user_id, user_name, user_email
                FROM users
                WHERE user_id = :user_id AND user_active = 1 AND user_deleted = 0 AND user_suspension_timestamp IS NULL
                LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(":user_id" => $user_id));
        return $query->fetch();
    }

    /**
     * @param $user_name_or_email
     *
     * @return mixed
     */
    public static function getUserDataByUserNameOrEmail($user_name_or_email, $applyLimit = true, $likeMatching = false)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $matcher = "=";
        if ($likeMatching == true) {
            $matcher = "LIKE";
            $user_name_or_email = "%" . $user_name_or_email . "%";
        }
        $sql = "SELECT user_id, user_name, user_email
                FROM users
                WHERE (user_name $matcher :user_name_or_email OR user_email $matcher :user_name_or_email)
                AND user_provider_type = :provider_type";
        if ($applyLimit) {
            $sql .= " LIMIT 1";
        }

        $query = $database->prepare($sql);
        $query->execute(array(
            ':user_name_or_email' => $user_name_or_email,
            ':provider_type' => 'DEFAULT',
        ));
        if ($applyLimit) {
            return $query->fetch();
        } else {
            return $query->fetchAll();
        }
    }

    /**
     * Checks if a username is already taken
     *
     * @param $user_name string username
     *
     * @return bool
     */
    public static function doesUsernameAlreadyExist($user_name)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("SELECT user_id FROM users WHERE user_name = :user_name LIMIT 1");
        $query->execute(array(':user_name' => $user_name));
        if ($query->rowCount() == 0) {
            return false;
        }
        return true;
    }

    /**
     * Checks if a email is already used
     *
     * @param $user_email string email
     *
     * @return bool
     */
    public static function doesEmailAlreadyExist($user_email)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("SELECT user_id FROM users WHERE user_email = :user_email LIMIT 1");
        $query->execute(array(':user_email' => $user_email));
        if ($query->rowCount() == 0) {
            return false;
        }
        return true;
    }

    /**
     * Writes new username to database
     *
     * @param $user_id int user id
     * @param $new_user_name string new username
     *
     * @return bool
     */
    public static function saveNewUserName($user_id, $new_user_name)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE users SET user_name = :user_name WHERE user_id = :user_id LIMIT 1");
        $query->execute(array(':user_name' => $new_user_name, ':user_id' => $user_id));
        if ($query->rowCount() == 1) {
            return true;
        }
        return false;
    }

    /**
     * Writes new email address to database
     *
     * @param $user_id int user id
     * @param $new_user_email string new email address
     *
     * @return bool
     */
    public static function saveNewEmailAddress($user_id, $new_user_email)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE users SET user_email = :user_email WHERE user_id = :user_id LIMIT 1");
        $query->execute(array(':user_email' => $new_user_email, ':user_id' => $user_id));
        $count = $query->rowCount();
        if ($count == 1) {
            return true;
        }
        return false;
    }

    // update the activation hash value in the db (e.g. re-send activation email)
    public static function saveUserActivationHash($user_id, $activation_hash)
    {

        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE users SET user_activation_hash = :activation_hash WHERE user_id = :user_id LIMIT 1");
        $query->execute(array(':activation_hash' => $activation_hash, ':user_id' => $user_id));
        $count = $query->rowCount();
        if ($count == 1) {
            // Session::add('feedback_positive', Text::get('FEEDBACK_VERIFICATION_MAIL_SENDING_SUCCESSFUL'));
            return true;
        }
        return false;

    }

    /**
     * Edit the user's name, provided in the editing form
     *
     * @param $new_user_name string The new username
     *
     * @return bool success status
     */
    public static function editUserName($new_user_name)
    {
        // new username same as old one ?
        if ($new_user_name == Session::get('user_name')) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_SAME_AS_OLD_ONE'));
            return false;
        }

        // username cannot be empty and must be azAZ09 and 2-64 characters
        if (!preg_match("/^[a-zA-Z0-9]{2,64}$/", $new_user_name)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_DOES_NOT_FIT_PATTERN'));
            return false;
        }

        // clean the input, strip usernames longer than 64 chars (maybe fix this ?)
        $new_user_name = substr(strip_tags($new_user_name), 0, 64);

        // check if new username already exists
        if (self::doesUsernameAlreadyExist($new_user_name)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_ALREADY_TAKEN'));
            return false;
        }

        $status_of_action = self::saveNewUserName(Session::get('user_id'), $new_user_name);
        if ($status_of_action) {
            Session::set('user_name', $new_user_name);
            Session::add('feedback_positive', Text::get('FEEDBACK_USERNAME_CHANGE_SUCCESSFUL'));
            return true;
        } else {
            Session::add('feedback_negative', Text::get('FEEDBACK_UNKNOWN_ERROR'));
            return false;
        }
    }

    /**
     * Edit the user's email
     *
     * @param $new_user_email
     *
     * @return bool success status
     */
    public static function editUserEmail($new_user_email)
    {
        // email provided ?
        if (empty($new_user_email)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_FIELD_EMPTY'));
            return false;
        }

        // check if new email is same like the old one
        if ($new_user_email == Session::get('user_email')) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_SAME_AS_OLD_ONE'));
            return false;
        }

        // user's email must be in valid email format, also checks the length
        // @see http://stackoverflow.com/questions/21631366/php-filter-validate-email-max-length
        // @see http://stackoverflow.com/questions/386294/what-is-the-maximum-length-of-a-valid-email-address
        if (!filter_var($new_user_email, FILTER_VALIDATE_EMAIL)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_DOES_NOT_FIT_PATTERN'));
            return false;
        }

        // strip tags, just to be sure
        $new_user_email = substr(strip_tags($new_user_email), 0, 254);

        // check if user's email already exists
        if (self::doesEmailAlreadyExist($new_user_email)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USER_EMAIL_ALREADY_TAKEN'));
            return false;
        }

        // write to database, if successful ...
        // ... then write new email to session, Gravatar too (as this relies to the user's email address)
        if (self::saveNewEmailAddress(Session::get('user_id'), $new_user_email)) {
            Session::set('user_email', $new_user_email);
            Session::set('user_gravatar_image_url', AvatarModel::getGravatarLinkByEmail($new_user_email));
            Session::add('feedback_positive', Text::get('FEEDBACK_EMAIL_CHANGE_SUCCESSFUL'));
            return true;
        }

        Session::add('feedback_negative', Text::get('FEEDBACK_UNKNOWN_ERROR'));
        return false;
    }

    /**
     * Gets the user's id
     *
     * @param $user_name
     *
     * @return mixed
     */
    public static function getUserIdByUsername($user_name)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT user_id FROM users WHERE user_name = :user_name AND user_provider_type = :provider_type LIMIT 1";
        $query = $database->prepare($sql);

        // DEFAULT is the marker for "normal" accounts (that have a password etc.)
        // There are other types of accounts that don't have passwords etc. (FACEBOOK)
        $query->execute(array(':user_name' => $user_name, ':provider_type' => 'DEFAULT'));

        // return one row (we only have one result or nothing)
        return $query->fetch()->user_id;
    }

    /**
     * Gets the user's data
     *
     * @param $user_name string User's name
     *
     * @return mixed Returns false if user does not exist, returns object with user's data when user exists
     */
    public static function getUserDataByUsername($user_name)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT user_id, user_name, user_email, user_password_hash, user_active,user_deleted, user_suspension_timestamp, user_account_type,
                       user_failed_logins, user_last_failed_login, user_newsletter_subscribed
                  FROM users
                 WHERE (user_name = :user_name OR user_email = :user_name)
                       AND user_provider_type = :provider_type
                 LIMIT 1";
        $query = $database->prepare($sql);

        // DEFAULT is the marker for "normal" accounts (that have a password etc.)
        // There are other types of accounts that don't have passwords etc. (FACEBOOK)
        $query->execute(array(':user_name' => $user_name, ':provider_type' => 'DEFAULT'));

        // return one row (we only have one result or nothing)
        return $query->fetch();
    }

    /**
     * Gets the user's data by user's id and a token (used by login-via-cookie process)
     *
     * @param $user_id
     * @param $token
     *
     * @return mixed Returns false if user does not exist, returns object with user's data when user exists
     */
    public static function getUserDataByUserIdAndToken($user_id, $token)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        // get real token from database (and all other data)
        $query = $database->prepare("SELECT user_id, user_name, user_email, user_password_hash, user_active,
                                          user_account_type,  user_has_avatar, user_failed_logins, user_last_failed_login
                                     FROM users
                                     WHERE user_id = :user_id
                                       AND user_remember_me_token = :user_remember_me_token
                                       AND user_remember_me_token IS NOT NULL
                                       AND user_provider_type = :provider_type LIMIT 1");
        $query->execute(array(':user_id' => $user_id, ':user_remember_me_token' => $token, ':provider_type' => 'DEFAULT'));

        // return one row (we only have one result or nothing)
        return $query->fetch();
    }

    public static function destroyUserForever($user_id, $base_type_only = true)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $force = ($base_type_only) ? " AND user_account_type=1 " : "";
        $query = $database->prepare("DELETE FROM users WHERE user_id=:user_id $force LIMIT 1");
        $query->execute(array(":user_id" => $user_id));
        return ($query->rowCount() == 1);
    }

    /**
     * Check to see if a user record has reached its alloted usage cap
     *
     * @param $user_id - (int) user that you want to check
     *
     * @return boolean - truthy if user is still able to log in
     *
     */
    public static function checkLogonCap($user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("SELECT COUNT(1) FROM users
                                                        WHERE user_id = :userid
                                                        AND (user_logon_count <= user_logon_cap OR user_logon_cap = -1)
                                                        LIMIT 1");
        $query->execute(array(
            ":userid" => $user_id,
        ));
        $result = (bool) $query->fetchColumn(0);
        return $result;
    }

    /**
     * Increment a user logon count
     *
     * @param $user_id - (int) user that you want to increment
     *
     */
    public static function incrementLoginCounter($user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("UPDATE users
                                                        SET user_logon_count = (user_logon_count + 1)
                                                        WHERE user_id = :userid
                                                        LIMIT 1");
        $query->execute(array(
            ":userid" => $user_id,
        ));
    }

    /**
     * Returns the users account type. 1 - Standard, 3 - Trial, 7 - Admin
     *
     * @param $user_id - (int) users id
     *
     * @return int - users account type
     *
     */
    public static function getUserAccountType($user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("SELECT user_account_type FROM users WHERE user_id = :user_id");
        $query->execute(array(":user_id" => $user_id));
        $result = $query->fetch();
        return $result->user_account_type;
    }

    // system task to check all trial users and remove their subscription if they have expired
    public static function trialUserExpire()
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->query("select count(id) from systasks where running=0 and task='trialUserExpire' and lastrun < timestamp(date_add(now(), INTERVAL -1 DAY))");
        if ($query->fetchColumn() == '1') {
            $database->query("update systasks set running=1 where task='trialUserExpire'");
            UserModel::updateTrialUsers();
            $database->query("update systasks set running=0, lastrun=timestamp(now()) where task='trialUserExpire'");
        }
    }

    public static function updateTrialUsers()
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT subscription_id, user_id, endDate FROM subscriptions WHERE user_account_type = 3";
        $query = $database->prepare($sql);
        $query->execute();
        $trialUsers = $query->fetchAll();
        foreach ($trialUsers as $users) {
            if (date_diff(date('Y-m-d'), $users->endDate) < 0) {
                SubscriptionModel::removeSubscriptionFromId($users->subscription_id);
                UserRoleModel::changeRoleById($users->user_id, 1); //change state to something other than 1 maybe?
            }
        }
    }

    /**
     * Returns whether the user can get a free trial
     *
     * @param $user_id - the user to check
     *
     * @return bool - truthy if user can get a free trial
     *
     */ 

    public static function getTrialAvailability($user_id)
    {
        if (!is_int($user_id)) {return false;}
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("SELECT user_free_trial_available FROM users WHERE user_id = :user_id LIMIT 1");
        $query->execute(array(":user_id" => $user_id));
        $result = $query->fetch();
        return $result->user_free_trial_available == 1 ? true : false;
    }

    /**
     * Stops the user from activating a free trial.
     *
     * @param $user_id - (int) The users id
     *
     */

    public static function setTrialUnavailable($user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("UPDATE users SET user_free_trial_available = 0 WHERE user_id = :user_id");
        $query->execute(array(":user_id" => $user_id));
    }
}
