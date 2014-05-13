<?php

return function(\EnterTerminal\Config\Application $config) {
    /** @var \Closure $handler */
    $handler = include __DIR__ . '/config.php';
    $handler($config);

    // dev config
};