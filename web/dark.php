<?php

require_once __DIR__ . '/../dark/lib/Debug/Timer.php';
\Debug\Timer::start('app');

// environment
$env = isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : 'live';

// configuration
$configClass = ucfirst($env).'Config';
require_once __DIR__ . '/../dark/config/'.$configClass.'.php';
/** @var $config AppConfig */
$config = new $configClass;

// debug
if (isset($_GET['APPLICATION_DEBUG'])) {
    if ($_GET['APPLICATION_DEBUG']) {
        $config->debug = true;
        setcookie('APPLICATION_DEBUG', 1, time() + 60 * 60 * 24 * 7);
    } else {
        setcookie('APPLICATION_DEBUG', null);
    }
} else if (isset($_COOKIE['APPLICATION_DEBUG'])) {
    $config->debug = true;
}

// application
require_once __DIR__ . '/../dark/lib/App.php';
\App::init($config);

\App::logger()->info('Start app');

// request
$request = \App::request();
// router
$router = \App::router();
$request->attributes->add($router->match($request->getPathInfo(), $request->getMethod()));
\App::logger()->info('Match route ' . $request->attributes->get('route') . ' by uri ' . $request->getRequestUri());

// resolver
$resolver = new \Routing\ActionResolver();
list($actionCall, $actionParams) = $resolver->getCall($request);

// response
$response = null;
try {
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

    \App::shutdown();

    if (false == \App::config()->debug) {
        $action = new \Controller\Error\ServerErrorAction();
        $response = $action->execute($e, $request);
    }
    else {
        $spend = \Debug\Timer::stop('app');
        \App::logger()->error('End app ' . $spend . ' ' . round(memory_get_peak_usage() / 1048576, 2) . 'Mb' . ' with ' . $e);

        throw $e;
    }
}

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
