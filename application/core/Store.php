<?php
class Store
{
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
    public static function AppMatrix($App, $AppFeatures, $AppTiers, $UserSubscription, $options)
    {

        $fs = new FastSpring(Config::get('FASTSPRING_STORE'), Config::get('FASTSPRING_API_USER'), Config::get('FASTSPRING_API_PASSWORD'));
        $table = array();
        $colspan = count($AppTiers);
        $table[] = '<table class="app-matrix colspan-' . $colspan . '">';
        $table[] = '<thead>';
        $table[] = "<tr><td></td><td colspan='$colspan'>" . Text::get('TIER_MATRIX_HEADER') . "</td></tr>";
        if (KeyStore::find("tiersystem")->get() == "true") {
            // $table[] = "<tr><td></td><td colspan='$colspan'><div id='tier-bracket'>Tiers</div></td></tr>";
            $table[] = '<tr><td></td>';
            foreach ($AppTiers as $tier) {
                $table[] = "<th class='tier-level-" . $tier["tier_level"] . "'><i class='cs-" . strtolower($tier["name"]) . "-full'></i></th>";
            }
            $table[] = '</tr>';
        }
        $table[] = '</thead><tbody>';

        // app feature matrix
        foreach ($AppFeatures as $info) {
            $details = $info["details"];
            $feature = $info["feature"];
            $icon = "";
            if (isset($details) && !empty($details)) {
                $icon = "<span data-tooltip='" . addslashes($details) . "'><i class='cs-help-with-circle cs-super cs-muted'></i></span>";
            }

            $table[] = "<tr><th>$feature $icon</th>";
            foreach ($AppTiers as $tier) {
                $tier_level = (int) $tier["tier_level"];
                $info_level = (int) $info["min_tier_level"];
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
            $table[] = '<tr class="price fill"><th>Price:</th>';
            foreach ($AppTiers as $tier) {
                $table[] = '<td>$' . $tier["price"] . ' per ' . $tier["period"] . ' (' . $tier["currency"] . ')</td>';
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
                if ($label != "") {
                    $table[] = "<a href='$button_url' class='$class_name' target='_app'>$label</a>";
                }

                $table[] = "</td>";
            }
            $table[] = '<tr><td></td>';
            $user_id = Session::get('user_id');
            foreach ($AppTiers as $tier) {
                if (!empty(SubscriptionModel::previouslySubscribed($user_id)) && SubscriptionModel::previouslySubscribed($user_id) == $tier["tier_id"] && empty($UserSubscription)) {
                    $table[] = "<td>Previous subscription</td>";
                } else {
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

    public static function TierMatrix($Tiers, $Apps, $UserSubscription, $Name, $options)
    {

        $infourl = Config::get("URL") . "store/info";

        $table = array();
        $colspan = count($Tiers) + 1;
        $table[] = '<table class="tier-matrix app-matrix">';
        $tier_apps = array();
        if (KeyStore::find("tiersystem")->get() == "true") {
            $table[] = "<thead><tr><td></td>";
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
            $table[] = '</tr></thead>';
        } else {
            foreach ($Tiers as $tier) {
                $tier_apps = array_merge($tier_apps, $tier["app_ids"]);
            }
        }
        $table[] = '<tbody>';
        $tier_apps = array_unique($tier_apps);
        foreach ($Apps as $app) {
            if (in_array($app["app_id"], $tier_apps)) {
                $headered = false;
                $AppFeatures = TierModel::getAppFeatures($app["app_id"]);
                $AppTiers = TierModel::getAllAppTiers($app["app_id"]);
                if ($headered != true) {
                    $table[] = "<tr><th colspan='$colspan' class='app-header'>" . $app["name"] . " <a href='$infourl/{$app["name"]}' class='float-right'><i class='cs-info-large-outline'></i></a></th></tr>";
                    $headered = true;
                }
                foreach ($AppFeatures as $info) {
                    $table[] = '<tr><td><h3>' . $info->feature . '</h3>' . $info->details . '</td>';
                    $cell = 1;
                    for ($i = 1; $i <= $AppTiers[0]->tier_level -1; $i++) { // tier_level is 1 based
                        $table[] = "<td>-</td>";
                    }
                    foreach ($AppTiers as $tier) {
                        $tier_level = (int) $tier->tier_level;
                        $info_level = (int) $info->min_tier_level;
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
            } else {
                $AppTiers = TierModel::getAllAppTiersForPack($Name);
                foreach ($AppTiers as $tier) {
                    if ($UserSubscription['tier_id'] < $tier->tier_id) {
                        $button_url = Config::get('URL') . 'store/updateSubscription/' . $tier->name;
                        $table[] = "<td><a href='$button_url'>Upgrade</a></td>";
                    } elseif ($UserSubscription['tier_id'] > $tier->tier_id) {
                        $button_url = Config::get('URL') . 'store/updateSubscription/' . $tier->name;
                        $table[] = "<td><a href='$button_url'>Downgrade</a></td>";
                    } else {
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
