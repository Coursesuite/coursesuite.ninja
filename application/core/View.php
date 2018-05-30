<?php

use LightnCandy\LightnCandy;

/**
 * Class View
 * The part that handles all the output
 */
class View
{

    protected $partials = array();
    protected $tmpl = array();
    protected $helpers = array();
    protected $css = array();
    protected $js = array();
    protected $initjs = array();
    protected $page = "";
    protected $action = "";
    protected $counters = array();

    public $page_title = "";
    public $page_keywords = "";
    public $page_description = "";

    public function renderTemplates() {
        if (isset($this->tmpl)) {
            foreach ($this->tmpl as $inst) {
                if (file_exists(Config::get('PATH_VIEW') . $inst)) {
                    $id = str_replace(array('/','.'),'-',$inst);
                    echo "<script type='text/x-handlebars-template' id='$id'>" . PHP_EOL;
                    echo file_get_contents(Config::get('PATH_VIEW') . $inst) . PHP_EOL;
                    echo "</script>" . PHP_EOL;
                }
            }
        }
    }

    // $this->View->requires(anything)
    public function requires($name, $param = null)
    {
        if (strpos($name, ".css") !== false) { // e.g third-party/tool/file.css, https://cdnjs.com/something/foo.css

            if ($name[0] === "/" || strpos($name, "://") !== false) {
                $this->css[] = $name;
            } else {
                $this->css[] = "/css/$name";
            }

        } else if (strpos($name, ".js") !== false) { // e.g. third-party/jstree/treeview.js, https://cdnjs.com/something/foo.js

            if ($name === "main.js" && Config::get("debug") !== true) $name = "main." . APP_JS . ".js";
            if ($name === "admin.js" && Config::get("debug") !== true) $name = "admin." . APP_JS . ".js";
            if ($name[0] === "/" || strpos($name, "://") !== false) {
                $this->js[] = $name;
            } else {
                $this->js[] = "/js/$name";
            }

        } else if (strpos($name, "::") !== false) { // e.g. Text::Paginator

            $this->helpers[] = $name;

        } else if (strpos($name, ".hbt") !== false) { // e.g. index/container.hbt

            $this->tmpl[] = $name;

        } else if ($name === "init") {

            $this->initjs[] = $param;

        } else if (file_exists(Config::get('PATH_VIEW') . $name . '.hbp')) { // e.g. login/forgot

            $spl = explode('/', $name, 2);
            $tok = $spl[0];
            $part = str_replace('/','_',$spl[1]);
            $this->partials[$part] = file_get_contents(Config::get('PATH_VIEW') . $name . '.hbp');

        }
    }

