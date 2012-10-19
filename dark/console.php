<?php

if ('cli' !== PHP_SAPI) {
    throw new \Exception('Действие доступно только через CLI');
}

require_once __DIR__ . '/lib/Debug/Timer.php';
\Debug\Timer::start('app');

// environment
$env = isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : 'live';

// configuration
$configClass = ucfirst($env).'Config';
require_once __DIR__ . '/config/'.$configClass.'.php';
/** @var $config AppConfig */
$config = new $configClass;

// application
require_once __DIR__ . '/../dark/lib/App.php';
\App::init($config);

\App::logger()->info('Start cli app');

try {
    if (!isset($argv[1])) {
        throw new \Exception\NotFoundException('Пустой контроллер. Выполните ' . basename(__FILE__) . ' с параметром "help"');
    }
    if ('help' == $argv[1]) {
        echo 'Например, "' . basename(__FILE__) . ' Import/RegionAction execute valueOfParam1" выполнит "(new \\Controller\\Import\\RegionAction())->execute(valueOfParam1)"' . "\n";
        return;
    }
    if (!isset($argv[2])) {
        throw new \Exception\NotFoundException('Пустое действие');
    }

    $class = new \ReflectionClass('\\Controller\\' . str_replace('/', '\\', trim($argv[1], '/')));
    $action = $argv[2];
    $controller = $class->newInstanceArgs();

    $actionCall = [
        [$controller, $action],
        array_slice($argv, 3),
    ];
    call_user_func_array($actionCall[0], $actionCall[1]);
} catch (\Exception $e) {
    $spend = \Debug\Timer::stop('app');
    \App::logger()->error('End cli app ' . $spend . ' ' . round(memory_get_peak_usage() / 1048576, 2) . 'Mb' . ' with ' . $e);

    \App::shutdown();

    throw $e;
}

$spend = \Debug\Timer::stop('app');
\App::logger()->info('End cli app in ' . $spend . ' used ' . round(memory_get_peak_usage() / 1048576, 2) . 'Mb');

\App::shutdown();
