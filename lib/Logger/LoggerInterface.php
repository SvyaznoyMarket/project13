<?php

namespace Logger;

interface LoggerInterface {
    public function debug($message, array $tags = []);

    public function info($message, array $tags = []);

    public function warn($message, array $tags = []);

    public function error($message, array $tags = []);

    public function dump();

    public function getMessages();
}