    public function __construct($creator = null, $page = "", $action = "")
    {
        $this->SystemMessages = MessageModel::getMyUnreadMessages();
        $this->MobileDetect = new Mobile_Detect;

        $view = Config::get('PATH_VIEW');
        $this->IsMobile = $this->MobileDetect->isMobile() && !$this->MobileDetect->isTablet();
        $this->page = $page;
        $this->action = $action;
        $this->helpers = array(
            "equals" => function ($arg1, $arg2, $options) {
                if (strcasecmp((string) $arg1, (string) $arg2) == 0) {
                    return $options['fn']();
                } else if (isset($options['inverse'])) {
                    return $options['inverse']();
                }
            },
            "contains" => function ($arg1, $arg2, $options) {
                if (strpos((string) $arg1, (string) $arg2) !== false) {
                    return $options['fn']();
                } else if (isset($options['inverse'])) {
                    return $options['inverse']();
                }
            },
            "not" => function ($arg1, $arg2, $options) {
                if (strcasecmp((string) $arg1, (string) $arg2) == 0) {
                    return $options['inverse']();
                } else if (isset($options['inverse'])) {
                    return $options['fn']();
                }
            },
            "gte" => function ($arg1, $arg2, $options) {
                if ((int) $arg1 >= (int) $arg2) {
                    return $options['fn']();
                } else if (isset($options['inverse'])) {
                    return $options['inverse']();
                }
            },
            "isin" => function ($arg1, $arg2, $options) {
                if (!is_array($arg2)) $arg2 = explode(',', $arg2);
                // var_dump($arg1); var_dump($arg2); var_dump(in_array($arg1, $arg2));
                if (in_array($arg1, $arg2)) {
                    return $options['fn']();
                } else if (isset($options['inverse'])) {
                    return $options['inverse']();
                }
            },
            "either" => function ($arg1, $arg2, $options) {
                $bool = (!empty($arg1) || !empty($arg2));
                if ($bool) {
                    return $options['fn']();
                } else if (isset($options['inverse'])) {
                    return $options['inverse']();
                }
            },
            "replace" => function ($replace_this, $with_that, $in_this) {
                return str_replace($replace_this, $with_that, $in_this);
            },
            "vardump" => function ($arg1) {
                var_dump($arg1);
                return "";
            },
            "dump" => function ($arg1, $pre = false) {
                if (filter_var($pre, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === true) return "<pre>" . print_r($arg1, true) . "</pre>";
                return print_r($arg1, true);
            },
            "escape" => function ($arg1) {
                return rawurlencode($arg1);
            },
            "htmlify" => function ($arg1) {
                return Text::toHtml($arg1);
            },
            "textify" => function ($arg1) {
                return Text::toText($arg1);
            },
            "jsonformat" => function ($arg1) {
                $json = json_decode($arg1);
                return json_encode($json, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);
            },
            "stringify" => function($obj, $pretty = false) {
                $params = JSON_NUMERIC_CHECK; // | JSON_PRETTY_PRINT
                return json_encode($obj, $params);
            },
            "typeof" => function ($arg1) {
                return gettype($arg1);
            },
            "idify" => function ($arg1) {
                return preg_replace('/[^a-zA_Z0-9]/', '_', strtolower($arg1));
            },
            // "iswhitelabelled" => function ($org_id, $app_key, $options) {
            //     $obj = OrgModel::getRecord($org_id);
            //     $html = json_decode($obj->header);
            //     if (isset($html) && array_key_exists($app_key, $html)) {  // "docninja" => "some html code"
            //         return $options['fn']();
            //     } elseif (isset($options['inverse'])) {
            //         return $options['inverse']();
            //     }
            // },
            "hasmorethan" => function ($arg1, $arg2, $options) {
                // var_dump(gettype($arg1), gettype($arg2));
                if (count($arg1) > $arg2) {
                    return $options['fn']();
                } elseif (isset($options['inverse'])) {
                    return $options['inverse']();
                }
            },
            "count" => function ($arg1) {
                return count($arg1);
            },
            "length" => function ($arg1, $inc) {
                $len = count($arg1);
                return $len + $inc;
            },
            "concat" => function ($a, $b) {
                return $a . $b;
            },
            "add" => function ($arg1, $arg2) {
                return (int) $arg1 + (int) $arg2;
            },
            "minus" => function ($arg1, $arg2) {
                return (int) $arg1 - (int) $arg2;
            },
            "cint" => function ($arg1) {
                return (int) $arg1;
            },
            "decodable" => function ($arg1, $options) {
                $bool = false;
                $value = "no-value";
                $options['data']['flange'] = 'bush';
                if (!is_null($arg1)) {
                    $value = Encryption::decrypt(Text::base64dec($arg1));
                    $options['data']['decoded'] = $value; // inject private variable @decoded ; https://github.com/zordius/lightncandy
                    $bool = true;
                }
                if ($bool) {
                    return $options['fn']($value);
                } elseif (isset($options['inverse'])) {
                    return $options['inverse']($value);
                }
            },
            "decode" => function ($arg1) {
                if (empty($arg1)) return "";
                return Encryption::decrypt(Text::base64dec($arg1));
            },
            "encode" => function ($arg1) {
                if (empty($arg1)) return "";
                return Text::base64enc(Encryption::encrypt($arg1));
            },
            // "Contact_Form_Placeholder" => function ($options) {
            //     return file_get_contents($view . '_templates/contact_form_placeholder.html');
            // },
            "plural" => function ($arg1, $options) {
                if (count($arg1) > 1) {
                    return $options['fn']();
                } elseif (isset($options['inverse'])) {
                    return $options['inverse']();
                }
            },
            "morethanone" => function ($obj, $property, $options) { // does a pool of objects contain more than one with a property set?
                $bool = false;
                if (property_exists((object) $obj[0], $property)) {
                    $count = 0;
                    foreach ($obj as $instance) {
                        if (!empty($instance[$property])) { $count++; }
                    }
                    $bool = ($count > 1);
                }
                if ($bool === true) {
                    return $options['fn']();
                } elseif (isset($options['inverse'])) {
                    return $options['inverse']();
                }
            },
            "tweetable" => function ($string1, $string2) {
                $remaining = 140 - 27 - strlen($string2); // tweet length minus shortened url length (guess) minus title of page (social platform copies that in automatically)
                return trim(substr($string1, 0, $remaining - 3)) . "...";
            },
            // "productDescription" => function ($productId) {
            //     return (new ProductModel($productId))->get_description();
            // },
            "date" => function ($arg1) {
                // http://php.net/manual/en/function.date.php
                date_default_timezone_set('UTC');
               return date("jS M Y", strtotime($arg1));
            },
            "datetime" => function ($arg1) {
                date_default_timezone_set('UTC');
               return date("jS M Y h:ia", strtotime($arg1));
            },
            "tsdatetime" => function ($arg1) {
                date_default_timezone_set('UTC');
               return date("jS M Y h:ia", $arg1);
           },
           "utc" => function ($arg1) {
                date_default_timezone_set('UTC');
                return gmdate("Y-m-d\TH:i:s\Z", strtotime($arg1));
           },
            "cheapest" => function ($appId) {
                $database = DatabaseFactory::getFactory()->getConnection();
                $query = $database->prepare("select price from product p inner join app_tiers t on p.entity_id = t.id where p.entity = 'app_tiers' and t.app_id = :appid limit 1");
                $query->execute(array(":appid" => $appId));
                return "$" . floor($query->fetchColumn(0));
            },
            "thumbnail" => function ($path, $width) {
                return Config::get("URL") . "content/image/" . Text::base64_urlencode($path) . "/$width";
            },
            "trim" => function ($content) {
                return trim($content);
            },
            "tierlevelname" => function ($atl) {
                return $atl;
            },
            "random" => function ($haystack) {
                $stack = explode(',', $haystack);
                return $stack[array_rand($stack)];
            },
            "uniq" => function ($value) {
                return hash('crc32b', $value);
            },
            "cookie" => function ($arg1) {
                return Request::cookie($arg1);
            },
            "ucfirst" => function ($string) {
                return ucfirst($string);
            },
            "humandate" => function ($time, $suffix) {
                $time = time() - $time; // to get the time since that moment
                $time = ($time<1)? 1 : $time;
                $tokens = array (
                    31536000 => 'year',
                    2592000 => 'month',
                    604800 => 'week',
                    86400 => 'day',
                    3600 => 'hour',
                    60 => 'minute',
                    1 => 'second'
                );

                foreach ($tokens as $unit => $text) {
                    if ($time < $unit) continue;
                    $numberOfUnits = floor($time / $unit);
                    return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'') . ' ' . $suffix;
                }
            },
            "humanprice" => function ($value, $suffix = ".00") {
                $price = explode('.',$value);
                return "$<span>{$price[0]}</span><sup>{$suffix}</sup>";
            },
            "calcprice" => function ($model) {
                $price = $model["price"];
                if (substr($price,-3)===".00") $price = substr($price, 0, -3);
                $period = "m";
                if (strpos((string) $model["product_key"], "api-") !== false && substr($model["product_key"],-1) === "3") $period = "q";
                return "$<span>{$price}</span><sup>/{$period}</sup>";
            },
            "statusReasonText" => function ($value) {
                if (empty($value)) return;
                switch ($value) {
                    case "canceled":
                    case "cancelled":
                        return "cancelled";
                        break;
                    case "canceled-non-payment":
                        return "declined";
                        break;
                    case "expired":
                        return "expired";
                        break;
                }
                return $value;
            },
            "statusReasonClass" => function ($subscription_row) {
                $prefix = "";
                $result = "";
                if (isset($subscription_row["email"]) && !isset($subscription_row["apikey"])) return "";
                if ($subscription_row["status"] === "active") {
                    $result = "success";
                }
                if (isset($subscription_row["ended"]) && $subscription_row["ended"] === true) {
                    $result = "warning";
                }
                switch ($subscription_row["statusReason"]) {
                    case "canceled":
                    case "cancelled":
                        $result = "warning";
                        break;
                    case "canceled-non-payment":
                        $result = "danger";
                        break;
                    case "expired":
                        $result = "warning";
                        break;
                }
                return $prefix . $result;
            },
            "is_even" => function ($arg1, $options) {
                $bool = (intval($arg1) % 2 === 0);
                if ($bool) {
                    return $options['fn']();
                } elseif (isset($options['inverse'])) {
                    return $options['inverse']();
                }
            },
            'englishify' => function ($arg1) {
                return ucwords(implode(' ', explode('_', $arg1)));
            },
            "dbLookup" => function ($table, $label, $key, $where, $options) {
                return Model::Read($table, $where, [], [$label,$key]);
            },
            "dbProperty" => function ($object, $propertyname) {
                $o = (object) $object;
                return $o->$propertyname;
            },
            "renderCrudTable" => function($context) {
                $selection = $context["selection"];
                $source = $context["crud"][$selection];
                $table = $context["table"];
                $idname = $source["key"];
                $link = ($source["crud"] !== false);
                $url = $context["baseurl"] . "admin/crud/{$selection}";
                $cols = 0;
                $results = [];
                $results[] = "<div class='uk-overflow-auto'>";
                $results[] = "<table class='uk-table uk-table-striped uk-table-small uk-table-justify'><thead><tr>";
                foreach ($table[0] as $key=>$value) {
                    $results[] = "<th>$key</th>";
                    $cols++;
                }
                $results[] = "</thead><tbody>";
                foreach($table as $row) {
                    $results[] = "<tr>";
                    foreach($row as $key=>$value) {
                        if ($link && $idname===$key) {
                            $results[] = "<td class='uk-table-link'><a href='{$url}/{$value}/edit'>$value</a></td>";
                        } else {
                            $results[] = "<td>$value</td>";
                        }
                    }
                    $results[] = "</tr>";
                }
                $results[] = "</tbody>";
                if ($link) {
                    $results[] = "<tfoot><tr>";
                    $results[] = "<td class='uk-table-link' colspan='{$cols}'><a href='{$url}/0/add'>Add record</a></td>";
                    $results[] = "</tr></tfoot>";
                }
                $results[] = "</table></div>";
                return implode('',$results);
            },
            "array_item" => function ($array, $index) {
                if (is_array($array) && isset($array[$index])) return $array[$index];
            },

            // I hate to muck about in globals, but this function gets copied to the output file so you can't reference class variables, and counters need to exist across function calls
            "counter_add" => function ($name) {
                if (!isset($GLOBALS["counters"])) $GLOBALS["counters"] = array();
                if (!isset($GLOBALS["counters"][$name])) {
                    $GLOBALS["counters"][$name] = 1;
                } else {
                    $GLOBALS["counters"][$name] = intval($GLOBALS["counters"][$name],10) + 1;
                }
            },
            "counter_get" => function ($name) {
                if (!isset($GLOBALS["counters"])) return 0;
                if (!isset($GLOBALS["counters"][$name])) return 0;
                return intval($GLOBALS["counters"][$name],10);
            },
            "counter_reset" => function ($name) {
                if (!isset($GLOBALS["counters"])) $GLOBALS["counters"] = array();
                unset($GLOBALS['counters'][$name]);
            },
            "helpdesk_auth_token" => function ($ticket) {
                // matches osticket -> class.client.php line 84
                $authtoken = sprintf('%s%dx%s', 'o', 1, Base32::encode(pack('VV',$ticket["user_id"], $ticket["ticket_id"])));
                $authtoken .= substr(base64_encode(md5($ticket["user_id"].$ticket["created"].$ticket["ticket_id"].Config::get("OST_SECRET_SALT"), true)), 8);
                return urlencode($authtoken);
            },
            "strlen" => function ($string) {
                return strlen($string);
            }
        );

        if (!Session::userIsLoggedIn()) {
            $this->Registration = RegistrationModel::get_page_model();
        }

        $this->helpers[] = "Text::StaticPageRenderer";

    }

