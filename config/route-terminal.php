<?php

return [
    // главная страница
    'homepage' => [
        'pattern' => '/',
        'action'  => ['Main\IndexAction', 'execute'],
    ],

    // карточка товара
    'product.show' => [
        'pattern' => '/product/{productId}',
        'action'  => ['Product\IndexAction', 'execute'],
        'require' => ['productId' => '[\d]+'],
    ],

    // список товаров
    'category.show' => [
        'pattern' => '/catalog/{categoryId}',
        'action'  => ['ProductCategory\IndexAction', 'execute'],
        'require' => ['categoryId' => '[\d]+'],
    ],
];
