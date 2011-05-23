<?php

/**
 * sfRedis tests.
 */
include dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(26, new lime_output_color());

// setup
sfConfig::set('sf_logging_enabled', false);

// ->initialize()
$t->diag('->initialize()');

$kname = 'sfredistestpager';

$client = sfRedis::getClient();

$client->del($kname);

$films = array(
  'L\'arnacoeur'                       => 2010,
  'Dans Paris'                         => 2006,
  'De battre, mon coeur s\'est arrêté' => 2005,
  'Osmose'                             => 2004,
  'Arsène Lupin'                       => 2004,
  'L\'auberge espagnole'               => 2002,
  'Peut-être'                          => 1999,
  'Dobermann'                          => 1997,
  'Le péril jeune'                     => 1995,
);

foreach ($films as $film => $annee)
{
  $client->zadd($kname, $annee, $film);
}

$pager = new sfRedisZsetPager($kname, 3);
$pager->setPage(1);
$pager->init();

$t->is($pager->count(), 9, '::count()');
$t->is($pager->getObjectByCursor(0), 'Le péril jeune', '::getObjectByCursor()');
$t->is($pager->getObjectByCursor(1), 'Le péril jeune', '::getObjectByCursor()');
$t->is($pager->getObjectByCursor(5), 'Arsène Lupin', '::getObjectByCursor()');
$t->is($pager->getObjectByCursor(9), 'L\'arnacoeur', '::getObjectByCursor()');
$t->is($pager->getObjectByCursor(10), 'L\'arnacoeur', '::getObjectByCursor()');

$page_1 = array('Le péril jeune', 'Dobermann', 'Peut-être');

$_i = 0;
foreach ($pager as $result)
{
  $_i++;
  $t->is($result, $page_1[$_i - 1], 'loop #'.$_i);
}
$t->is($_i, 3, 'loop count');

$pager->setPage(3);
$pager->init();

$page_3 = array('De battre, mon coeur s\'est arrêté', 'Dans Paris', 'L\'arnacoeur');

$_i = 0;
foreach ($pager as $result)
{
  $_i++;
  $t->is($result, $page_3[$_i - 1], 'loop #'.$_i);
}
$t->is($_i, 3, 'loop count');

// min & max filtering
$pager = new sfRedisZsetPager($kname, 2);
$pager->setParameter('min', 1999);
$pager->setParameter('max', 2006);
$pager->setPage(1);
$pager->init();

$t->is($pager->count(), 6, '::count()');
$t->is($pager->getObjectByCursor(1), 'Peut-être', '::getObjectByCursor()');
$t->is($pager->getObjectByCursor(3), 'Arsène Lupin', '::getObjectByCursor()');
$t->is($pager->getObjectByCursor(6), 'Dans Paris', '::getObjectByCursor()');

$page_1 = array('Peut-être', 'L\'auberge espagnole');

$_i = 0;
foreach ($pager as $result)
{
  $_i++;
  $t->is($result, $page_1[$_i - 1], 'loop #'.$_i);
}
$t->is($_i, 2, 'loop count');

$pager->setPage(3);
$pager->init();

$page_3 = array('De battre, mon coeur s\'est arrêté', 'Dans Paris');

$_i = 0;
foreach ($pager as $result)
{
  $_i++;
  $t->is($result, $page_3[$_i - 1], 'loop #'.$_i);
}
$t->is($_i, 2, 'loop count');

// min filtering
$pager = new sfRedisZsetPager($kname, 2);
$pager->setParameter('min', 2004);
$pager->setPage(1);
$pager->init();

$t->is($pager->count(), 5, '::count()');

// max filtering
$pager = new sfRedisZsetPager($kname, 2);
$pager->setParameter('max', 1997);
$pager->setPage(1);
$pager->init();

$t->is($pager->count(), 2, '::count()');

// cleanup
$client->del($kname);