    /* For parsing strings instead of files
    usage:
    $render = $this->View->prepareString($template);
    $render(array('key'=>'value'));
    */
    public function prepareString($template) {
        $compiled = LightnCandy::compile($template);
        return LightnCandy::prepare($compiled);
    }

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

    public function page() {
        return $this->page;
    }

    public function breadcrumb() {
        global $PAGE;
        $url = array_slice(explode('/', $_SERVER['REQUEST_URI']),1);
        $results = [["route"=>"", "label"=>"Home"]];
        switch ($this->page) {
            case "pricing";
                $results[] = ["route"=>"pricing", "label"=>"Pricing","link"=>true];
                break;
            case "content":
                $results[] = ["route"=>implode('/',$url), "label"=> ucfirst($url[1]) ];
                break;
            case "products":
                $results[] = ["route"=>"products", "label"=>"Products" ];
                if (isset($url[1]) && !empty($url[1])) {
                    $results[] = ["route"=>implode('/',array_slice($url,0,2)), "label"=> SectionsModel::label_for_route($url[1]) ];
                }
                if (isset($url[2]) && !empty($url[2])) {
                    $results[] = ["route"=>false,"label"=>AppModel::app_name_for_key($url[2])];
                }
                break;
            case "blog":
                $results[] = ["route"=>"blog", "label"=>"Blog" ];
                if (isset($url[1]) && !empty($url[1])) {
                    $results[] = ["route"=>false, "label"=> BlogModel::blog_entry_name($url[1]) ];
                }
                break;
            case "me":
                $results[] = ["route"=>"me", "label"=>"My account" ];
                if (isset($url[1]) && !empty($url[1])) {
                    $results[] = ["route"=>implode('/',array_slice($url,0,2)), "label"=> $url[1] ];
                }
                $results[] = ["route"=>false, "label"=>$PAGE->user_email];
        }
        return $results;
    }

