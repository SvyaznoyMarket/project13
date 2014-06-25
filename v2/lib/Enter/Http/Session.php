<?php

namespace Enter\Http;

use Enter\Http\Session\Config;

class Session {
    /** @var Config */
    private $config;
    /** @var bool */
    static private $started = false;

    /**
     * @param Config $config
     */
    public function __construct(Config $config) {
        $this->config = $config;
    }

    public function start() {
        $cookieDefaults = session_get_cookie_params();

        $options = [
            'session_name'            => $this->config->name,
            'session_id'              => null,
            'auto_start'              => true,
            'session_cookie_lifetime' => (null === $this->config->cookieLifetime) ? $cookieDefaults['lifetime'] : $this->config->cookieLifetime,
            'session_cookie_path'     => $cookieDefaults['path'],
            'session_cookie_domain'   => $this->config->cookieDomain ?: $cookieDefaults['domain'],
            'session_cookie_secure'   => $cookieDefaults['secure'],
            'session_cookie_httponly' => isset($cookieDefaults['httponly']) ? $cookieDefaults['httponly'] : false,
            'session_cache_limiter'   => null,
        ];

        session_name($options['session_name']);

        session_set_cookie_params(
            $options['session_cookie_lifetime'],
            $options['session_cookie_path'],
            $options['session_cookie_domain'],
            $options['session_cookie_secure'],
            $options['session_cookie_httponly']
        );

        if (!self::$started) {
            if (!session_start()) {
                throw new \RuntimeException('Не удалось стартовать сессию');
            }

            self::$started = true;
        }
    }

    /**
     * @return bool
     */
    public function isStarted() {
        return self::$started;
    }

    /**
     * @param $value
     */
    public function setId($value) {
        session_id($value);
    }

    /**
     * @return string
     */
    public function getId() {
        return session_id();
    }

    /**
     * @param $value
     */
    public function setName($value) {
        session_name($value);
    }

    /**
     * @return string
     */
    public function getName() {
        return session_name();
    }

    /**
     * @param bool $destroy
     * @param int|null $lifetime
     * @return bool
     */
    public function regenerate($destroy = false, $lifetime = null) {
        if (null !== $lifetime) {
            ini_set('session.cookie_lifetime', $lifetime);
        }

        return session_regenerate_id($destroy);
    }

    /**
     * @param $name
     * @param $value
     */
    public function set($name, $value) {
        $_SESSION[$name] = $value;
    }

    /**
     * @return array
     */
    public function all() {
        return $_SESSION;
    }

    /**
     * @param $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null) {
        return array_key_exists($name, $_SESSION) ? $_SESSION[$name] : $default;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name) {
        return array_key_exists($name, $_SESSION);
    }

    /**
     * @param $name
     */
    public function remove($name) {
        if (isset($_SESSION[$name])) unset($_SESSION[$name]);
    }

    public function clear() {
        $_SESSION = [];
    }
}
