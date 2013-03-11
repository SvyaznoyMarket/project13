<?php

namespace Cache;

interface CacheInterface {
    public function get($key, $timeout = null);
    public function set($key, $value);
}