<?php

namespace Logger;

interface LoggerInterface {
    public function debug($message);

    public function info($message);

    public function warn($message);

    public function error($message);

    public function dump();

    public function getMessages();
}
