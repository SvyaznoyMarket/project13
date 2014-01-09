<?php

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
    realpath(__DIR__ . '/../v2'),
    realpath(__DIR__ . '/../lib'),
]));
spl_autoload_register(function ($class) {
    //echo $class, PHP_EOL;
    include_once str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
});

// shutdown handler
register_shutdown_function(function () use (&$startAt) {
    $error = error_get_last();
    if ($error && (error_reporting() & $error['type'])) {
        var_dump($error);
    }

    echo PHP_EOL . round(microtime(true) - $startAt, 3) . ' ms' . PHP_EOL;
});



$request = new \Enter\Http\Request(
    [
        'productCategoryPath' => 'noutbuki-i-monobloki-noutbuki-4280',
        'f-brand-hp'          => '540',
        'f-price-from'        => '30000',
        'f-price-to'          => '50000',
        //'sort'                => 'default-desc',
    ],
    [],
    ['geo_id' => '14974']
);

$action = new \Enter\Site\Action\ProductCatalog\ChildCategory();
$action->execute($request);