<?php

class LoginModel
{
    public static function logout($all = false)
    {
        $cookie_value = Request::cookie("login");
        if (empty($cookie_value)) {
            return;
        }
        if ($all === true) {
            if ($user_id = Model::Read("users","md5(user_id) IN (SELECT `user` FROM logons WHERE `cookie`=:hash)",array(":hash"=>$cookie_value),"user_id",true)) {
        		Auth::logout_user_all($user_id);
        	}
        } else {
	        Auth::logout_user_self($cookie_value);
	    }
        Session::reset();
    }

    public static function current_logins_model($user_id) {
        return Model::Read("logons","user=:user",array(":user"=>md5($user_id)));
    }

}
