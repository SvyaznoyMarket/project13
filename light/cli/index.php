<?php

namespace light;
use Logger;

ini_set('html_errors', 0);

if ('cli' !== PHP_SAPI) {
  throw new \Exception('Действие доступно только через CLI.');
}

require dirname(__FILE__) . '/../lib/Config.php';

$path = isset($argv[1]) ? trim($argv[1], '/') : null;
$env = isset($argv[2]) ? trim($argv[2]) : null;

if (!$path) {
  throw new \LogicException('Пустой путь. Путь должен быть вида "controller/action"');
}
if (false === strpos($path, '/')) {
  throw new \LogicException('Не указано действие.');
}
if (!$env) {
  throw new \LogicException('Не указана среда. Например, prod, dev или loc.');
}

$parameterList = require_once(__DIR__."/../config/{$env}.php");

Config::init($parameterList);


//require_once(__DIR__.'/../config/main.php');
require_once(Config::get('rootPath').'lib/TimeDebug.php');
require_once(Config::get('rootPath').'lib/log4php/Logger.php');
require_once(Config::get('rootPath').'system/App.php');

TimeDebug::start('Total');
TimeDebug::start('Configure');



App::init();
Logger::getLogger('Settings')->debug("$env env.");

Logger::getLogger('Settings')->info('core v2 url: '.Config::get('coreV2UserAPIUrl'));
Logger::getLogger('Settings')->info('core v1 url: '.Config::get('coreV1APIUrl'));
TimeDebug::end('Configure');

list($controller, $action) = explode('/', $path);

require_once Config::get('rootPath').'/controller/'.$controller.'.php';
$controllerClass = '\\light\\'.$controller.'Controller';
$controllerInstance = new $controllerClass();

call_user_func_array(array($controllerInstance, $action), array_slice($argv, 3));


TimeDebug::end('Total');
Logger::getLogger('Timer')->debug(TimeDebug::getAll());