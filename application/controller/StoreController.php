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
            "UserSubscription" => null
        );
		if (Session::currentUserId() > 0) {
			$submodel = SubscriptionModel::getAllSubscriptions(Session::currentUserId(), false, true);
			if (!empty($submodel)) {
				$model["UserSubscription"] = $submodel;
			}
	    };
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
			$submodel = SubscriptionModel::getAllSubscriptions(Session::currentUserId(), false, true);
			if (!empty($submodel)) {
				$model["UserSubscription"] = $submodel;
			}
	    };
        $this->View->renderHandlebars("store/tiers", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
        // $this->View->render("store/tiers", $model);
	    
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
    public static function AppMatrix($App, $AppFeatures, $AppTiers, $UserSubscription, $options) {

        $table = array();
        $colspan = count($AppTiers);
        $table[] = '<table class="app-matrix"><thead>';
        $table[] = "<tr><td></td><td colspan='$colspan'><div id='tier-bracket'>Tiers</div></td></tr>";
        $table[] = '<tr><td></td>';
        foreach ($AppTiers as $tier) {
            $table[] = "<th class='tier-level-" . $tier["tier_level"] . "'>" . $tier["name"] . "</th>";
        }
        $table[] = '</tr></thead><tbody>';

		// app feature matrix
        foreach ($AppFeatures as $info) {
            $table[] = '<tr><th>' . $info["feature"] . '</th>';
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
        $table[] = '<tr class="price fill"><th>Price (USD):</th>';
        foreach ($AppTiers as $tier) {
	        $table[] = '<td>$' . $tier["price"] . ' /' . $tier["period"] . '</td>';
	    }
	    $table[] = '</tr>';

		// purchase / launch buttons
        $table[] = '<tr class="fill"><th></th>';
        if (Session::userIsLoggedIn()) {
            foreach ($AppTiers as $tier) {
		        $label = 'Subscribe';
		        $button_url = trim($tier["store_url"]) . "?referrer=" . Text::base64enc(Encryption::encrypt(Session::CurrentUserId())) . "&mode=test";
		        $class_name = '';
		        if (!empty($UserSubscription)) {
			        $user_tier_level = $UserSubscription["tier"]["tier_id"];
			        $label = "";
			        if ($tier["tier_id"] == $user_tier_level) {
				        $label = 'Launch App';
				        $class_name = 'current-tier';
				        $button_url = AppModel::getLaunchUrl($App["app_id"]);
			        } else if ($tier["tier_id"] < $user_tier_level) {
				        // $label = 'Downgrade';
			        } else if ($tier["tier_id"] > $user_tier_level) {
				        // $label = 'Upgrade';
			        }
		        }
                $table[] = "<td>";
                if ($label != "") $table[]= "<a href='$button_url' class='$class_name'>$label</a>";
                $table[] = "</td>";
            }
        } else {
            $table[] = "<td colspan='$colspan'><a href='" . Config::get('URL') . "login/'>Please log in to subscribe</a></td>";
        }
        $table[] = '</tr>';

        // caveat
        $table[] = "<tr class='caveat'><td></td><td colspan='$colspan'><p>This product is part of a paid subscription that offers multiple products (<a href='" . Config::get('URL') . "store/tiers/coursesuite'>details</a>).</p><p>Subscriptions are charged monthly until cancelled.</p><div class='text-center'><img src='/img/fastspring.png'></div></td></tr>";
        $table[] = '</tfoot></table>';
        return implode('', $table);
        
    }
    
    
    /*
	    Renderer which is similar to an App Tier Matrix except that it is listing all matching tiers
	*/
    
    public static function TierMatrix($Tiers, $Apps, $UserSubscription, $options) {

		$table = array();
		$colspan = count($Tiers) + 1;
		$table[] = '<table class="tier-matrix app-matrix"><thead>';
		$table[] = "<tr><td></td>";
		$tier_apps = array();
		foreach ($Tiers as $tier) {
		    $table[] = "<th class='tier-level-" . $tier["tier_level"] . "'>" . $tier["name"];
		    if (isset($tier["description"])) $table[] = "<br><small>" . $tier["description"] . "</small>";
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
		$table[] = '</tbody></table>';
		return implode('', $table);
    }

}
            }
		            $table[] = '</tr>';
		        }
	        }
		}		
		$table[] = '</tbody></table>';
		return implode('', $table);
    }

}
