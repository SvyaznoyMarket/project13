<?php

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config-live.php';

$c->preview = true;

return $c;