<?php
namespace light;
/**
 * содержит часть конфига, общего для продакшн и тест-сред
 */

$env = isset($_ENV['APPLICATION_ENV']) ? $_ENV['APPLICATION_ENV'] : 'dev';
switch ($env) {
  case 'live':
    $env .= '_dev';
    break;
  default:
    $env = 'dev';
    break;
}
define('APP_ENV', $env);

define('ROOT_PATH', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR);
define('HELPER_PATH', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR);
define('VIEW_PATH', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR);
define('LOGGER_CONFIG_PATH', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'log4php.xml');
define('LOG_FILES_PATH', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR);
define('SESSION_NAME', 'enter');
define('SESSION_COOKIE_LIFETIME', Null); //Если Null - то используются настройки php

define('DEFAULT_PAGE_TITLE', 'You can Enter');
define('DEFAULT_PAGE_DESCRIPTION', 'Enter - новый способ покупать. Любой из 20000 товаров нашего ассортимента можно купить где угодно, как угодно и когда угодно. Наша миссия: дарить время для настоящего. Честно. С любовью. Как для себя.');

if(isset($_SERVER['HTTP_HOST'])){
  define('HTTP_HOST', $_SERVER['HTTP_HOST']);
}
else{
  define('HTTP_HOST', 'localhost'); //@TODO подумать над этим моментом
}

define('BANNER_IMAGE_URL', 'http://fs01.enter.ru/4/1/');
define('BANNER_TIMEOUT', 6000);