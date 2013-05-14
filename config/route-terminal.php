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
    // товары в категории
    'category.product' => [
        'pattern' => '/catalog/{categoryId}/product',
        'action'  => ['ProductCategory\IndexAction', 'product'],
        'require' => ['categoryId' => '[\d]+'],
    ],
    // список моделей в категории товара
    'category.show.model' => [
        'pattern' => '/catalog/{categoryId}/model',
        'action'  => ['ProductCategory\ModelAction', 'execute'],
        'require' => ['categoryId' => '[\d]+'],
    ],
    // линия товара
    'line.show' => [
        'pattern' => '/line/{lineId}',
        'action'  => ['ProductLine\IndexAction', 'execute'],
        'require' => ['lineId' => '[\d]+'],
    ],
    // товары в линии
    'line.product' => [
        'pattern' => '/line/{lineId}/product',
        'action'  => ['ProductLine\IndexAction', 'product'],
        'require' => ['lineId' => '[\d]+'],
    ],
];
