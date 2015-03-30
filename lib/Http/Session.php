<?php

namespace Http;

class Session extends \Symfony\Component\HttpFoundation\Session\Session {
    static private $started = false;

    public function start() {

        $cookieDefaults = session_get_cookie_params();

        $options = [
            'session_name'            => \App::config()->session['name'],
            'session_id'              => null,
            'auto_start'              => true,
            'session_cookie_lifetime' => is_null(\App::config()->session['cookie_lifetime'])? $cookieDefaults['lifetime'] : \App::config()->session['cookie_lifetime'],
            'session_cookie_path'     => $cookieDefaults['path'],
            'session_cookie_domain'   => \App::config()->session['cookie_domain'] ?: $cookieDefaults['domain'],
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

    public function regenerate($destroy = false, $lifetime = null) {
        if (null !== $lifetime) {
            ini_set('session.cookie_lifetime', $lifetime);
        }

        return session_regenerate_id($destroy);
    }

    public function getWithChecking($name, $default = null) {
        if (!array_key_exists($name, $_SESSION)) {
            return $default;
        }
        $_SESSION[$name]['_is_readed'] = (bool) isset($_SESSION[$name]['_is_readed']);

        return $_SESSION[$name];
    }

    /** Функция для работы с flash-сообщениями
     *  При передаче параметра устанавливает сообщение
     *  При вызове без параметра возвращает сообщение и удаляет его из сессии
     * @param mixed $data
     * @return mixed|null
     */
    public function flash($data = null) {
        if ($data !== null) {
            $this->set('message', $data);
            return null;
        } else {
            $data = $this->get('message');
            $this->remove('message');
            return $data;
        }
    }
}
