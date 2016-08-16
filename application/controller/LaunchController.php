<?php

/**
 * This controller shows an area that's only visible for logged in users (because of Auth::checkAuthentication(); in line 16)
 */
class LaunchController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();

        // TODO: need to know if page was called with two params and avoid auth check
        Auth::checkAuthentication();
    }

    public function index($appkey = "", $token = "")
    {
        if (empty($appkey)) {
            Redirect::to("store");
        }
        $url = AppModel::getLaunchUrl($appkey);
        if (empty($token)) {
            Redirect::external($url);
        } else {
            $database = DatabaseFactory::getFactory()->getConnection();
            // this token exists and is fairly current
            $query = $database->prepare("SELECT count(1) FROM api_requests
                    WHERE token = :token
                    AND created < CURDATE()
                    AND created > CURDATE() - INTERVAL 1 DAY");
            $query->execute(array(":token" => $token));
            $val = intval($query->fetchColumn());
            if ($val > 0) {
                Redirect::external($url);
            } else {
                $this->View->renderJSON(array("error" => "invalid or expired apikey"));
            }
        }
    }

    public function app($appkey = "")
    {
        if (empty($appkey)) {
            Redirect::to("404");
        }
        $url = AppModel::getLaunchUrl($appkey);
        Redirect::external($url);
    }

}
