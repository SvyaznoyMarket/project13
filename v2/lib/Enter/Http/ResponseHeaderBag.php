<?php

namespace Enter\Http;

class ResponseHeaderBag implements \ArrayAccess, \IteratorAggregate, \Countable {
    use BagTrait;

    /**
     * @var Cookie[]
     */
    protected $cookies = [];

    public function __construct(array $container = []) {
        $this->container = $container;
    }

    /**
     * @param Cookie $cookie
     */
    public function setCookie(Cookie $cookie) {
        $this->cookies[$cookie->domain][$cookie->path][$cookie->name] = $cookie;
    }

    /**
     * @param $name
     * @param string $path
     * @param string|null $domain
     */
    public function removeCookie($name, $path = '/', $domain = null) {
        if (null === $path) {
            $path = '/';
        }

        unset($this->cookies[$domain][$path][$name]);

        if (empty($this->cookies[$domain][$path])) {
            unset($this->cookies[$domain][$path]);

            if (empty($this->cookies[$domain])) {
                unset($this->cookies[$domain]);
            }
        }
    }

    /**
     * @return Cookie[]
     */
    public function getCookies() {
        $return = [];
        foreach ($this->cookies as $path) {
            foreach ($path as $cookies) {
                foreach ($cookies as $cookie) {
                    $return[] = $cookie;
                }
            }
        }

        return $return;
    }

    /**
     * @param $name
     * @param string $path
     * @param string|null $domain
     */
    public function clearCookie($name, $path = '/', $domain = null) {
        $this->setCookie(new Cookie($name, null, 1, $path, $domain));
    }
}