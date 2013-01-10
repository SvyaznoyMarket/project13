<?php

if ('cli' !== PHP_SAPI) {
    throw new \Exception('Действие доступно только через CLI');
}

require_once __DIR__ . '/lib/Debug/Timer.php';
\Debug\Timer::start('app');

// environment
if (!isset($argv[3])) {
    throw new \Exception\NotFoundException('Не указана среда окружения');
} else {
    $env = $argv[3];
}

// configuration
/** @var $config \Config\AppConfig */
$config = include realpath(__DIR__ . '/config/config-' . $env . '.php');
if (false === $config) die(sprintf('Не удалось загрузить конфигурацию для среды "%s"', $env));

// application
require_once __DIR__ . '/../dark/lib/App.php';
\App::init($env, $config, function() {
    if ($error = error_get_last()) {
        \App::logger()->error($error);
    }

    \App::shutdown();
});

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
        array_slice($argv, 4),
    ];
    call_user_func_array($actionCall[0], $actionCall[1]);
} catch (\Exception $e) {
    $spend = \Debug\Timer::stop('app');
    \App::logger()->error('End cli app ' . $spend . ' ' . round(memory_get_peak_usage() / 1048576, 2) . 'Mb' . ' with ' . $e);

    throw $e;
}

$spend = \Debug\Timer::stop('app');
\App::logger()->info('End cli app in ' . $spend . ' used ' . round(memory_get_peak_usage() / 1048576, 2) . 'Mb');
