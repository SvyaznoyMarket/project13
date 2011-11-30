<?php


require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('main', 'alpha', false);
sfContext::createInstance($configuration)->dispatch();