    public function renderHandlebars($filename, $data = null, $template_folder = false, $force = null, $return = false)
    {

        if (is_null($force)) $force = Config::get("FORCE_HANDLEBARS_COMPILATION");

        $hashname = md5($filename) . "_" . str_replace("/","_", $filename);

        if ($template_folder === false) $template_folder = "_templates";
        if (is_array($data)) $data = (object) $data;
        if (is_null($data)) $data = new stdClass();

        $data->baseurl = Config::get("URL");
        $data->sheets = $this->css;
        $data->scripts = $this->js;
        $data->page = $this->page;
        $data->action = $this->action;
        $data->IsMobile = $this->IsMobile;

        if (isset($this->page_title) && !empty($this->page_title)) $data->meta_title = $this->page_title;
        if (isset($this->page_keywords) && !empty($this->page_keywords)) $data->meta_keywords = $this->page_keywords;
        if (isset($this->page_description) && !empty($this->page_description)) $data->meta_description = $this->page_description;


        $precompiled = Config::get('PATH_VIEW_PRECOMPILED') . $hashname . '.php';
        $assoc = json_decode(json_encode($data), true); // data is now an associative array

        // if (!array_key_exists("Feedback", $assoc)) {
        //     $assoc["Feedback"] = array(
        //         "positive" => Session::get('feedback_positive'),
        //         "negative" => Session::get('feedback_negative'),
        //         "area" => Session::get('feedback_area')
        //     );
        // }

        $assoc["feedback_area"] = Session::get("feedback_area");

        $feedback_positive = Session::get("feedback_positive");
        if (!array_key_exists("feedback_positive", $assoc) && !empty($feedback_positive)) {
            $assoc["feedback_positive"] = $feedback_positive;
        }

        $feedback_negative = Session::get("feedback_negative");
        if (!array_key_exists("feedback_negative", $assoc) && !empty($feedback_negative)) {
            $assoc["feedback_negative"] = $feedback_negative;
        }

        $feedback_meh = Session::get("feedback_meh");
        if (!array_key_exists("feedback_meh", $assoc) && !empty($feedback_meh)) {
            $assoc["feedback_meh"] = $feedback_meh;
        }

        $feedback_intermediate = Session::get("feedback_intermediate");
        if (!array_key_exists("feedback_intermediate", $assoc) && !empty($feedback_intermediate)) {
            $assoc["feedback_intermediate"] = $feedback_intermediate;
        }

        // expose data so header and footer can also pick it up (includes, not handlebars templates)
        if ($data) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }

