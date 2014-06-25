<?php

// start time
$startAt = microtime(true);

// application dir
$applicationDir = realpath(__DIR__ . '/..');

// environment
$environment = isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : 'live';

// response
$response = null;

// debug
$debug = call_user_func(require $applicationDir . '/v2/config/debug.php');

// error reporting
call_user_func(require $applicationDir . '/v2/config/error-report.php', $debug);

// autoload
call_user_func(require $applicationDir . '/v2/config/autoload.php', $applicationDir);

// request
$request = new \Enter\Http\Request($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);

// exception
$error = null;

// config
(new \EnterSite\Action\ImportConfig())->execute($applicationDir, $applicationDir . sprintf('/config/config-%s.php', $environment));
//(new \EnterSite\Action\LoadConfig())->execute(include $applicationDir . sprintf('/v2/config/config-%s.php', $environment));
//(new \EnterSite\Action\LoadCachedConfig())->execute($applicationDir . sprintf('/v2/config/config-%s.json', $environment));

// config post-handler
(new \EnterSite\Action\HandleConfig())->execute($environment, $debug);

// error handler
(new \EnterSite\Action\HandleError())->execute($error);

// shutdown handler, send response
(new \EnterSite\Action\RegisterShutdown())->execute($request, $response, $error, $startAt);

// response
(new \EnterSite\Action\HandleResponse())->execute($request, $response);