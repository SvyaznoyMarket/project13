<?php

namespace EnterSite\Curl\Query;

class Url {
    /**
     * scheme + host + path
     * @var string
     */
    public $prefix;
    /** @var string */
    public $path;
    /**
     * после знака вопроса ?
     * @var array
     */
    public $query = [];

    /**
     * @return string
     */
    public function __toString() {
        return $this->prefix . $this->path . ((bool)$this->query ? ('?' . http_build_query($this->query)) : '');
    }
}
