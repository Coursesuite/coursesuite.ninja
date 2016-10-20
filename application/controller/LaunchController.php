<?php

class LaunchController extends Controller
{

    public function __construct(...$args)
    {

        parent::__construct();

        $count = count($args[1]);
        if ($count < 2) {
            Redirect::home(); // not enough params, reset
        } else if ($count == 2) {
            Auth::checkAuthentication(); // looks like an internal call
        } else {
            // No need to check Auth, it's a parameterised launch, let the index sort it out
        }

    }

    public function index($appkey = "", ...$args)
    {

        if (count($args) >= 2) {
            // launch/docninja/apikey/f63bc5ae39
            $method = strtolower($args[0]);
            $token = $args[1];
            // any more params than that are ignored
        } else {
            // launch/docninja/48fbaec24
            $method = "token";
            $token = $args[0];

            // TODO: validate this user has an active subscription to a product that contains this $appkey
        }

        $url = AppModel::getLaunchUrl($appkey, $method, $token);
        if (!empty($url)) {
            Redirect::external($url);
        } else {
            Redirect::to("500"); // because we don't know what it could be
        }

    }

}
