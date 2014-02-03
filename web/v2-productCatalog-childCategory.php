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
    $error = error_get_last();
    if ($error && (error_reporting() & $error['type'])) {
        // response.status = 500
        var_dump($error);
    }

    (new \EnterSite\Action\DumpLogger())->execute();
    (new \EnterSite\Action\Debug())->execute($response, $startAt);
});



$request = new \Enter\Http\Request(
    [
        'productCategoryPath' => 'noutbuki-i-monobloki-noutbuki-4280',
        'f-brand-apple'       => '9',
        'f-brand-htc'         => '178',
        'f-price-from'        => '800',
        'f-price-to'          => '35000',
        'sort'                => 'default-desc',
    ],
    [],
    ['geo_id' => '14974']
);

$action = new \EnterSite\Controller\ProductCatalog\ChildCategory();
$response = $action->execute($request);
echo $response->content;