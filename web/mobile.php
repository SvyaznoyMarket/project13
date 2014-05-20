<?php

// start time
$startAt = microtime(true);

// application dir
$applicationDir = realpath(__DIR__ . '/..');

// environment
$environment = isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : 'dev';

// response
$response = null;

// debug
$debug = true === call_user_func(require $applicationDir . '/v2/config/debug.php');

// error reporting
call_user_func(require $applicationDir . '/v2/config/error-report.php', $debug);

// autoload
call_user_func(require $applicationDir . '/v2/config/autoload.php', $applicationDir);

// request
$request = new \Enter\Http\Request($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);

// config
(new \EnterSite\Action\ImportConfig())->execute($applicationDir, $applicationDir . sprintf('/config/config-%s.php', $environment));
//(new \EnterSite\Action\LoadConfig())->execute(include $applicationDir . sprintf('/v2/config/config-local.php', $environment));
//(new \EnterSite\Action\LoadCachedConfig())->execute($applicationDir . sprintf('/v2/config/config-local.php', $environment));

// config post-handler
(new \EnterSite\Action\HandleConfig())->execute($environment, $debug);

// shutdown handler, send response
(new \EnterSite\Action\RegisterShutdown())->execute($request, $response, $startAt);

// error handler
(new \EnterSite\Action\HandleError())->execute($response);

// response
(new \EnterSite\Action\HandleResponse())->execute($request, $response);