<?php

return function(\EnterSite\Config\Application $config) {
    /** @var \Closure $handler */
    $handler = include __DIR__ . '/config.php';
    $handler($config);

    // live config
    $config->debug = false;
};