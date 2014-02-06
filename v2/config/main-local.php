<?php

return function(\EnterSite\Config\Application $config) {
    /** @var \Closure $handler */
    $handler = include __DIR__ . '/main.php';
    $handler($config);

    // local config
};