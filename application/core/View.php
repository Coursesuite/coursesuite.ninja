<?php

use LightnCandy\LightnCandy;

/**
 * Class View
 * The part that handles all the output
 */
class View
{

    public function __construct()
    {
        $this->SystemMessages = MessageModel::getMyUnreadMessages();
    }

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
    }

    public function renderWithTemplate($template_folder, $filename, $data = null)
    {
        if ($data) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }
        require Config::get('PATH_VIEW') . $template_folder . '/header.php';
        require Config::get('PATH_VIEW') . $filename . '.php';
        require Config::get('PATH_VIEW') . $template_folder . '/footer.php';
    }

    public function renderHandlebars($filename, $data = null, $template_folder = false, $force = false, $return = false)
    {

        $hashname = md5($filename);

        if ($template_folder === false) {
            $template_folder = "_templates";
        }

        $precompiled = Config::get('PATH_VIEW_PRECOMPILED') . $hashname . '.php';
        $assoc = json_decode(json_encode($data), true); // data is now an associative array

        // expose data so header and footer can also pick it up (includes, not handlebars tempaltes)
        if ($data) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }

        if (!file_exists($precompiled) || $force == true) {
            // if we have already compiled this page, don't compile it again unless being forced to

            $template = file_get_contents(Config::get('PATH_VIEW') . $filename . '.hba');

            $helper_functions = array(
                "equals" => function ($arg1, $arg2, $options) {
                    if (strcasecmp((string) $arg1, (string) $arg2) == 0) {
                        return $options['fn']();
                    } else if (isset($options['inverse'])) {
                        return $options['inverse']();
                    }
                },
                "gte" => function ($arg1, $arg2, $options) {
                    if ((int) $arg1 >= (int) $arg2) {
                        return $options['fn']();
                    } else if (isset($options['inverse'])) {
                        return $options['inverse']();
                    }
                },
                "dump" => function ($arg1) {
                    return print_r($arg1, true);
                },
                "escape" => function ($arg1) {
                    return rawurlencode($arg1);
                },
                "htmlify" => function ($arg1) {
                    return Text::toHtml($arg1);
                },
                "jsonformat" => function ($arg1) {
                    $json = json_decode($arg1);
                    return json_encode($json, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);
                },
                "typeof" => function ($arg1) {
                    return gettype($arg1);
                },
                "idify" => function ($arg1) {
                    return preg_replace('/[^a-zA_Z0-9]/', '_', strtolower($arg1));
                },
            );

            // extend helpers by adding methods when particular controllers are loaded.
            // should kinda be a lib core class, but seems to work this way
            if (class_exists("StoreController")) {
                $helper_functions[] = "Store::AppMatrix";
                $helper_functions[] = "Store::TierMatrix";
                $helper_functions[] = "Store::ContactForm";
            }
            $helper_functions[] = "Text::StaticPageRenderer";
            $phpStr = LightnCandy::compile($template, array(
                "flags" => LightnCandy::FLAG_PARENT | LightnCandy::FLAG_ADVARNAME | LightnCandy::FLAG_HANDLEBARS, //  | LightnCandy::FLAG_RENDER_DEBUG | LightnCandy::FLAG_STANDALONEPHP | LightnCandy::FLAG_ERROR_LOG,
                "helpers" => $helper_functions,
                "debug" => false,
            ));
            file_put_contents($precompiled, implode('', array('<', '?php', ' ', $phpStr, ' ', '?', '>'))); // so php tags are not recognised
        }

        $buffer = "";

        if ($return === true) {
            // capture all outputs
            $buffer = ob_start();
        }

        if ($template_folder !== null) {
            require Config::get('PATH_VIEW') . $template_folder . '/header.php';
        }

        $renderer = include $precompiled; // so its in the lightncandy use namespace on this file
        echo $renderer($assoc);
        if ($template_folder !== null) {
            require Config::get('PATH_VIEW') . $template_folder . '/footer.php';
        }

        if ($return === true) {
            $buffer = ob_get_contents();
            ob_end_clean();
        }

        self::destroyFeedbackMessages();

        return $buffer;

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
        foreach ($filenames as $filename) {
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

    public function output($output)
    {
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

            self::destroyFeedbackMessages();

        }

    }

    public static function destroyFeedbackMessages()
    {
        // delete these messages (as they are not needed anymore and we want to avoid to show them twice
        Session::set('feedback_positive', null);
        Session::set('feedback_negative', null);
        Session::set('feedback_area', null);
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

        if ($active_controller == $navigation_controller and $active_action == $navigation_action) {
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
    public function encodeHTML($str)
    {
        return htmlentities($str, ENT_QUOTES, 'UTF-8');
    }

}
