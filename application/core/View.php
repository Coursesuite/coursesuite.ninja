<?php

use LightnCandy\LightnCandy;

/**
 * Class View
 * The part that handles all the output
 */
class View
{

    /**
     * simply includes (=shows) the view. this is done from the controller. In the controller, you usually say
     * $this->view->render('help/index'); to show (in this example) the view index.php in the folder help.
     * Usually the Class and the method are the same like the view, but sometimes you need to show different views.
     * @param string $filename Path of the to-be-rendered view, usually folder/file(.php)
     * @param array $data Data to be used in the view
     */
    public function render($filename, $data = null)
    {
        self::renderWithTemplate("_templates", $filename, $data);
        /*
        if ($data) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }

        require Config::get('PATH_VIEW') . '_templates/header.php';
        require Config::get('PATH_VIEW') . $filename . '.php';
        require Config::get('PATH_VIEW') . '_templates/footer.php';
        */
    }

    public function renderWithTemplate($template_folder, $filename, $data = null) {
        if ($data) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }
        require Config::get('PATH_VIEW') . $template_folder . '/header.php';
        require Config::get('PATH_VIEW') . $filename . '.php';
        require Config::get('PATH_VIEW') . $template_folder . '/footer.php';
    }

    public function renderHandlebars($filename, $data = null, $template_folder = false, $force = false) {

        $hashname = md5($filename);

        $precompiled = Config::get('PATH_VIEW_PRECOMPILED') . $hashname . '.php';
        $assoc = json_decode(json_encode($data), true); // data is now an associative array

		// expose data so header and footer can also pick it up
        if ($data) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }

        if (!file_exists($precompiled) || $force == TRUE) { // if we have already compiled this page, don't compile it again unless being forced to

            $template = file_get_contents(Config::get('PATH_VIEW') . $filename . '.hba');

            $phpStr = LightnCandy::compile($template, array(
              "flags" => LightnCandy::FLAG_PARENT | LightnCandy::FLAG_ADVARNAME | LightnCandy::FLAG_HANDLEBARS, //  | LightnCandy::FLAG_STANDALONEPHP | LightnCandy::FLAG_ERROR_LOG,
              "helpers" => array(
                  "equals" => function ($arg1, $arg2, $options) {
                    if (strcasecmp((string)$arg1, (string)$arg2) == 0) {
                        return $options['fn']();
                    } else if (isset($options['inverse'])) {
                        return $options['inverse']();
                    }
                  },
                  "gte" => function($arg1, $arg2, $options) {
                    if ((int)$arg1 >= (int)$arg2) {
                        return $options['fn']();
                    } else {
                        return $options['inverse']();
                    }
                  },
                  "dump" => function ($arg1) {
                    return print_r($arg1, true);
                  },
                  "escape" => function ($arg1) {
                    return rawurlencode($arg1);
                  },
                  "View::AppMatrix"
              ),
              "debug" => FALSE,
            ));
            file_put_contents($precompiled, implode('', array('<','?php',' ', $phpStr,' ','?','>'))); // so php tags are not recognised
        }
        if ($template_folder !== null) require Config::get('PATH_VIEW') . $template_folder . '/header.php';
        $renderer = include($precompiled); // so its in the lightncandy use namespace on this file
        echo $renderer($assoc);
        if ($template_folder !== null) require Config::get('PATH_VIEW') . $template_folder . '/footer.php';
    }

    /**
     * Similar to render, but accepts an array of separate views to render between the header and footer. Use like
     * the following: $this->view->renderMulti(array('help/index', 'help/banner'));
     * @param array $filenames Array of the paths of the to-be-rendered view, usually folder/file(.php) for each
     * @param array $data Data to be used in the view
     * @return bool
     */
    public function renderMulti($filenames, $data = null)
    {
        if (!is_array($filenames)) {
            self::render($filenames, $data);
            return false;
        }

        if ($data) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }

        require Config::get('PATH_VIEW') . '_templates/header.php';
        foreach($filenames as $filename) {
            require Config::get('PATH_VIEW') . $filename . '.php';
        }
        require Config::get('PATH_VIEW') . '_templates/footer.php';
    }

    /**
     * Same like render(), but does not include header and footer
     * @param string $filename Path of the to-be-rendered view, usually folder/file(.php)
     * @param mixed $data Data to be used in the view
     */
    public function renderWithoutHeaderAndFooter($filename, $data = null)
    {
        if ($data) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }

        require Config::get('PATH_VIEW') . $filename . '.php';
    }
    
    public function output($output) {
	    echo $output;
    }

    /**
     * Renders pure JSON to the browser, useful for API construction
     * @param $data
     */
    public function renderJSON($data)
    {
        header("Content-Type: application/json");
        echo json_encode($data);
    }

    /**
     * renders the feedback messages into the view
     */
    public function renderFeedbackMessages($filter = "")
    {
	    $area = Session::get("feedback_area") ?: "";
	    if ($area == $filter) {
	        // echo out the feedback messages (errors and success messages etc.),
	        // they are in $_SESSION["feedback_positive"] and $_SESSION["feedback_negative"]
	        require Config::get('PATH_VIEW') . '_templates/feedback.php';
	
	        // delete these messages (as they are not needed anymore and we want to avoid to show them twice
	        Session::set('feedback_positive', null);
	        Session::set('feedback_negative', null);

			Session::set('feedback_area', null);
        }
        
    }

    /**
     * Checks if the passed string is the currently active controller.
     * Useful for handling the navigation's active/non-active link.
     *
     * @param string $filename
     * @param string $navigation_controller
     *
     * @return bool Shows if the controller is used or not
     */
    public static function checkForActiveController($filename, $navigation_controller)
    {
        $split_filename = explode("/", $filename);
        $active_controller = $split_filename[0];

        if ($active_controller == $navigation_controller) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the passed string is the currently active controller-action (=method).
     * Useful for handling the navigation's active/non-active link.
     *
     * @param string $filename
     * @param string $navigation_action
     *
     * @return bool Shows if the action/method is used or not
     */
    public static function checkForActiveAction($filename, $navigation_action)
    {
        $split_filename = explode("/", $filename);
        $active_action = $split_filename[1];

        if ($active_action == $navigation_action) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the passed string is the currently active controller and controller-action.
     * Useful for handling the navigation's active/non-active link.
     *
     * @param string $filename
     * @param string $navigation_controller_and_action
     *
     * @return bool
     */
    public static function checkForActiveControllerAndAction($filename, $navigation_controller_and_action)
    {
        $split_filename = explode("/", $filename);
        $active_controller = $split_filename[0];
        $active_action = $split_filename[1];

        $split_filename = explode("/", $navigation_controller_and_action);
        $navigation_controller = $split_filename[0];
        $navigation_action = $split_filename[1];

        if ($active_controller == $navigation_controller AND $active_action == $navigation_action) {
            return true;
        }

        return false;
    }

    /**
     * Converts characters to HTML entities
     * This is important to avoid XSS attacks, and attempts to inject malicious code in your page.
     *
     * @param  string $str The string.
     * @return string
     */
    public function encodeHTML($str){
        return htmlentities($str, ENT_QUOTES, 'UTF-8');
    }

    /**
     * called from within a handlebars template to render a table
     a bit ugly to render, and View isn't the best place to put this function since it's rendering models,
     but we are comparing tiers to features (for an app)
    each feature requires a minimum tier level, and has a true/false label
    a user is subscribed to a tier, and we can offer upgrades to get to a higher tier
    if the user tier allows access to that app

                     tiers:  low     medium      high
    features:
        a                      no         no            yes
        b                      off         on            on
        c                      fixed     custom     custom
     */
    public static function AppMatrix($App, $AppTierFeatures, $AppTiers, $UserSubscription, $options) {

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
        foreach ($AppTierFeatures as $info) {
            $table[] = '<tr><th>' . $info["feature"] . '</th>';
            foreach ($AppTiers as $tier) {
                $tier_level = (int)$tier["tier_level"];
                $info_level = (int)$info["level"];
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
			        $user_tier_level = $UserSubscription[0]["tier"]["tier_id"];
			        if ($tier["tier_id"] == $user_tier_level) {
				        $label = 'Launch App';
				        $class_name = 'current-tier';
				        $button_url = AppModel::getLaunchUrl($App["app_id"]);
			        } else if ($tier["tier_id"] < $user_tier_level) {
				        $label = 'Downgrade';
			        } else if ($tier["tier_id"] > $user_tier_level) {
				        $label = 'Upgrade';
			        }
		        }
                $table[] = "<td><a href='$button_url' class='$class_name'>$label</a></td>";
            }
        } else {
            $table[] = "<td colspan='$colspan'><a href='" . Config::get('URL') . "login/'>Please log in to subscribe</a></td>";
        }
        $table[] = '</tr>';
        
        // caveat
        $table[] = "<tr><td></td><td colspan='$colspan'><p>This product is part of a paid subscription that offers multiple products. Subscriptions are charged monthly until cancelled. You can change your tier at any time.</p><div class='text-center'><img src='/img/fastspring.png'></div></td></tr>";
        $table[] = '</tfoot></table>';
        return implode('', $table);
    }

}
