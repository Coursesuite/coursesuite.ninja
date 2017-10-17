<?php

class LoginModel
{
    public static function logout()
    {
        Session::reset();
        Response::cookie("login", null);
    }
}
