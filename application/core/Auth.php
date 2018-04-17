<?php

class Auth
{

    public static function checkAuthentication()
    {
        if (!Session::userIsLoggedIn()) {
            Session::set("RedirectTo", urlencode($_SERVER['REQUEST_URI']));
            header('location: ' . Config::get('URL') . 'login');
            exit();
        }
    }

    public static function checkAdminAuthentication()
    {
        if (!Session::userIsAdmin()) {
            Session::destroy();
            header('location: ' . Config::get('URL') . 'login/admin');
            exit();
        }
    }

    // info@ password = $2y$10$ps6g57RStBCkUiREL4RyZeI3XTWRXV4g6gZrs/Wvshvy09oiLnGaW
    public static function is_administrator_email($email) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("SELECT count(1) FROM users WHERE user_email=:email AND user_account_type=7");
        $query->execute(array(":email"=>$email));
        return ((int)$query->fetchColumn() > 0);
    }

    public static function set_user_logon_cookie($user_id, $source = null) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $ip = Environment::remoteIp();
        $browser = Environment::useragent();
        $hash = md5(uniqid());

        Response::cookie("login",$hash);

        // update user record
        $query = $database->prepare("UPDATE users SET user_logon_count = user_logon_count + 1, last_browser=:browser, last_ip=:ip, user_last_login_timestamp=:now WHERE user_id=:user");
        $query->execute([
            ":user"=>$user_id,
            ":ip"=>$ip,
            ":browser"=>$browser,
            ":now"=>time()
        ]);

        // record login
        $query = $database->prepare("INSERT INTO logons (`user`,`cookie`,`ip`,`source`) VALUES (:user,:cookie,:ip,:source)"); // " users set user_remember_me_token=:hash where user_id=:id limit 1");
        return $query->execute([
            ":user"=>md5($user_id),
            ":cookie"=>$hash,
            ":ip"=>$ip,
            ":source"=>md5($source . Config::get('HMAC_SALT'))
        ]);
    }

    // log out THIS browser
    public static function logout_user_self($cookie_value) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("DELETE FROM logons WHERE `cookie`=:hash");
        $query->execute(array(":hash"=>$cookie_value));
        Response::cookie('login', null);
    }

    // log out ALL browsers (excluding an optional one, typically self)
    public static function logout_user_all($user_id, $retain_cookie = "") {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "DELETE FROM logons WHERE `user`=:hash";
        if (!empty($retain_cookie)) {
            $sql .= " AND `cookie`<>:cookie";
        } else {
           Response::cookie('login', null);
        }
        $query = $database->prepare($sql);
        $query->execute(array(
            ":hash"=>md5($user_id),
            ":cookie"=>$retain_cookie
        ));
    }

}
