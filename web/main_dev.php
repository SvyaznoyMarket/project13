<?php
session_start();

$enter_code = 'welcome';
// this check prevents access to debug front controllers that are deployed by accident to production servers.
// feel free to remove this, extend it or make something more sophisticated.
if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1')))
{
  //die('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

if (isset($_POST['enter_code']) && !strcmp($_POST['enter_code'], $enter_code))
{
  $_SESSION['enter_code'] = $enter_code;
  header('Location: http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
  die();
}

if (!isset($_SESSION['enter_code']) || strcmp($_SESSION['enter_code'], 'welcome'))
{
  require_once('enter.html');
  die();
}

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('main', 'dev', true);
sfContext::createInstance($configuration)->dispatch();
