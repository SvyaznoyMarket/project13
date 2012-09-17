<?php
namespace light;

define('LOG_FILES_PATH', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR);

return array(
    'rootPath' => realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR,
    'helperPath' => realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR,
    'viewPath' => realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR,
    'loggerConfigPath' => realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'log4php.xml',
    'logFilesPath' => realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR,
    'sessionName' => 'enter',
    'sessionCookieLifeTime' => Null,
    'defaultPageTitle' => 'You can Enter',
    'defaultPageDescription' => 'Enter - новый способ покупать. Любой из 20000 товаров нашего ассортимента можно купить где угодно, как угодно и когда угодно. Наша миссия: дарить время для настоящего. Честно. С любовью. Как для себя.',
    'bannerImageUrl' => 'http://fs01.enter.ru/4/1/',
    'bannerTimeout' => 6000,
    'servicePhotoUrlList' => array(
        'http://fs01.enter.ru/11/1/500/',
        'http://fs01.enter.ru/11/1/160/',
        'http://fs01.enter.ru/11/1/120/'
    ),
    'queuePidFile' => (sys_get_temp_dir() ?: '/tmp').'/enter-queue.pid',
    'queueWorkerLimit' => 10,
    'queueMaxLockTime' => 600,
    'debug' => False
);