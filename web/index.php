<?php

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$env = isset($_ENV['APPLICATION_ENV']) ? $_ENV['APPLICATION_ENV'] : 'prod';

if (!in_array($env, array('live', 'prod'))) {
  $env = 'prod';
}

$configuration = ProjectConfiguration::getApplicationConfiguration('main', $env, false);
sfContext::createInstance($configuration)->dispatch();
