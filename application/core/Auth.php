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

    public static function set_user_logon_cookie($user_id) {
        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("UPDATE users set user_remember_me_token=:hash where user_id=:id limit 1");
        $hash = md5(uniqid());
        Response::cookie("login", $hash);
        return $query->execute(array(":hash"=>$hash,":id"=>$user_id));
    }
}
