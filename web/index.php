<?php
setlocale(LC_TIME, 'ru_RU', 'ru_RU.utf8'); // Для вывода даты на русском языке
setlocale(LC_CTYPE, 'ru_RU', 'ru_RU.utf8'); // Для правильной работы basename
set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, [
    realpath(__DIR__ . '/../v2/Enter'),
]));

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
        setcookie('debug', 1, strtotime('+14 days' ), '/', $config->session['cookie_domain']);
    } else {
        $config->debug = false;
        setcookie('debug', 0, strtotime('+14 days' ), '/', $config->session['cookie_domain']);
    }
} else if (isset($_COOKIE['debug'])) {
    $config->debug = !empty($_COOKIE['debug']);
}

// request
\Http\Request::trustProxyData();

// app name
\App::$name = isset($_SERVER['APPLICATION_NAME']) ? $_SERVER['APPLICATION_NAME'] : 'main';

// response
$response = null;

// сервис EnterLab !новинка
$GLOBALS['enter/service'] = new EnterApplication\Service();

\App::init($env, $config, function() use (&$response) {
    $request = \App::request();

    $error = error_get_last();
    if ($error && (error_reporting() & $error['type'])) {

        $spend = \Debug\Timer::stop('app');

        \App::logger()->error(
            [
                'message' => 'Fail app',
                'error'   => $error,
                'env'     => \App::$env,
                'spend'   => $spend,
                'memory'  => round(memory_get_peak_usage() / 1048576, 2) . 'Mb',
                'server'  => array_map(function($name) use (&$request) { return $request->server->get($name); }, [
                    'REQUEST_METHOD',
                    'REQUEST_URI',
                    'QUERY_STRING',
                    'HTTP_X_REQUESTED_WITH',
                    'HTTP_COOKIE',
                    'HTTP_USER_AGENT',
                    'HTTP_REFERER',
                    'REQUEST_TIME_FLOAT',
                ]),
            ],
            ['fatal']
        );

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
        \App::logger()->info(['message' => 'End app', 'env' => \App::$env, 'spend' => $spend, 'memory' => round(memory_get_peak_usage() / 1048576, 2) . 'Mb']);
    }

    if ($response instanceof \Http\Response) {
        $exceptions = \App::exception()->all();
        if ($exceptions) {
            $response->setStatusCode(500);
            if (\App::config()->debug) {
                $action = new \Debug\ErrorAction();
                $response = $action->execute();
            }
        } else {
            //\App::richRelevanceClient()->setCookie($response);
        }

        // debug panel
        if (\App::config()->debug) {
            (new \Debug\ShowAction())->execute($request, $response);
        }

        try {
            $response->headers->clearCookie('urlParams', '/');
        } catch (\Exception $e) {
            \App::logger()->error($e, ['cookie.urlParams']);
        }

        $response->send();
    }

    // dumps logs
    \App::shutdown();

});

\App::logger()->info(['message' => 'Start app', 'env' => \App::$env, 'ssi' => isset($_GET['SSI']) ? $_GET['SSI'] : false]);

// ssi
if (('/index.php' !== $_SERVER['SCRIPT_NAME']) && (0 === strpos($_SERVER['SCRIPT_NAME'], '/ssi'))) {
    // восстановление параметров родительского запроса для SSI, родительский запрос передается в headers x-uri
    if ($xUri = (isset($_SERVER['HTTP_X_URI']) ? $_SERVER['HTTP_X_URI'] : null)) {
        $queryStrPosition = strpos($xUri, '?');
        $parentQuery = substr($xUri, $queryStrPosition === false ? 0 : $queryStrPosition + 1);
        parse_str($parentQuery, $params);
        $_GET = array_merge($_GET, $params);
    }

    // request
    $request = \Http\Request::create(
        '/ssi' . (!empty($_GET['path']) ? $_GET['path'] : ''),
        'GET',
        $_GET
    );
} else {
    // request
    $request = \App::request();
}

// degradation
call_user_func(include realpath(__DIR__ . '/../config/degradation.php'), $config, $request);
if ($c->degradation) {
    \App::logger()->info(['message' => 'degradation', 'value' => $c->degradation]);
}

// router
$router = \App::router();

try {
    $request->attributes->add($router->match($request->getPathInfo(), $request->getMethod()));

    // проверка редиректа из scms
    if (!$response instanceof \Http\Response) {
        $response = (new \Controller\PreAction())->execute($request);
    }

    // если предыдущие контроллеры не вернули Response, ...
    if (!$response instanceof \Http\Response) {
        // \App::logger()->info(['message' => 'Match route', 'route' => $request->attributes->get('route'), 'uri' => $request->getRequestUri(), 'method' => $request->getMethod()], ['router']);
        \App::logger()->info(
            [
                'message' => 'Match route',
                'action' => $request->get('action'),
                'route' => $request->attributes->get('route'),
                'uri' => $request->getRequestUri(),
                'method' => $request->getMethod(),
                'query' => $request->query->all(),
                'data' => $request->request->all()
            ],
            ['router']
        );

        // action resolver
        $resolver = \App::actionResolver();
        list($actionCall, $actionParams) = $resolver->getCall($request);

        /* @var $response \Http\Response */
        $response = call_user_func_array($actionCall, $actionParams);

        //сохраняю данные для abtest
        \App::abTest()->setCookie($response);
        \App::richRelevanceClient()->setCookie($response);
    }
} catch (\Exception\NotFoundException $e) {
    \App::logger()->warn([
        'request' => [
            'uri'     => $request->getRequestUri(),
            'method'  => $request->getMethod(),
            'query'   => (array)$request->query->all(),
            'data'    => (array)$request->request->all(),
            'headers' => (array)$request->headers->all(),
        ],
    ]);

    \App::request()->attributes->set('pattern', '');
    \App::request()->attributes->set('route', '');
    \App::request()->attributes->set('action', ['Error\NotFoundAction', 'execute']);

    $action = new \Controller\Error\NotFoundAction();
    $response = $action->execute($e, $request);
} catch (\Exception\AccessDeniedException $e) {
    $action = new \Controller\Error\AccessDeniedAction();
    $response = $action->execute($e, $request);
}
