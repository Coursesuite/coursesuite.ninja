<?php

class Config
{
    public static $config;
    private static $crud;
    private static $menu;

    public static function get($key)
    {

        if ($key === "debug") {
            return (Environment::get() === "tim");
        }

        if (!self::$config) {

            $config_file = '../application/config/config.' . Environment::get() . '.php';

            if (!file_exists($config_file)) {
                return false;
            }

            self::$config = require $config_file;
        }

        return self::$config[$key];
    }

    public static function Crud() {
        if (!self::$crud) {
            $file = '../application/config/db.json';
            if (!file_exists($file)) {
                return false;
            }
            self::$crud = json_decode(file_get_contents($file), false, JSON_NUMERIC_CHECK); // JSON_PARTIAL_OUTPUT_ON_ERROR
        }
        return self::$crud;
    }

    public static function Menu() {
        if (!self::$menu) {
            $file = '../application/config/menu.json';
            if (!file_exists($file)) {
                return false;
            }
            self::$menu = json_decode(file_get_contents($file), false, JSON_NUMERIC_CHECK); // JSON_PARTIAL_OUTPUT_ON_ERROR
        }
        return self::$menu;
    }

    public static function CustomCss() {
        $cache = CacheFactory::getFactory()->getCache();
        $cacheItem = $cache->getItem("custom_css");
        $css = $cacheItem->get();
        if (is_null($css)) {
            $css = KeyStore::find("customcss")->get();
            $cacheItem->set($css)->expiresAfter(86400); // 1 day
            $cache->save($cacheItem);
        }
        return $css;
    }
}
