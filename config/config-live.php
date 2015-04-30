<?php

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config.php';

$c->debug = false;

$c->eventService['enabled'] = true;

return $c;
