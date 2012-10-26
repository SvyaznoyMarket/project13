<?php

require_once __DIR__ . '/../dark/lib/Debug/Timer.php';
\Debug\Timer::start('app');

// environment
$env = isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : 'dev';

// configuration
$configClass = ucfirst($env).'Config';
require_once __DIR__ . '/../dark/config/'.$configClass.'.php';
/** @var $config AppConfig */
$config = new $configClass;

// debug
if (isset($_GET['APPLICATION_DEBUG'])) {
    if (!empty($_GET['APPLICATION_DEBUG'])) {
        $config->debug = true;
        setcookie('debug', 1, strtotime('+7 days' ));
    } else {
        $config->debug = false;
        setcookie('debug', 0, strtotime('+7 days' ));
    }
} else if (isset($_COOKIE['debug'])) {
    $config->debug = !empty($_COOKIE['debug']);
}

// application
require_once __DIR__ . '/../dark/lib/App.php';
\App::init($config);

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

    if (false == \App::config()->debug) {
        $action = new \Controller\Error\ServerErrorAction();
        $response = $action->execute($e, $request);
    }
    else {
        $spend = \Debug\Timer::stop('app');
        \App::logger()->error('End app ' . $spend . ' ' . round(memory_get_peak_usage() / 1048576, 2) . 'Mb' . ' with ' . $e);

        \App::shutdown();

        throw $e;
    }
}
\App::logger('request_compatible')->info($requestLogger->getStatistics());

if ($response instanceof \Http\Response) {
    $response->send();
}

$spend = \Debug\Timer::stop('app');
\App::logger()->info('End app in ' . $spend . ' used ' . round(memory_get_peak_usage() / 1048576, 2) . 'Mb');

\App::shutdown();

// debug panel
if ($config->debug) {
    require \App::config()->dataDir . '/debug/panel.php';
}
