<?php
// environment
$env = 'dev';

// configuration
/** @var $config \Config\AppConfig */
$config = include realpath(__DIR__ . '/../config/config-' . $env . '.php');
if (false === $config) die(sprintf('Не удалось загрузить конфигурацию для среды "%s"', $env));

// autoload
require_once __DIR__ . '/../lib/Autoloader.php';
Autoloader::register($config->appDir);

\App::init($env, $config);