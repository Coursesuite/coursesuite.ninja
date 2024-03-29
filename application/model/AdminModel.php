<?php

/**
 * Handles all data manipulation of the admin part
 */
class AdminModel
{
    /**
     * Sets the deletion and suspension values
     *
     * @param $suspensionInDays
     * @param $softDelete
     * @param $userId
     */
    public static function setAccountSuspensionAndDeletionStatus($userId, $suspensionInDays, $softDelete = "", $hardDelete = "", $makeActive = "", $logonCap = -1)
    {

        // Prevent to suspend or delete own account.
        // If admin suspend or delete own account will not be able to do any action.
        if ($userId == Session::get('user_id')) {
            Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_CANT_DELETE_SUSPEND_OWN'));
            return false;
        }

        if ($suspensionInDays > 0) {
            $suspensionTime = time() + ($suspensionInDays * 60 * 60 * 24);
        } else {
            $suspensionTime = null;
        }

        // FYI "on" is what a checkbox delivers by default when submitted. Didn't know that for a long time :)

        $delete = ($softDelete == "on") ? 1 : 0;
        $destroy = ($hardDelete == "on") ? 1 : 0;
        $activate = ($makeActive == "on") ? 1 : 0;

        // write the above info to the database
        self::writeDeleteAndSuspensionInfoToDatabase($userId, $suspensionTime, $delete, $destroy, $activate, $logonCap);

        // if suspension or deletion should happen, then also kick user out of the application instantly by resetting
        // the user's session :)
        if ($suspensionTime != null or $delete = 1 or $destroy = 1) {
            self::resetUserSession($userId);
        }
    }

    /**
     * Simply write the deletion and suspension info for the user into the database, also puts feedback into session
     *
     * @param $userId
     * @param $suspensionTime
     * @param $delete
     * @return bool
     */
    private static function writeDeleteAndSuspensionInfoToDatabase($userId, $suspensionTime, $delete, $destroy, $activate, $logonCap)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        if ($destroy == 1) {
            $query = $database->prepare("DELETE FROM users WHERE user_id=:uid");
            $query->execute(array(":uid" => $userId));
            Session::add('feedback_positive', Text::get('FEEDBACK_ADMIN_USER_DELETED', array("id" => $userId)));
            return true;
        }

        $params = array(
            ':user_suspension_timestamp' => $suspensionTime,
            ':user_deleted' => $delete,
            ':user_id' => $userId,
            ':logoncap' => $logonCap,
        );

        $ACTIVATED = "";
        if ($activate == 1) {
            $ACTIVATED = ", user_active=1, user_activation_hash=NULL, user_password_reset_hash=NULL ";
        }

        $query = $database->prepare("UPDATE users SET user_suspension_timestamp = :user_suspension_timestamp, user_deleted = :user_deleted, user_logon_cap = :logoncap $ACTIVATED WHERE user_id = :user_id LIMIT 1");
        $query->execute($params);

        if ($query->rowCount() == 1) {
            Session::add('feedback_positive', Text::get('FEEDBACK_ACCOUNT_SUSPENSION_DELETION_STATUS'));
            return true;
        }
    }

    /**
     * Kicks the selected user out of the system instantly by resetting the user's session.
     * This means, the user will be "logged out".
     *
     * @param $userId
     * @return bool
     */
    private static function resetUserSession($userId)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE users SET session_id = :session_id  WHERE user_id = :user_id LIMIT 1");
        $query->execute(array(
            ':session_id' => null,
            ':user_id' => $userId,
        ));

        if ($query->rowCount() == 1) {
            Session::add('feedback_positive', Text::get('FEEDBACK_ACCOUNT_USER_SUCCESSFULLY_KICKED'));
            return true;
        }
    }

    public static function CurrentSubscribersModel() {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "
                    select u.user_id, u.user_email, s.added, s.endDate, s.referenceId, p.product_id, p.entity, p.entity_id from users u
                    inner join subscriptions s on s.user_id = u.user_id
                    inner join product p on s.product_id = p.id
                    where s.status = 'active' order by added desc
        ";
        $query = $database->prepare($sql);
        $query->execute();
        $users = $query->fetchAll();
        foreach ($users as &$user) {
            $token = ApiModel::encodeToken($user->user_id);
            if ($user->entity == "app_tiers") {
                $sql = "SELECT case when a.app_key = 'coursebuildr' then concat(a.launch,'data/',:token) else concat(a.launch, '?data=', :token) end url, a.name from apps a
                        inner join app_tiers apt on apt.app_id = a.`app_id`
                        where apt.id = :id";
            } else if ($user->entity == "bundle") {
                $sql = "SELECT case when a.app_key = 'coursebuildr' then concat(a.launch,'data/',:token) else concat(a.launch, '?data=', :token) end url, a.name
                    FROM bundle b
                    INNER JOIN bundle_apps ba ON b.id = ba.bundle
                    INNER JOIN app_tiers at ON at.id = ba.app_tier
                    INNER JOIN apps a ON at.app_id = a.app_id
                    WHERE b.id = :id
                    ORDER BY at.tier_level, a.name";
            }
            $query = $database->prepare($sql);
            $query->execute(array(":id" => $user->entity_id, ":token" => $token));
            $launchlinks = $query->fetchAll();
            $user->launchlinks = $launchlinks;
            $user->ref_id = Text::base64enc(Encryption::encrypt($user->user_id));
        }
        return array(
            "user" => $users
        );
    }
}
