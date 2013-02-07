<?php

namespace Http;

class Session implements \Http\SessionInterface {
    static private $started = false;

    public function start() {
        $cookieDefaults = session_get_cookie_params();

        $options = [
            'session_name'            => \App::config()->session['name'],
            'session_id'              => null,
            'auto_start'              => true,
            'session_cookie_lifetime' => is_null(\App::config()->session['cookie_lifetime'])? $cookieDefaults['lifetime'] : \App::config()->session['cookie_lifetime'],
            'session_cookie_path'     => $cookieDefaults['path'],
            'session_cookie_domain'   => $cookieDefaults['domain'],
            'session_cookie_secure'   => $cookieDefaults['secure'],
            'session_cookie_httponly' => isset($cookieDefaults['httponly']) ? $cookieDefaults['httponly'] : false,
            'session_cache_limiter'   => null,
        ];


        // set session name
        $sessionName = $options['session_name'];
        session_name($sessionName);

        $lifetime = $options['session_cookie_lifetime'];
        $path     = $options['session_cookie_path'];
        $domain   = $options['session_cookie_domain'];
        $secure   = $options['session_cookie_secure'];
        $httpOnly = $options['session_cookie_httponly'];
        session_set_cookie_params($lifetime, $path, $domain, $secure, $httpOnly);

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
        $_SESSION = [];
    }
}
