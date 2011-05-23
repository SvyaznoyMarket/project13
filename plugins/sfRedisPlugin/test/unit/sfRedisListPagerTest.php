<?php

/**
 * sfRedis tests.
 */
include dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(28, new lime_output_color());

// setup
sfConfig::set('sf_logging_enabled', false);

// ->initialize()
$t->diag('->initialize()');

$kname = 'sfredistestpager';

$client = sfRedis::getClient();

$client->del($kname);

for ($_i = 1; $_i <= 100; $_i++)
{
  $client->rpush($kname, 'val'.$_i);
}

$pager = new sfRedisListPager($kname, 10);
$pager->setPage(1);
$pager->init();

$t->is($pager->count(), 100, '::count()');
$t->is($pager->getObjectByCursor(0), 'val1', '::getObjectByCursor()');
$t->is($pager->getObjectByCursor(1), 'val1', '::getObjectByCursor()');
$t->is($pager->getObjectByCursor(50), 'val50', '::getObjectByCursor()');
$t->is($pager->getObjectByCursor(100), 'val100', '::getObjectByCursor()');
$t->is($pager->getObjectByCursor(101), 'val100', '::getObjectByCursor()');

$_i = 0;
foreach ($pager as $result)
{
  $_i++;
  $t->is($result, 'val'.$_i, 'loop #'.$_i);
}
$t->is($_i, 10, 'loop count');

$pager->setPage(10);
$pager->init();

$_i = 0;
foreach ($pager as $result)
{
  $_i++;
  $t->is($result, 'val'.(90 + $_i), 'loop #'.$_i);
}
$t->is($_i, 10, 'loop count');

// cleanup
$client->del($kname);

