<?php

DEFINE('AUTH_TYPE_TOKEN', 0);
DEFINE('AUTH_TYPE_NONE', 1);
DEFINE('AUTH_TYPE_DIGEST', 2);

DEFINE('MESSAGE_LEVEL_HAPPY', 2);
DEFINE('MESSAGE_LEVEL_MEH', 1);
DEFINE('MESSAGE_LEVEL_SAD', 0);

DEFINE('USER_TYPE_STANDARD', 1);
DEFINE('USER_TYPE_TRIAL', 3);
DEFINE('USER_TYPE_ADMIN', 7);

DEFINE("APP_CSS", "/css/compiled.20190918130519.css");
DEFINE("APP_JS", "/js/main.20190918130519.js");
DEFINE("ADMIN_CSS", "/css/admin.20190918130519.css");
DEFINE("ADMIN_JS", "/js/admin.20190918130519.js");

date_default_timezone_set('Australia/Sydney');

/**
 * Class Application
 * The heart of the application
 */
class Application
{
    /** @var mixed Instance of the controller */
    private $controller;

    /** @var array URL parameters, will be passed to used controller-method */
    private $parameters = array();

    /** @var string Just the name of the controller, useful for checks inside the view ("where am I ?") */
    private $controller_name;

    /** @var string Just the name of the controller's method, useful for checks inside the view ("where am I ?") */
    private $action_name;

    protected $PAGE;
    private $mem;

    /**
     * Start the application, analyze URL elements, call according controller/method or relocate to fallback location
     */
    public function __construct()
    {
         // $this->mem = memory_get_usage();
        $cacheConfig = [
            "host" => Config::get('REDIS_HOST'),
            "port" => (int) Config::get('REDIS_PORT'),
            "password" => Config::get('REDIS_PASS'),
            "database" => (int) Config::get('REDIS_DATABASE'),
            "timeout" => 30
        ];
        \phpFastCache\CacheManager::setDefaultConfig($cacheConfig);
        // $cacheConfig = array(
        //     "storage" => "redis",
        //     "redis" => array(
        //         "host" => Config::get('REDIS_HOST'),
        //         "port" => Config::get('REDIS_PORT'),
        //         "password" => "",
        //         "database" => (int) Config::get('REDIS_DATABASE'),
        //         "timeout" => ""
        //     )
        // );
        // \phpFastCache\CacheManager::setDefaultConfig($cacheConfig);

        // create array with URL parts in $url
        $this->splitUrl();

        // creates controller and action names (from URL input)
        $this->createControllerAndActionNames();

        // does such a controller exist ?
        if (file_exists(Config::get('PATH_CONTROLLER') . $this->controller_name . '.php')) {

            // there's no "router" as such to this framework,
            // it works as className/methodName and subsequent items are send to the method as parameters
            // so store/info/docninja/ becomes store::info(docninja)
            // When we have a non-standard route
            // we have to handle it here by modifying the action_name and parameters objects.

            if (in_array($this->controller_name, Config::get("DISABLED_CONTROLLERS"))) {
                require Config::get('PATH_CONTROLLER') . 'ErrorController.php';
                $this->controller = new ErrorController;
                $this->controller->error404();
                die();
            }

            switch ($this->controller_name) {
                case "ContentController": // content/foo becomes content/index (foo), but content/image/foo stays content/image/foo .. hmm
                    if ($this->action_name !== "image") {
                        $this->parameters = array($this->action_name);
                        $this->action_name = "index";
                    }
                    break;

                case "ProductsController":
                case "LaunchController": // launch/app/param1/param2 becomes launch/index (app, param1, param2)
                case "BlogController":
                    $params = $this->parameters;
                    array_unshift($params, $this->action_name);
                    $this->action_name = "index";
                    $this->parameters = $params;

            }
            // load this file and create this controller
            // example: if controller would be "car", then this line would translate into: $this->car = new car();
            require Config::get('PATH_CONTROLLER') . $this->controller_name . '.php';
            $this->controller = new $this->controller_name($this->action_name, $this->parameters); // pass in action name so the constructor can use it, constructor ok if param passed and not handled

            // check for method: does such a method exist in the controller ?
            if (method_exists($this->controller, $this->action_name)) {
                if (!empty($this->parameters)) {
                    // call the method and pass arguments to it
                    call_user_func_array(array($this->controller, $this->action_name), $this->parameters);
                } else {
                    // if no parameters are given, just call the method without parameters, like $this->index->index();
                    $this->controller->{$this->action_name}();
                }
            } else {
                // load 404 error page
                require Config::get('PATH_CONTROLLER') . 'ErrorController.php';
                $this->controller = new ErrorController;
                $this->controller->error404();
            }
        } else {
            // load 404 error page
            require Config::get('PATH_CONTROLLER') . 'ErrorController.php';
            $this->controller = new ErrorController;
            $this->controller->error404();
        }

    }
    public function override_action($action)
    {
        $action_name = $action;
    }

    /**
     * Get and split the URL
     */
    private function splitUrl()
    {
        if (Request::get('url')) {

            // split URL
            $url = trim(Request::real_get('url'), '/'); // real_get replaces space with +, since $_GET urldecodes then converts plus to space automatically, which invalidates the base64 string
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);

            // put URL parts into according properties
            $this->controller_name = isset($url[0]) ? $url[0] : null;
            $this->action_name = isset($url[1]) ? $url[1] : null;

            // remove controller name and action name from the split URL
            unset($url[0], $url[1]);

            // rebase array keys and store the URL parameters
            $this->parameters = array_values($url);
        }
    }

    /**
     * Checks if controller and action names are given. If not, default values are put into the properties.
     * Also renames controller to usable name.
     */
    private function createControllerAndActionNames()
    {
        // check for controller: no controller given ? then make controller = default controller (from config)
        if (!$this->controller_name) {
            $this->controller_name = Config::get('DEFAULT_CONTROLLER');
        }

        // check for action: no action given ? then make action = default action (from config)
        if (!$this->action_name or (strlen($this->action_name) == 0)) {
            $this->action_name = Config::get('DEFAULT_ACTION');
        }

        // rename controller name to real controller class/file name ("index" to "IndexController")
        $this->controller_name = ucwords($this->controller_name) . 'Controller';
    }

    function __destruct() {
         // $used = round((memory_get_usage() - $this->mem) / 1024 / 1024, 2);
         //  echo "Memory used: $used M";
    }
}
