<?php

session_start();

$enter_code = 'welcome';

if (isset($_POST['enter_code']) && !strcmp($_POST['enter_code'], $enter_code))
{
  $_SESSION['enter_code'] = $enter_code;
  header('Location: '.$_SERVER['REFERER']);
  die();
}

if (!isset($_SESSION['enter_code']) || strcmp($_SESSION['enter_code'], 'welcome'))
{
  require_once('enter.html');
  die();
}

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('main', 'prod', false);
sfContext::createInstance($configuration)->dispatch();
