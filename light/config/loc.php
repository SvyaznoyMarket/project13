<?php
namespace light;

ini_set('display_errors', 1);
ini_set('html_errors', 1);

error_reporting(E_ALL);

$baseConfig = require 'dev.php';

$developmentConfig = array(
  'db' => array(
    'host' => 'localhost',
    'name' => 'enter',
    'user' => 'root',
    'password' => 'qazwsxedc'
  )
);

return array_merge($baseConfig, $developmentConfig);