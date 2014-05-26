<?php

// start time
$startAt = microtime(true);

// application dir
$applicationDir = realpath(__DIR__ . '/..');

// environment
$environment = isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : 'dev';

// session settings FIXME: вынести в конфиг
ini_set('session.use_cookies', false);
ini_set('session.use_only_cookies', false);
ini_set('session.use_trans_sid', true);

// response
$response = null;

// debug
$debug = true === call_user_func(require $applicationDir . '/v2/config/terminal/debug.php');

// error reporting
call_user_func(require $applicationDir . '/v2/config/error-report.php', $debug);

// autoload
call_user_func(require $applicationDir . '/v2/config/autoload.php', $applicationDir);

// request
//$request = new \Enter\Http\Request($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
$request = new \Enter\Http\Request($_GET, $_POST, $_COOKIE, [], $_SERVER);

// exception
$error = null;

// config
//(new \EnterSite\Action\ImportConfig())->execute($applicationDir, $applicationDir . '/config/config-local.php');
(new \EnterSite\Action\LoadConfig())->execute(include $applicationDir . '/v2/config/terminal/config-local.php');
//(new \EnterSite\Action\LoadCachedConfig())->execute($applicationDir . '/v2/config/config-local.json');

// config post-handler
(new \EnterSite\Action\HandleConfig())->execute($environment, $debug);

// error handler
(new \EnterSite\Action\HandleError())->execute($error);

// shutdown handler, send response
(new \EnterTerminal\Action\RegisterShutdown())->execute($request, $response, $error, $startAt);

// response
(new \EnterTerminal\Action\HandleResponse())->execute($request, $response);
