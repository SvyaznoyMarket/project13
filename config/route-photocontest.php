<?php

return array_merge([
    // главная страница
    'pc.homepage' => [
        'pattern' => '/',
        'action'  => ['Photocontest\IndexAction', 'index'],
		'require' => [
            'order'	=> '\w{1}',
			'page'	=> '\d{1,2}'
        ],
    ],

	'pc.contest'	=> [
		'pattern'	=> '/{id}',
		'action'  => ['Photocontest\IndexAction', 'contest'],
		'require' => [
            'id'	=> '\d+',
			'order'	=> '\w{1}',
			'page'	=> '\d{1,2}'
        ],
	],
	
	'pc.photo.show' => [
        'pattern' => '/{contestId}/{id}',
        'action'  => ['Photocontest\PhotoAction', 'show'],
		'require' => [
            'id'		=> '\d+',
			'contestId'	=> '\d+',
        ],
    ],
	
	'pc.photo.create' => [
        'pattern' => '/{contestId}/add',
        'action'  => ['Photocontest\PhotoAction', 'create'],
		'require' => [
            'contestId'   => '\d+',
        ],
    ],
	
	'pc.photo.vote' => [
        'pattern' => '/vote/{id}',
        'action'  => ['Photocontest\PhotoAction', 'vote'],
		'require' => [
            'id'		=> '\d+'
        ],
    ],
	
	'pc.photo.unvote' => [
        'pattern' => '/unvote/{id}',
        'action'  => ['Photocontest\PhotoAction', 'unvote'],
		'require' => [
            'id'		=> '\d+'
        ],
    ],
    
	
	'pc.service.safeKey' => [
        'pattern' => '/sk',
        'action'  => ['Photocontest\PhotoAction', 'safeKey'],
    ],
	
],require __DIR__ . '/route-main.php');