<?php

namespace Logger;

class NullLogger implements LoggerInterface {
    public function __construct() {
    }

    public function debug($message) {}

    public function info($message) {}

    public function warn($message) {}

    public function error($message) {}

    public function dump() {}

    public function getMessages() {
        return [];
    }
}
