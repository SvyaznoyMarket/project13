<?php

namespace Enter\Http;

class Cookie {
    /** @var string */
    public $name;
    /** @var string */
    public $value;
    /** @var \DateTime */
    public $expiredAt;
    /** @var string */
    public $domain;
    /** @var string */
    public $path;
    /** @var bool */
    public $isSecure;
    /** @var bool */
    public $isHttpOnly;

    /**
     * @param $name
     * @param null $value
     * @param int $expire
     * @param string $path
     * @param null $domain
     * @param bool $isSecure
     * @param bool $isHttpOnly
     * @throws \InvalidArgumentException
     */
    public function __construct($name, $value = null, $expire = 0, $path = '/', $domain = null, $isSecure = false, $isHttpOnly = true) {
        // from PHP source code
        if (preg_match("/[=,; \t\r\n\013\014]/", $name)) {
            throw new \InvalidArgumentException(sprintf('The cookie name %s contains invalid characters', $name));
        }

        if (empty($name)) {
            throw new \InvalidArgumentException('The cookie name cannot be empty');
        }

        // convert expiration time to a Unix timestamp
        if ($expire instanceof \DateTime) {
            $expire = $expire->format('U');
        } elseif (!is_numeric($expire)) {
            $expire = strtotime($expire);

            if (false === $expire || -1 === $expire) {
                throw new \InvalidArgumentException('The cookie expiration time is not valid');
            }
        }

        $this->name = $name;
        $this->value = $value;
        $this->domain = $domain;
        $this->expiredAt = $expire;
        $this->path = empty($path) ? '/' : $path;
        $this->isSecure = (bool)$isSecure;
        $this->isHttpOnly = (bool)$isHttpOnly;
    }

    /**
     * @return string
     */
    public function __toString() {
        $return = urlencode($this->name) . '=';

        if ('' === (string)$this->value) {
            $return .= 'deleted; expires=' . gmdate("D, d-M-Y H:i:s T", time() - 31536001);
        } else {
            $return .= urlencode($this->value);

            if ($this->expiredAt !== 0) {
                $return .= '; expires=' . gmdate("D, d-M-Y H:i:s T", $this->expiredAt);
            }
        }

        if ($this->path) {
            $return .= '; path='.$this->path;
        }

        if ($this->domain) {
            $return .= '; domain=' . $this->domain;
        }

        if (true === $this->isSecure) {
            $return .= '; secure';
        }

        if (true === $this->isHttpOnly) {
            $return .= '; httponly';
        }

        return $return;
    }
}