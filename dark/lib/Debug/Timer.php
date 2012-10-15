<?php

namespace Debug;

class Timer {
    private static $default = array(
        'active' => false,
        'start'  => 0,
        'total'  => 0,
        'count'  => 0,
    );
    private static $instances = array();

    public static function start($name) {
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = self::$default;
        }

        self::$instances[$name]['active'] = true;
        self::$instances[$name]['start'] = microtime(true);
    }

    public static function stop($name) {
        if (!isset(self::$instances[$name]) || !self::$instances[$name]['active']) {
            return;
        }

        self::$instances[$name]['active'] = false;
        $spend = self::$instances[$name]['total'] += microtime(true) - self::$instances[$name]['start'];
        ++self::$instances[$name]['count'];

        return $spend;
    }

    public static function get($name) {
        if (!isset(self::$instances[$name])) {
            return self::$default;
        }

        return self::$instances[$name];
    }

    public static function getAll() {
        return self::$instances;
    }
}