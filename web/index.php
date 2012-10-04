<?php

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$env = isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : 'prod';

$debug = false;
if (isset($_GET['APPLICATION_DEBUG'])) {
    if ($_GET['APPLICATION_DEBUG']) {
        $debug = true;
        setcookie('APPLICATION_DEBUG', 1, time() + 60 * 60 * 24 * 7);
    } else {
        setcookie('APPLICATION_DEBUG', null);
    }
} else if (isset($_COOKIE['APPLICATION_DEBUG'])) {
    $debug = true;
}

if (!in_array($env, array('live', 'prod'))) {
  $env = 'prod';
}

$configuration = ProjectConfiguration::getApplicationConfiguration('main', $env, $debug);
sfContext::createInstance($configuration)->dispatch();
