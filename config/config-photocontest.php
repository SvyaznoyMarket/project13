<?php

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config.php';

$c->debug = false;
$c->session['cookie_domain'] = 'vadim.ent3.ru';
$c->mainHost = 'vadim.ent3.ru';

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
