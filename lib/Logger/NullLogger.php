<?php

namespace Logger;

class NullLogger implements LoggerInterface {
    public function __construct() {
    }

    public function debug($message, array $tags = []) {}

    public function info($message, array $tags = []) {}

    public function warn($message, array $tags = []) {}

    public function error($message, array $tags = []) {}

    public function dump() {}

    public function getMessages() {
        return [];
    }
}
