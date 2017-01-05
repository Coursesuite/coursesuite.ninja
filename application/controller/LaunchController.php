<?php

class LaunchController extends Controller
{

    public function __construct(...$args)
    {

        parent::__construct();

        $count = count($args[1]);

        if ($count < 1) {
            Redirect::home(); // not enough params, reset

        } else if ($count == 2 || $count == 1) {
            Auth::checkAuthentication(); // looks like an internal call

        } else {
            // No need to check Auth, it's a parameterised launch, let the index sort it out
        }

    }

    public function index($appkey = "", ...$args)
    {
        $cargs = count($args);

        if ($cargs >= 2) { // launch/docninja/apikey/f63bc5ae39 or launch/docninja/token/e7847fds789
            $method = strtolower($args[0]);
            $token = $args[1];

        } else if ($cargs == 1) {  // launch/docninja/48fbaec24
            $method = "token";
            $token = $args[0];

        } else if (!SubscriptionModel::user_has_active_subscription_to_app(Session::CurrentUserId(), $appkey)) { // launch/docninja
                Redirect::to("store/$appkey/invalid/");

        } else {
            $token = "";
            $method = "";

        }

        $url = AppModel::getLaunchUrl($appkey, $method, $token);

        if (!empty($url)) {
            Redirect::external($url);
        } else {
            Redirect::to("500"); // because we don't know what it could be
        }

    }

}
