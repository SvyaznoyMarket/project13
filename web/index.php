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
\Http\Request::trustProxyData();
// TODO: придумать, как по другому можно получить имя хоста
//$request = \Http\Request::createFromGlobals(); // TODO: временно убрал проверку на мобильное приложение

// app name
\App::$name = isset($_SERVER['APPLICATION_NAME']) ? $_SERVER['APPLICATION_NAME'] : 'main';
if ('main' == \App::$name) {
    // определение флага {десктопное|мобильное приложение} на основе домена
    /* // TODO: временно убрал проверку на мобильное приложение
    if ($config->mobileHost && ($config->mobileHost == $request->getHttpHost())) {
        \App::$name = 'mobile';
        $config->templateDir = $config->appDir . '/mobile/template';
        $config->controllerPrefix = 'Mobile\\Controller';
    }
    */
} else if ('terminal' == \App::$name) {
    $request = \Http\Request::createFromGlobals();

    $clientId = $request->get('client_id') ? trim((string)$request->get('client_id')) : null;
    $shopId = $request->get('shop_id') ? trim((string)$request->get('shop_id')) : null;
    if (!$clientId) die('Не передан параметр client_id');
    if (!$shopId) die('Не передан параметр shop_id');
    $config->coreV2['client_id'] = $clientId;
    $config->region['shop_id'] = $shopId;


    $config->templateDir = $config->appDir . '/terminal/template';
    $config->controllerPrefix = 'Terminal\\Controller';
    $config->routePrefix = 'terminal';
}

// response
$response = null;

\App::init($env, $config, function() use (&$response) {
    $error = error_get_last();
    if ($error && (error_reporting() & $error['type'])) {

        $spend = \Debug\Timer::stop('app');
        \App::logger()->error('Fail app ' . $spend . ' ' . round(memory_get_peak_usage() / 1048576, 2) . 'Mb ' . \App::request()->getMethod()  . ' ' . \App::request()->getRequestUri() . ' with error ' . json_encode($error, JSON_UNESCAPED_UNICODE));

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
        } else {
            \App::partner()->set($response);
        }

        // debug panel
        if (\App::config()->debug && !$response instanceof \Http\JsonResponse && $response->getIsShowDebug()) {
            $response->setContent(
                $response->getContent()
                . "\n\n"
                . (new \Templating\PhpEngine(\App::config()->appDir . '/data'))->render('debug/panel')
            );
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

    //сохраняю данные для abtest
    \App::abTest()->setCookie($response);
} catch (\Exception\NotFoundException $e) {
    $action = new \Controller\Error\NotFoundAction();
    $response = $action->execute($e, $request);
} catch (\Exception\AccessDeniedException $e) {
    $action = new \Controller\Error\AccessDeniedAction();
    $response = $action->execute($e, $request);
}
