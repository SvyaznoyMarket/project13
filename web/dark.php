<?php

require_once __DIR__ . '/../dark/lib/Debug/Timer.php';
\Debug\Timer::start('app');

// environment
$env = isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : 'dev';

// configuration
/** @var $config \Config\AppConfig */
$config = include realpath(__DIR__ . '/../dark/config/config-' . $env . '.php');
if (false === $config) die(sprintf('Не удалось загрузить конфигурацию для среды "%s"', $env));

// debug
if (isset($_GET['APPLICATION_DEBUG'])) {
    if (!empty($_GET['APPLICATION_DEBUG'])) {
        $config->debug = true;
        setcookie('debug', 1, strtotime('+7 days' ), '/');
    } else {
        $config->debug = false;
        setcookie('debug', 0, strtotime('+7 days' ), '/');
    }
} else if (isset($_COOKIE['debug'])) {
    $config->debug = !empty($_COOKIE['debug']);
}

// application
require_once __DIR__ . '/../dark/lib/App.php';
\App::init($env, $config);

\App::logger()->info('Start app');
$requestLogger = \Util\RequestLogger::getInstance();
$requestLogger->setId(\App::$id);

// request
$request = \App::request();
// router
$router = \App::router();
$request->attributes->add($router->match($request->getPathInfo(), $request->getMethod()));
\App::logger()->info('Match route ' . $request->attributes->get('route') . ' by ' . $request->getMethod()  . ' ' . $request->getRequestUri());

// response
$response = null;
try {
    // resolver
    $resolver = new \Routing\ActionResolver();
    list($actionCall, $actionParams) = $resolver->getCall($request);

    /* @var $response \Http\Response */
    $response = call_user_func_array($actionCall, $actionParams);
} catch (\Exception\NotFoundException $e) {
    $action = new \Controller\Error\NotFoundAction();
    $response = $action->execute($e, $request);
} catch (\Exception\AccessDeniedException $e) {
    $action = new \Controller\Error\AccessDeniedAction();
    $response = $action->execute($e, $request);
} catch (\Exception $e) {
    \App::logger()->error(array(
        'message'   => 'Ошибка сервера.',
        'exception' => (string)$e,
    ));

    if (\App::config()->debug) {
        $spend = \Debug\Timer::stop('app');
        \App::logger()->error('End app ' . $spend . ' ' . round(memory_get_peak_usage() / 1048576, 2) . 'Mb' . ' with ' . $e);

        throw $e;
    }
    else {
        $action = new \Controller\Error\ServerErrorAction();
        $response = $action->execute($e, $request);
    }
}
if ($response instanceof \Http\Response) {
    $response->send();
}

\App::logger('request_compatible')->info($requestLogger->getStatistics());

$spend = \Debug\Timer::stop('app');
\App::logger()->info('End app in ' . $spend . ' used ' . round(memory_get_peak_usage() / 1048576, 2) . 'Mb');

// debug panel
if ($config->debug) {
    require \App::config()->dataDir . '/debug/panel.php';
}
