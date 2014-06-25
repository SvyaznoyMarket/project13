<?php

return array_merge([
    // главная страница
    'pc.homepage' => [
        'pattern' => '/contest',
        'action'  => ['Photocontest\IndexAction', 'index'],
		'require' => [
            'order'	=> '\w{1}',
			'page'	=> '\d{1,2}'
        ],
    ],
	
	'pc.service.safeKey' => [
        'pattern' => '/contest/sk',
        'action'  => ['Photocontest\PhotoAction', 'safeKey'],
    ],
	
	'pc.photo.unvote' => [
        'pattern' => '/contest/unvote/{id}',
        'action'  => ['Photocontest\PhotoAction', 'unvote'],
		'require' => [
            'id'		=> '\d+'
        ],
    ],
	
	'pc.photo.vote' => [
        'pattern' => '/contest/vote/{id}',
        'action'  => ['Photocontest\PhotoAction', 'vote'],
		'require' => [
            'id'		=> '\d+'
        ],
    ],
	
	'pc.photo.create' => [
        'pattern' => '/contest/{contestRoute}/add',
        'action'  => ['Photocontest\PhotoAction', 'create'],
		'require' => [
            'contestRoute'	=> '[A-z0-9_]+',
        ],
    ],
	
	
	'pc.photo.show' => [
        'pattern' => '/contest/{contestRoute}/{id}',
        'action'  => ['Photocontest\PhotoAction', 'show'],
		'require' => [
            'id'		=> '\d+',
			'contestRoute'		=> '[A-z0-9_]+',
        ],
    ],

	'pc.contest'	=> [
		'pattern'	=> '/contest/{contestRoute}',
		'action'  => ['Photocontest\IndexAction', 'contest'],
		'require' => [
            'contestRoute'	=> '[A-z0-9_]+',
			'order'	=> '\w{1}',
			'page'	=> '\d{1,2}'
        ],
	],
	
],require __DIR__ . '/route-main.php');