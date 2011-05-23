<?php

/**
 * sfRedis tests.
 */
include dirname(__FILE__).'/../bootstrap/unit.php';
require_once $_SERVER['SYMFONY'].'/../test/unit/cache/sfCacheDriverTests.class.php';

$t = new lime_test(64, new lime_output_color());

try
{
  new sfRedisCache;
}
catch (sfInitializationException $e)
{
  $t->skip($e->getMessage(), $plan);
  return;
}

// setup
sfConfig::set('sf_logging_enabled', false);

// ->initialize()
$t->diag('->initialize()');
$cache = new sfRedisCache;
$cache->initialize();

sfCacheDriverTests::launch($t, $cache);

