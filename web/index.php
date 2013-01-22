<?php

require_once __DIR__ . '/../lib/Debug/Timer.php';
\Debug\Timer::start('app');

// environment
$env = isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : 'dev';

// configuration
/** @var $config \Config\AppConfig */
$config = include realpath(__DIR__ . '/../config/config-' . $env . '.php');
if (false === $config) die(sprintf('Не удалось загрузить конфигурацию для среды "%s"', $env));

// autoload
require_once __DIR__ . '/../lib/Autoloader.php';
Autoloader::register($config->appDir);

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

// request
// TODO: придумать, как по другому можно получить имя хоста
$request = \Http\Request::createFromGlobals();

// определение флага {десктопное|мобильное приложение} на основе домена
if ($config->mobileHost && ($config->mobileHost == $request->getHttpHost())) {
    \App::$name = 'mobile';
    $config->templateDir = $config->appDir . '/mobile/template';
    $config->controllerPrefix = 'Mobile\\Controller';
}

// response
$response = null;

\App::init($env, $config, function() use (&$response) {
    $error = error_get_last();
    if ($error && (error_reporting() & $error['type'])) {

        $spend = \Debug\Timer::stop('app');
        \App::logger()->error('Fail app ' . $spend . ' ' . round(memory_get_peak_usage() / 1048576, 2) . 'Mb' . ' with error ' . json_encode($error));

        // очищаем буфер вывода
        $previous = null;
        while (($level = ob_get_level()) > 0 && $level !== $previous) {
            $previous = $level;
            ob_end_clean();
        }

        if (\App::config()->debug) {
            $action = new \Debug\ErrorAction();
            $response = $action->execute();
        } else {
            $action = new \Controller\Error\ServerErrorAction();
            $response = $action->execute();
        }
    } else {
        $spend = \Debug\Timer::stop('app');
        \App::logger()->info('End app in ' . $spend . ' used ' . round(memory_get_peak_usage() / 1048576, 2) . 'Mb');
    }

    if ($response instanceof \Http\Response) {
        if ((bool)\App::exception()->all()) {
            $response->setStatusCode(500);
            if (\App::config()->debug) {
                $action = new \Debug\ErrorAction();
                $response = $action->execute();
            }
        }

        // debug panel
        if (\App::config()->debug && !\App::request()->isXmlHttpRequest()) {
            ob_start();
            include \App::config()->dataDir . '/debug/panel.php';
            $content = ob_get_flush();
            $response->setContent($response->getContent() . "\n\n" . $content);
        }

        $response->send();
    }

    \App::logger('request_compatible')->info(\Util\RequestLogger::getInstance()->getStatistics());

    // dumps logs
    \App::shutdown();

});

\App::logger()->info('Start app in ' . \App::$env . ' env');
$requestLogger = \Util\RequestLogger::getInstance();
$requestLogger->setId(\App::$id);

// request
$request = \App::request();
// router
$router = \App::router();

try {
    $request->attributes->add($router->match($request->getPathInfo(), $request->getMethod()));
    \App::logger()->info('Match route ' . $request->attributes->get('route') . ' by ' . $request->getMethod()  . ' ' . $request->getRequestUri());

    // action resolver
    $resolver = \App::actionResolver();
    list($actionCall, $actionParams) = $resolver->getCall($request);

    /* @var $response \Http\Response */
    $response = call_user_func_array($actionCall, $actionParams);
} catch (\Exception\NotFoundException $e) {
    $action = new \Controller\Error\NotFoundAction();
    $response = $action->execute($e, $request);
} catch (\Exception\AccessDeniedException $e) {
    $action = new \Controller\Error\AccessDeniedAction();
    $response = $action->execute($e, $request);
}
