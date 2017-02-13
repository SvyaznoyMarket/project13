<?php

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config.php';

$c->debug = true;

//$c->eventService['url'] = 'http://event.ent3.ru/';

$c->richRelevance['jsUrl'] = '//media.richrelevance.com/rrserver/js/1.1/p13n_unoptimized.js';

$c->analytics['enabled'] = false;
$c->googleAnalytics['enabled'] = false;
$c->yandexMetrika['enabled'] = false;
$c->adFox['enabled'] = false;
$c->googleTagManager['enabled'] = false;

$c->partners['GetIntent']['enabled'] = false;
$c->partners['alexa']['enabled'] = false;

$c->partners['AddThis']['enabled'] = false;
$c->partners['CityAdsRetargeting']['enabled'] = false;
$c->partners['Sociaplus']['enabled'] = false;
$c->partners['ActionpayRetargeting']['enabled'] = false;
$c->partners['MyThings']['enabled'] = false;
$c->partners['Giftery']['enabled'] = false;
$c->partners['facebook']['enabled'] = false;

$c->mainMenu['recommendationsEnabled'] = false;

return $c;