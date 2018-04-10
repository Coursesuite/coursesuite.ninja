<?php
class CacheFactory {
    private static $factory;
    private $cache;

    public static function getFactory() {
        if (!self::$factory) {
            self::$factory = new CacheFactory();
        }
        return self::$factory;
    }

    public function __destruct() {
        $this->cache = null;
        self::$factory = null;
    }

    public function getCache() {
        if (!$this->cache) {
            $this->cache = \phpFastCache\CacheManager::getInstance('redis');
        }
        return $this->cache;
    }
}
