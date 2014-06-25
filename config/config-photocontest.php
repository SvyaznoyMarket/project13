<?php

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config.php';

$c->debug = false;

$c->coreV2['client_id'] = 'photocontest';
$c->photoContest = [
	'client' => [
		'url'          => 'http://photo.vadim.ent3.ru/',
		'client_id'    => 'photocontest',
		'timeout'      => 1,
		'retryTimeout' => 1,
		'retryCount'   => 2,
		'debug'        => true,
	]
];


return $c;
