<?php

return array_merge(require __DIR__ . '/route-main.php', [
    // главная страница
    'homepage' => [
        'pattern' => '/',
        'action'  => ['Photocontest\IndexAction', 'execute'],
    ],

    //
    'photo.show' => [
        'pattern' => '/photos/{photoId}',
        'action'  => ['Photocontest\Photo\ShowAction', 'execute'],
    ],
]);