        // if we have already compiled this page, don't compile it again unless being forced to
        if (!file_exists($precompiled) || $force == true) {

            $template = file_get_contents(Config::get('PATH_VIEW') . $filename . '.hba');
            $phpStr = LightnCandy::compile($template, array(
                "flags" => LightnCandy::FLAG_PARENT | LightnCandy::FLAG_ADVARNAME | LightnCandy::FLAG_HANDLEBARS | LightnCandy::FLAG_RENDER_DEBUG,
                "helpers" => $this->helpers,
                "debug" => Config::get('debug') && false,
                "partials" => $this->partials,
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
        if (!isset($phpStr) || strlen($phpStr) === 0) {
            echo "error in compiler - " . $filename;
            if (Config::get('debug')) {
                var_dump($this);
            }
        } else {
            echo $renderer($assoc);
        }

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

    public function output($output)
    {
        echo $output;
    }

    /**
     * Renders pure JSON to the browser, useful for API construction
     * @param $data
     */
    public function renderJSON($data, $prettify = false, $numeric = true)
    {
        header("Content-Type: application/json");
        $options = 0;
        if ($numeric) $options = $options | JSON_NUMERIC_CHECK;
        if ($prettify) $options = $options | JSON_PRETTY_PRINT;
        echo json_encode($data, $options);
    }

    /**
     * renders the feedback messages into the view
     */
    public function renderFeedbackMessages($filter = "")
    {
        $area = Session::get("feedback_area") ?: "";
        if ($area == $filter) {
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
        Session::set('feedback_meh', null);
        Session::set('feedback_intermediate', null);
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
