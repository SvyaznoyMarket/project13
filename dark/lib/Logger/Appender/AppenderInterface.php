<?php

namespace Logger\Appender;

interface AppenderInterface {
    public function dump($messages);
}
