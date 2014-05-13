<?php

return function(\EnterTerminal\Config\Application $config) {
    /** @var \Closure $handler */
    $handler = include __DIR__ . '/config-dev.php';
    $handler($config);

    // local config
    $config->hostname = 't.enter.loc';
};