<?php

class StoreController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     * we are being passed the current function and parameters, so remember these in case we need to log on
     */
    public function __construct($current_function = "", ...$params) {
        parent::__construct();
        if (Session::userIsLoggedIn()) {
            Session::remove("RedirectTo");
        } else {
            $extra = (is_array($params[0])) ? "/" . implode("/", $params[0]) : "";
            Session::set("RedirectTo", "store/$current_function$extra");
        }
    }

    /**
     * Handles what happens when user moves to URL/index/index - or - as this is the default controller, also
     * when user moves to /index or enter your application at base level
     */
    public function index() {
        $storedata = StoreModel::getStoreViewModel();
        $this->View->renderHandlebars("store/index", $storedata, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

    public function info($app_key) {
            $app = AppModel::getAppByKey($app_key);
            $model = array(
                "App" => $app,
                "AppTierFeatures" => TierModel::getAppTierFeatures((int)$app->app_id),
                "AppTiers" => TierModel::getAllAppTiers((int)$app->app_id),
                "UserSubscription" => SubscriptionModel::getUserCurrentSubscription()
            );
            $this->View->renderHandlebars("store/info", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

}
