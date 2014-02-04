<?php

$response = null;
$applicationDir = realpath(__DIR__ . '/..');
$startAt = microtime(true);

// error reporting
error_reporting(-1);
ini_set('display_errors', true); // ini_set('display_errors', false);
ini_set('log_errors', true);
ini_set('error_log', $applicationDir . '/log/php-error.log');
ini_set('ignore_repeated_source', false);
ini_set('ignore_repeated_errors', true);

// autoload
set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, [
    realpath(__DIR__ . '/../v2/lib'),
]));
spl_autoload_register(function ($class) {
    if ($class[0] === '\\') {
        $class = substr($class, 1);
    }

    if (
        (0 !== strpos($class, 'Enter'))
        && (0 !== strpos($class, 'EnterSite'))
    ) {
        return;
    }

    //echo str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php' . PHP_EOL;

    include_once str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
});

// shutdown handler
register_shutdown_function(function () use (&$response, &$startAt) {
    if (!$response instanceof \Enter\Http\Response) {
        $response = new \Enter\Http\Response();
    }

    $error = error_get_last();
    if ($error && (error_reporting() & $error['type'])) {
        $response->statusCode = \Enter\Http\Response::STATUS_INTERNAL_SERVER_ERROR;
    }

    (new \EnterSite\Action\DumpLogger())->execute();
    $endAt = microtime(true);
    (new \EnterSite\Action\TimerDebug())->execute($response, $startAt, $endAt);
    //(new \EnterSite\Action\Debug())->execute($response, $startAt, $endAt);

    $response->send();
});

// error handler
set_error_handler(function($code, $message, $file, $line) use (&$response) {
    switch ($code) {
        case E_USER_ERROR:
            if ($response instanceof \Enter\Http\Response) {
                $response->statusCode = \Enter\Http\Response::STATUS_INTERNAL_SERVER_ERROR;
            }

            return true;
    }

    return false;
});



$request = new \Enter\Http\Request(
    [
        'productCategoryPath' => 'electronics/telefoni-897',
        'f-brand-apple'       => '9',
        'f-brand-htc'         => '178',
        'f-price-from'        => '3000',
        'f-price-to'          => '50000',
        'sort'                => 'default-desc',
    ],
    [],
    ['geo_id' => '14974']
);

$action = new \EnterSite\Controller\ProductCatalog\ChildCategory();
$response = $action->execute($request);