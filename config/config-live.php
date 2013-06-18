<?php

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config.php';

$c->debug = false;

$c->abtest['bestBefore'] = '2013-04-23';
$c->abtest['enabled']    = true;
$c->abtest['test']       = [
    [
        'traffic'  => '40',
        'key'      => 'upsell',
        'name'     => 'Страница tocart',
        'ga_event' => 'tocart',
    ],
    [
        'traffic'  => '40',
        'key'      => 'order2cart',
        'name'     => 'Страница cart',
        'ga_event' => 'cart',
    ],
];

$c->requestMainMenu = true;

return $c;
