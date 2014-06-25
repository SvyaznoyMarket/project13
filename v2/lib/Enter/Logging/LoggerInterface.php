<?php

namespace Enter\Logging;

interface LoggerInterface {
    /**
     * @param AppenderInterface[] $appenders
     * @param array $types
     * @param array $parameters
     */
    public function __construct(array $appenders, array $types = null, array $parameters = []);

    /**
     * @param array $message
     * @return void
     */
    public function push(array $message);

    /**
     * @return void
     */
    public function dump();
}