<?php

// this check prevents access to debug front controllers that are deployed by accident to production servers.
// feel free to remove this, extend it or make something more sophisticated.
if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1')))
{
  //die('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$env = isset($_ENV['APPLICATION_ENV']) ? $_ENV['APPLICATION_ENV'] : 'dev';

switch ($env) {
  case 'live':
    $env .= '_dev';
    break;
  default:
    $env = 'dev';
    break;
}

$configuration = ProjectConfiguration::getApplicationConfiguration('main', $env, true);
sfContext::createInstance($configuration)->dispatch();
