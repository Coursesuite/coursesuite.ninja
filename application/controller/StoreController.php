<?php

class StoreController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     * we are being passed the current function and parameters, so remember these in case we need to log on
     */
    public function __construct($current_function = "", ...$params)
    {
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
    public function index()
    {
        $storedata = StoreModel::getStoreViewModel();
        $this->View->renderHandlebars("store/index", $storedata, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

    public function info($app_key)
    {
        $app = AppModel::getAppByKey($app_key);
        $spm = StoreProductModel::getProductsByAppId($app->app_id);
        $model = array(
            "baseurl" => Config::get("URL"),
            "App" => $app,
            "AppFeatures" => TierModel::getAppFeatures((int) $app->app_id),
            "AppTiers" => TierModel::getAllAppTiers((int) $app->app_id),
            "UserSubscription" => null,
            "user_id" => Session::get('user_id'),
            "purchase_url" => isset($spm[0]) ? StoreProductModel::getStoreProductById((int) $spm[0]->product_id)->purchase_url : "",
            "urlSuffix" => "?referrer=" . Text::base64enc(Encryption::encrypt(Session::CurrentUserId())) . Config::get('FASTSPRING_PARAM_APPEND'),
        );
        if (Session::currentUserId() > 0) {
            $submodel = SubscriptionModel::getCurrentSubscription(Session::currentUserId());
            if (!empty($submodel) && $submodel->status == 'active') {
                $model["UserSubscription"] = $submodel;
            }
        };
        if (Session::get("user_account_type") == 7) {
            // $model["tokenlink"] = AppModel::getLaunchUrl($app->app_id); // because token verify checks the subscription
            $model["editlink"] = Config::get("URL") . 'admin/editApps/' . $app->app_id . '/edit';
        }
        $this->View->renderHandlebars("store/info", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

    public function tiers($name)
    {
        $model = array(
            "baseurl" => Config::get("URL"),
            "Name" => $name,
            "Apps" => AppModel::getAllApps(false),
            "Tiers" => TierModel::getTierPackByName($name, false),
            "UserSubscription" => null,
            "tiersystem" => (KeyStore::find("tiersystem")->get() == "true"),
        );
        if (Session::currentUserId() > 0) {
            $submodel = SubscriptionModel::getCurrentSubscription(Session::currentUserId());
            if (!empty($submodel) && $submodel->status == 'active') {
                $model["UserSubscription"] = $submodel;
            }
        } else {
            $model["notLoggedOn"] = true;
        }
        $this->View->renderHandlebars("store/tiers", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
        // $this->View->render("store/tiers", $model);

    }

    // $newSubscription = the tier name e.g Bronze, Silver, Gold
    public function updateSubscription($newSubscription, $confirm = null)
    {
        $userId = Session::get('user_id');
        $model = array(
            "baseurl" => Config::get("URL"),
            "user_name" => Session::get('user_name'),
            "current_tier" => TierModel::getTierById(SubscriptionModel::getCurrentSubscription($userId)->tier_id, false),
            "new_tier" => TierModel::getTierById(TierModel::getTierIdByName($newSubscription), false),
            "subscription_ref" => SubscriptionModel::getCurrentSubscription($userId)->referenceId,
        );
        $this->View->renderHandlebars("store/updateSubscription", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));

        if ($confirm) {
            $fs = new FastSpring(Config::get('FASTSPRING_STORE'), Config::get('FASTSPRING_API_USER'), Config::get('FASTSPRING_API_PASSWORD'));
            $fs->updateSubscription($model["subscription_ref"], $fs->updateSubscriptionXML(strtolower('/' . $model['new_tier']->name) . '-1-month', true));
            SubscriptionModel::updateSubscriptionTier($model["subscription_ref"], $model["new_tier"]->tier_id, $model["current_tier"]);
            Redirect::to('user/index');
        }
    }

    public function contactUs()
    {
        $user_email = Request::post("your-email", true);
        $user_name = Request::post("your-name", true);
        $message = Request::post("your-message", true);
        $captcha = Request::post("g-recaptcha-response");

        if (($captcha_check = CaptchaModel::checkCaptcha($captcha)) === true) {

            $mailer = new Mail;
            $mail_sent = $mailer->sendMail(Config::get('EMAIL_SUBSCRIPTION'), $user_email, $user_name, "CourseSuite Contact Form", $message);

            $model = array(
                "sent" => true, // $mail_sent,
                "message" => Text::get("CONTACT_FORM_OK"),
            );
        } else {
            $model = array(
                "sent" => false, // $mail_sent,
                "message" => Text::get("CONTACT_FORM_SPAM"),
            );
        }
        $this->View->renderJSON($model);

    }

    public function bundles() {
        $model = array(
            "baseurl" => Config::get('URL'),
            "bundles" => BundleModel::getBundles(),
            "allApps" => AppModel::getAllApps(),
            "scripts" => array("jquery.fancybox.js", "jquery.fancybox.pack.js", "bundles.js"),
            "sheets" => array("jquery.fancybox.css"),
            );
        // Iterate through each bundle
        foreach ($model['bundles'] as $bundle) {
            // Add app names to bundle
            $appIds = BundleModel::getBundleContents($bundle->product_id);
            $bundleAppNames = array();
            foreach ($appIds as $id) {
                $bundleApp = AppModel::getAppById($id->app_id);
                $bundleApp->name = str_replace(' ', '-', $bundleApp->name); // add dashes to names to fix id problem
                array_push($bundleAppNames, $bundleApp->name);
            }
            $bundle->bundleAppNames = $bundleAppNames;
            // Add all app info to bundle
            $bundleApps = array();
            foreach ($appIds as $id) {
                $app = AppModel::getAppById($id->app_id);
                $app->name = str_replace(' ', '-', $app->name); // add dashes to names to fix id problem
                array_push($bundleApps, $app);
            }
            $bundle->bundleApps = $bundleApps;
            // Add product data to bundle
            $bundleProducts = BundleModel::getBundleProducts($bundle->bundle_id);
            $products = array();
            foreach ($bundleProducts as $bp) {
                array_push($products, StoreProductModel::getStoreProductById($bp->product_id));
            }
            $bundle->products = $products;
        }
        // Add all active app names
        $activeApps = AppModel::getActiveApps();
        $allAppNames = array();
        foreach ($activeApps as $app) {
            $app->name = str_replace(' ', '-', $app->name); // add dashes to names to fix id problem
            array_push($allAppNames, $app->name);
        }
        $model['allAppNames'] = $allAppNames;
        $this->View->renderHandlebars("store/bundles", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

    public function bundleInfo($id) {
        $model = array(
            "baseurl" => Config::get('URL'),
            "bundleId" => $id,
            "tiers" => TierModel::getAllTiers(true),
            );
        $this->View->renderHandlebars("store/bundleInfo", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

}
