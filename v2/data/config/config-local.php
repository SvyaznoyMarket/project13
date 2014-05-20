<?php

return function(\EnterSite\Config\Application $config) {
    /** @var \Closure $handler */
    $handler = include __DIR__ . '/config-dev.php';
    $handler($config);

    $config->hostname = 'enter.loc';

    // local config
    $config->curl->logResponse = true;
};