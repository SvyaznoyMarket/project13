<?php
/**
 * содержит часть конфига, общего для продакшн и тест-сред
 */

define('ROOT_PATH', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR);
define('HELPER_PATH', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR);
define('VIEW_PATH', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR);
define('LOGGER_CONFIG_PATH', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'log4php.xml');
define('LOG_FILES_PATH', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR);
if(isset($_SERVER['HTTP_HOST'])){
  define('HTTP_HOST', $_SERVER['HTTP_HOST']);
}
else{
  define('HTTP_HOST', 'localhost'); //@TODO подумать над этим моментом
}