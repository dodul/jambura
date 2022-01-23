<?php
class jTestCache {
    private static $cache = [];
    private static $tb = 'global';
    private static $pointer = [];

    public static function add($identifier, $data) {
        self::$cache[self::$tb][$identifier][] = $data;
    }

    public static function setTB($tbName) {
        self::$tb = $tbName;
    }

    public static function get($tb, $identifier) {
        if (!isset(self::$cache[$tb][$identifier])) {
            return false;
        }

        if (!isset(self::$pointer[$tb][$identifier])) {
            self::$pointer[$tb][$identifier] = 0;
        }

        $pointer = self::$pointer[$tb][$identifier]++;
        return self::$cache[$tb][$identifier][$pointer];
    }
}
