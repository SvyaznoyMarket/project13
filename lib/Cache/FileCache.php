<?php

namespace Cache;

class FileCache implements CacheInterface {
    private $config = [];

    public function __construct(array $config) {
        $this->config = array_merge([
            'dataDir' => null,
        ], $config);

        if ($this->config['dataDir'] && !file_exists($this->config['dataDir'])) {
            mkdir($this->config['dataDir']);
        }
    }

    /**
     * @param string   $key
     * @param int|null $timeout
     * @return string
     */
    public function get($key, $timeout = null) {
        $file = $this->config['dataDir'] . '/' . $key;
        if (is_readable($file) && ($timeout ? ((filemtime($file) + $timeout) > microtime(true)) : true)) {
            return file_get_contents($file);
        }

        return null;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function set($key, $value) {
        file_put_contents($this->config['dataDir'] . '/' . $key, $value, LOCK_EX);
    }
}