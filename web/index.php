<?php

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('main', 'dev', true);
sfContext::createInstance($configuration)->dispatch();
