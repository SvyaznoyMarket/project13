<?php

namespace Http;

class Session implements \Http\SessionInterface {
    static private $started = false;

    public function start() {
        session_name(SESSION_NAME);

        if (!self::$started) {
            if (!session_start()) {
                throw new \RuntimeException('Не удалось стартовать сессию.');
            }

            self::$started = true;
        }
    }

    public function isStarted() {
        return self::$started;
    }

    public function setId($value) {
        session_id($value);
    }

    public function getId() {
        return session_id();
    }

    public function setName($value) {
        session_name($value);
    }

    public function getName() {
        return session_name();
    }

    public function regenerate($destroy = false, $lifetime = null) {
        if (null !== $lifetime) {
            ini_set('session.cookie_lifetime', $lifetime);
        }

        return session_regenerate_id($destroy);
    }

    public function set($name, $value) {
        $_SESSION[$name] = $value;
    }

    public function all() {
        return $_SESSION;
    }

    public function get($name, $default = null) {
        return array_key_exists($name, $_SESSION) ? $_SESSION[$name] : $default;
    }

    public function has($name) {
        return array_key_exists($name, $_SESSION);
    }

    public function remove($name) {
        if (isset($_SESSION[$name])) unset($_SESSION[$name]);
    }

    public function clear() {
        $_SESSION = array();
    }
}
