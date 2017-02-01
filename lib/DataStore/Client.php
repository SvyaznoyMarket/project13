<?php

namespace DataStore;

class Client {
    /**
     * @param string $path
     * @return array|null
     * @throws \Exception
     */
    public function query($path) {
        if (strpos($path, '/') !== 0) {
            throw new \Exception('Wrong path');
        }

        $file = \App::config()->dataDir . '/data-store' . $path;
        return is_file($file) ? json_decode(file_get_contents($file), true) : null;
    }
}