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
    // фильтры для категории
    'category.filter' => [
        'pattern' => '/catalog/{categoryId}/filter',
        'action'  => ['ProductCategory\FilterAction', 'execute'],
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
    'line.kit' => [
        'pattern' => '/line/{lineId}',
        'action'  => ['ProductLine\KitAction', 'execute'],
        'require' => ['lineId' => '[\d]+'],
    ],
    // наборы товаров в линии
    'line.kit.product' => [
        'pattern' => '/line/{lineId}/product',
        'action'  => ['ProductLine\KitAction', 'product'],
        'require' => ['lineId' => '[\d]+'],
    ],
    // список товаров в линии
    'line.part' => [
        'pattern' => '/line/{lineId}/part',
        'action'  => ['ProductLine\PartAction', 'execute'],
        'require' => ['lineId' => '[\d]+'],
    ],
];
