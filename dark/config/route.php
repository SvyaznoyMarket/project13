<?php

return array(
    // главная страница
    'homepage' => array(
        'pattern' => '/',
        'action'  => array('Main\IndexAction', 'execute'),
    ),

    // корзина
    'cart' => array(
        'pattern' => '/cart',
        'action'  => array('Cart\Action', 'executeIndex'),
    ),

    // поиск
    'search' => array(
        'pattern' => '/search',
        'action'  => array('Search\IndexAction', 'execute'),
    ),

    // пользователь
    'user.login'  => array(
        'pattern' => '/login',
        'action'  => array('User\Action', 'login'),
        'method'  => array('GET', 'POST'),
    ),
    'user.logout' => array(
        'pattern' => '/logout',
        'action'  => array('User\Action', 'logout'),
        'method'  => array('GET'),
    ),
    'user' => array(
        'pattern' => '/private',
        'action'  => array('User\IndexAction', 'execute'),
    ),

    // регион
    'region.init' => array(
        'pattern' => '/region/init',
        'action'  => array('Region\Action', 'init'),
    ),
    'region.change' => array(
        'pattern' => '/region/change/:region',
        'action'  => array('Region\Action', 'change'),
    ),

    // каталог товаров
    'product.category' => array(
        'pattern' => '/catalog/{categoryPath}/',
        'action'  => array('ProductCategory\IndexAction', 'execute'),
        'require' => array('categoryPath' => '[\w\d-_]+\/?[\w\d-_]+'),
    ),
    'product' => array(
        'pattern' => '/product/{productPath}',
        'action'  => array('Product\IndexAction', 'execute'),
        'require' => array('productPath' => '[\w\d-_]+\/{1}[\w\d-_]+'),
    ),
    'product.delivery' => array(
        'pattern' => '/product/delivery-info/{productId}',
        'action'  => array('Product\DeliveryAction', 'execute'),
    ),

    // магазины
    'shop' => array(
        'pattern' => '/shops',
        'action'  => array('Shop\IndexAction', 'execute'),
    ),
);
