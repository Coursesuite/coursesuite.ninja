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
	        "baseurl" => Config::get("URL"),
            "App" => $app,
            "AppFeatures" => TierModel::getAppFeatures((int)$app->app_id),
            "AppTiers" => TierModel::getAllAppTiers((int)$app->app_id),
            "UserSubscription" => null,
            "user_id" => Session::get('user_id')
        );
		if (Session::currentUserId() > 0) {
			$submodel = SubscriptionModel::getCurrentSubscription(Session::currentUserId());
			if (!empty($submodel) && $submodel->status == 'active') {
				$model["UserSubscription"] = $submodel;
			}
	    };
        if (Session::get("user_account_type") == 7) {
    	    // $model["tokenlink"] = AppModel::getLaunchUrl($app->app_id); // because token verify checks the subscription
    	    $model["editlink"] =Config::get("URL") . 'admin/editApps/' . $app->app_id . '/edit';
        }
        $this->View->renderHandlebars("store/info", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

    public function tiers($name) {
	    $model = array(
	        "baseurl" => Config::get("URL"),
		    "Name" => $name,
			"Apps" => AppModel::getAllApps(false),
			"Tiers" => TierModel::getTierPackByName($name, false),
			"UserSubscription" => null,
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
    public function updateSubscription($newSubscription, $confirm=Null) {
    	$userId = Session::get('user_id');
    	$model = array(
    		"baseurl" => Config::get("URL"),
    		"user_id" => $userId,
    		"user_name" => Session::get('user_name'),
    		"current_tier" => TierModel::getTierById(SubscriptionModel::getCurrentSubscription($userId)->tier_id, false),
    		"new_tier" => TierModel::getTierById(TierModel::getTierIdByName($newSubscription), false),
    		"subscription_ref" => SubscriptionModel::getCurrentSubscription($userId)->referenceId
    		);
    	$this->View->renderHandlebars("store/updateSubscription", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));

    	if ($confirm) {
    		$fs = new FastSpring(Config::get('FASTSPRING_STORE'), Config::get('FASTSPRING_API_USER'), Config::get('FASTSPRING_API_PASSWORD'));
    		$fs->updateSubscription($model["subscription_ref"], $fs->updateSubscriptionXML(strtolower('/'.$model['new_tier']->name).'-1-month', true));
    		Redirect::to('user/index'); 
    	}
    }

   /**
     * called from within a handlebars template to render a table
     a bit ugly to render, and View isn't the best place to put this function since it's rendering models,
     but we are comparing tiers to features (for an app)
    each feature requires a minimum tier level, and has a true/false label
    a user is subscribed to a tier, and we can offer upgrades to get to a higher tier
    if the user tier allows access to that app

	// look into
    // https://support.fastspring.com/hc/en-us/articles/207436046-Retrieving-Localized-Store-Pricing

                     tiers:  low     medium      high
    features:
        a                      no         no            yes
        b                      off         on            on
        c                      fixed     custom     custom
     */
    public static function AppMatrix($App, $AppFeatures, $AppTiers, $UserSubscription, $user_id, $options) {
    	$fs = new FastSpring(Config::get('FASTSPRING_STORE'), Config::get('FASTSPRING_API_USER'), Config::get('FASTSPRING_API_PASSWORD'));
        $table = array();
        $colspan = count($AppTiers);
        $table[] = '<table class="app-matrix colspan-' . $colspan . '"><thead>';
        // $table[] = "<tr><td></td><td colspan='$colspan'><div id='tier-bracket'>Tiers</div></td></tr>";
        $table[] = "<tr><td></td><td colspan='$colspan'>" . Text::get('TIER_MATRIX_HEADER') . "</td></tr>";
        $table[] = '<tr><td></td>';
        foreach ($AppTiers as $tier) {
            $table[] = "<th class='tier-level-" . $tier["tier_level"] . "'><i class='cs-" . strtolower($tier["name"]) . "-full'></i></th>";
        }
        $table[] = '</tr></thead><tbody>';

		// app feature matrix
        foreach ($AppFeatures as $info) {
	        $details = $info["details"];
	        $feature = $info["feature"];
	        $icon = "";
	        if (isset($details) && !empty($details)) $icon = "<span data-tooltip='" . addslashes($details) . "'><i class='cs-help-with-circle cs-super cs-muted'></i></span>"; 
            $table[] = "<tr><th>$feature $icon</th>";
            foreach ($AppTiers as $tier) {
                $tier_level = (int)$tier["tier_level"];
                $info_level = (int)$info["min_tier_level"];
                $value = $info["mismatch_label"];
                if ($tier_level >= $info_level) {
                    $value = $info["match_label"];
                }
                $table[] = "<td class='tier-level-$tier_level'>$value</td>";
            }
            $table[] = '</tr>';
        }
        $table[] = '</tbody><tfoot>';

		// price in footer
		if (Config::get('STORE_INFO_SHOW_PRICING') == true) {
	        $table[] = '<tr class="price fill"><th>Price (USD):</th>';
	        foreach ($AppTiers as $tier) {
		        $table[] = '<td>$' . $tier["price"] . ' /' . $tier["period"] . '</td>';
		    }
		    $table[] = '</tr>';
	    }

		// purchase / launch buttons
        $table[] = '<tr class="fill"><th></th>';
        if (Session::userIsLoggedIn()) {
            foreach ($AppTiers as $tier) {
		        $label = 'Subscribe';
		        $button_url = trim($tier["store_url"]) . "?referrer=" . Text::base64enc(Encryption::encrypt(Session::CurrentUserId())) . Config::get('FASTSPRING_PARAM_APPEND');
		        $class_name = '';
		        if (!empty($UserSubscription)) {
			        $user_tier_level = $UserSubscription["tier_id"];
			        $label = "";
			        if ($tier["tier_id"] == $user_tier_level) {
				        $label = 'Launch App';
				        $class_name = 'current-tier';
				        $button_url = Config::get("URL") . "launch/" . $App["app_id"]; //    AppModel::getLaunchUrl($App["app_id"]);
			        } else if ($tier["tier_id"] < $user_tier_level) {
				        $label = 'Downgrade';
				        $button_url = Config::get('URL') . 'store/updateSubscription/' . $tier['name'];
			        } else if ($tier["tier_id"] > $user_tier_level) {
				        $label = 'Upgrade';
				        $button_url = Config::get('URL') . 'store/updateSubscription/' . $tier['name'];
			        }
		        }
                $table[] = "<td>";
                if ($label != "") $table[]= "<a href='$button_url' class='$class_name' target='_app'>$label</a>";
                $table[] = "</td>";
            }
            $table[] = '<tr><td></td>';
            foreach ($AppTiers as $tier) {
            	if (!empty(SubscriptionModel::previouslySubscribed($user_id)) && SubscriptionModel::previouslySubscribed($user_id) == $tier["tier_id"] && empty($UserSubscription)) {
                	$table[] = "<td>Previous subscription</td>";	
                }
                else {
                	$table[] = "<td></td>";
                }
            }

        } else {
            $table[] = "<td colspan='$colspan'><a href='" . Config::get('URL') . "login/'>Please log in to subscribe</a></td>";
        }
        $table[] = '</tr>';
        // caveat
        $table[] = "<tr class='caveat'><td></td><td colspan='$colspan'>" . Text::get("TIER_MATRIX_CAVEATS") . "</td></tr>";
        // $table[] = "<tr class='caveat'><td></td><td colspan='$colspan'><p>This product is part of a paid subscription that offers multiple products (<a href='" . Config::get('URL') . "store/tiers/NinjaSuite'>details</a>).</p><p>Subscriptions are charged monthly until cancelled.</p><div class='text-center'><img src='/img/fastspring.png'></div></td></tr>";
        $table[] = '</tfoot></table>';
        return implode('', $table);
        
    }
    
    
    /*
	    Renderer which is similar to an App Tier Matrix except that it is listing all matching tiers
	*/

    public static function TierMatrix($Tiers, $Apps, $UserSubscription, $Name, $options) {

		$table = array();
		$colspan = count($Tiers) + 1;
		$table[] = '<table class="tier-matrix app-matrix"><thead>';
		$table[] = "<tr><td></td>";
		$tier_apps = array();
		foreach ($Tiers as $tier) {
		    $table[] = "<th class='tier-level-" . $tier["tier_level"] . "'><i class='cs-" . strtolower($tier["name"]) . "-full'></i>";
		    // if (isset($tier["description"])) $table[] = "<br><small>" . $tier["description"] . "</small>";
			if ($UserSubscription !== null) {
				if ($UserSubscription["tier_id"] == $tier["tier_id"]) {
					$table[] = '<p>(Your current tier)</p>';
				}
			}
		    $table[] = "</th>";
		    $tier_apps = array_merge($tier_apps, $tier["app_ids"]);
		}
		$table[] = '</tr></thead><tbody>';
		$tier_apps = array_unique($tier_apps);
		foreach ($Apps as $app) {
			if (in_array($app["app_id"], $tier_apps)) {
				$headered = false;
				$AppFeatures = TierModel::getAppFeatures($app["app_id"]);
				$AppTiers = TierModel::getAllAppTiers($app["app_id"]);
				if ($headered != true) {
					$table[] = "<tr><th colspan='$colspan' class='app-header'>" . $app["name"] . "</th></tr>";
					$headered = true;
				}
		        foreach ($AppFeatures as $info) {
		            $table[] = '<tr><td><h3>' . $info->feature . '</h3>' . $info->details . '</td>';
		            $cell = 1;
		            for ($i=1; $i <= $AppTiers[0]->tier_level; $i++) {
			            $table[] = "<td>-</td>";
		            }
		            foreach ($AppTiers as $tier) {
		                $tier_level = (int)$tier->tier_level;
		                $info_level = (int)$info->min_tier_level;
		                $value = $info->mismatch_label;
		                if ($tier_level >= $info_level) {
		                    $value = $info->match_label;
		                }
		                $table[] = "<td class='tier-level-$tier_level'>$value</td>";
		            }
		            $table[] = '</tr>';
		        }
	        }
		}
		$table[] = '</tbody><tfoot>';
        $table[] = '<tr class="fill"><th></th>';
        if (Session::userIsLoggedIn()) {
	        if ($UserSubscription === null) {
				$AppTiers = TierModel::getAllAppTiersForPack($Name);
	            foreach ($AppTiers as $tier) {
			        $button_url = trim($tier->store_url) . "?referrer=" . Text::base64enc(Encryption::encrypt(Session::CurrentUserId())) . Config::get('FASTSPRING_PARAM_APPEND');
	                $table[] = "<td><a href='$button_url'>Subscribe</a></td>";
	            }
            }
            else {
				$AppTiers = TierModel::getAllAppTiersForPack($Name);
				foreach ($AppTiers as $tier) {
					if ($UserSubscription['tier_id'] < $tier->tier_id){
						$button_url = Config::get('URL') . 'store/updateSubscription/' . $tier->name;
						$table[] = "<td><a href='$button_url'>Upgrade</a></td>";
					}
					elseif ($UserSubscription['tier_id'] > $tier->tier_id){
						$button_url = Config::get('URL') . 'store/updateSubscription/' . $tier->name;
						$table[] = "<td><a href='$button_url'>Downgrade</a></td>";
					}
					else{
						$table[] = "<td><a href=''>Launch</a></td>";
					}
				}   	
            }

        } else {
            $table[] = "<td colspan='$colspan'><a href='" . Config::get('URL') . "login/'>Please log in to subscribe</a></td>";
        }
        $table[] = '</tfoot></table>';

		return implode('', $table);
    }

}
