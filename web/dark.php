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

// response
$response = null;

// application
require_once __DIR__ . '/../dark/lib/App.php';
\App::init($env, $config, function() use (&$response) {
    $error = error_get_last();
    if ($error && (error_reporting() & $error['type'])) {
        $spend = \Debug\Timer::stop('app');
        \App::logger()->error('Fail app ' . $spend . ' ' . round(memory_get_peak_usage() / 1048576, 2) . 'Mb' . ' with error ' . json_encode($error));

        if (\App::config()->debug) {
            $action = new \Debug\ErrorAction();
            $response = $action->execute();
        }
    } else {
        $spend = \Debug\Timer::stop('app');
        \App::logger()->info('End app in ' . $spend . ' used ' . round(memory_get_peak_usage() / 1048576, 2) . 'Mb');
    }

    if ($response instanceof \Http\Response) {
        if (\App::$exception instanceof \Exception) {
            $response->setStatusCode(500);
            if (\App::config()->debug) {
                $action = new \Debug\ErrorAction();
                $response = $action->execute();
            }
        }

        // debug panel
        if (\App::config()->debug && !\App::request()->isXmlHttpRequest()) {
            $content = $response->getContent();
            $content .= require \App::config()->dataDir . '/debug/panel.php';
            $response->setContent($content);
        }

        $response->send();
    }

    \App::logger('request_compatible')->info(\Util\RequestLogger::getInstance()->getStatistics());

    // dumps logs
    \App::shutdown();

});

\App::logger()->info('Start app');
$requestLogger = \Util\RequestLogger::getInstance();
$requestLogger->setId(\App::$id);

// request
$request = \App::request();
// router
$router = \App::router();
$request->attributes->add($router->match($request->getPathInfo(), $request->getMethod()));
\App::logger()->info('Match route ' . $request->attributes->get('route') . ' by ' . $request->getMethod()  . ' ' . $request->getRequestUri());

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
        throw $e;
    } else {
        $action = new \Controller\Error\ServerErrorAction();
        $response = $action->execute($e, $request);
    }
}
