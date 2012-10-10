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

// resolver
$resolver = new \Routing\ActionResolver();
list($actionCall, $actionParams) = $resolver->getCall($request);

// response
$response = null;
try {
    /* @var $response \Http\Response */
    $response = call_user_func_array($actionCall, $actionParams);
} catch (\Routing\Exception $e) {
    throw $e;
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
        \App::logger()->error('End app ' . $spend . ' with ' . $e);

        throw $e;
    }
}

if ($response instanceof \Http\Response) {
    $response->send();
}

$spend = \Debug\Timer::stop('app');
\App::logger()->info('End app ' . $spend);

\App::shutdown();

// debug panel
$timers = \Debug\Timer::getAll();
if ($config->debug && !$request->isXmlHttpRequest()) {
    echo '<pre draggable="true" ondblclick="$(this).remove()" style="position: fixed; top: 14px; left: 2px; width: 200px; overflow: hidden; z-index: 999; background: #000000; color: #00ff00; opacity: 0.8; padding: 2px 5px; border-radius: 5px; font-size: 10px; font-family: Courier New; box-shadow: 0 0 10px rgba(0,0,0,0.5);">'
        . 'env: ' . $env . '<br />'
        . 'act: ' . (isset($actionCall[0]) ? (str_replace('Controller\\', '', get_class($actionCall[0])) . '.' . $actionCall[1]) : '') . '<br />'
        . ($response && (200 != $response->getStatusCode()) ? ('status: <span style="color: #ff0000;">' . $response->getStatusCode() . '</span><br />') : '')
        . '<br />'
        . 'app: ' . round($timers['app']['total']
            - (isset($timers['core']) ? $timers['core']['total'] : 0)
            - (isset($timers['content']) ? $timers['content']['total'] : 0)
        , 3).' s<br />'
        . 'core: ' . (isset($timers['core']) ? sprintf('%s s [%s]', round($timers['core']['total'], 3), $timers['core']['count']) : '~')
        . '<br />'
        . 'content: ' . (isset($timers['content']) ? sprintf('%s s [%s]', round($timers['content']['total'], 3), $timers['content']['count']) : '~')
        . '<br />'
        . '<br />'
        . 'total: ' . round($timers['app']['total'], 3).' s<br />'
        . 'memory: ' . round(memory_get_peak_usage() / 1048576, 2). ' Mb'
    .'</pre>';
}