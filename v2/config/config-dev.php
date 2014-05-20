<?php

return function(\EnterSite\Config\Application $config) {
    /** @var \Closure $handler */
    $handler = include __DIR__ . '/config.php';
    $handler($config);

    // dev config
    $config->debug = true;
};