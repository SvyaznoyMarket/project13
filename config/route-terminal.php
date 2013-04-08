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

    // категория товара
    'category.show' => [
        'pattern' => '/catalog/{categoryId}',
        'action'  => ['ProductCategory\IndexAction', 'execute'],
        'require' => ['categoryId' => '[\d]+'],
    ],
    'category.product' => [
        'pattern' => '/catalog/{categoryId}/product',
        'action'  => ['ProductCategory\IndexAction', 'product'],
        'require' => ['categoryId' => '[\d]+'],
    ],
];
