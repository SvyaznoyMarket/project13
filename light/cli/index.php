<?php

namespace light;
use Logger;

ini_set('html_errors', 0);

if ('cli' !== PHP_SAPI) {
  throw new \Exception('Действие доступно только через CLI.');
}

require_once(__DIR__.'/../config/main.php');
require_once(ROOT_PATH.'lib/TimeDebug.php');
require_once(ROOT_PATH.'lib/log4php/Logger.php');
require_once(ROOT_PATH.'system/App.php');

TimeDebug::start('Total');
TimeDebug::start('Configure');


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

require_once(__DIR__."/../config/{$env}.php");

App::init();
Logger::getLogger('Settings')->debug("$env env.");

Logger::getLogger('Settings')->info('core v2 url: '.CORE_V2_USERAPI_URL);
Logger::getLogger('Settings')->info('core v1 url: '.CORE_V1_API_URL);
TimeDebug::end('Configure');

try {
  list($controller, $action) = explode('/', $path);

  require_once ROOT_PATH.'/controller/'.$controller.'.php';
  $controllerClass = '\\light\\'.$controller.'Controller';
  $controllerInstance = new $controllerClass();

  call_user_func_array(array($controllerInstance, $action), array_slice($argv, 3));
}
catch(Exception $e) {
  Logger::getRootLogger()->warn('Exception: '.$e->getMessage());

  echo "\n{$e->getMessage()}\n";
}
TimeDebug::end('Total');
Logger::getLogger('Timer')->debug(TimeDebug::getAll());