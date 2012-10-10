<?php

return array(
    // главная страница
    'homepage' => array(
        'pattern' => '/',
        'action'  => array('Main\IndexAction', 'execute'),
    ),

    // поиск
    'search' => array(
        'pattern' => '/search',
        'action'  => array('Search\IndexAction', 'execute'),
    ),

    // пользователь
    'user.login' => array(
        'pattern' => '/login',
        'action'  => array('User\Action', 'login'),
    ),
    'user.register' => array(
        'pattern' => '/register',
        'action'  => array('User\Action', 'register'),
    ),
    'user.logout' => array(
        'pattern' => '/logout',
        'action'  => array('User\Action', 'logout'),
        'method'  => array('GET'),
    ),
    'user.forgot' => array(
        'pattern' => '/request-password',
        'action'  => array('User\Action', 'forgot'),
    ),
    'user.reset' => array(
        'pattern' => '/reset-password',
        'action'  => array('User\Action', 'reset'),
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
        'pattern' => '/region/change/{regionId}',
        'action'  => array('Region\Action', 'change'),
    ),
    'region.autocomplete' => array(
        'pattern' => '/region/autocomplete',
        'action'  => array('Region\Action', 'autocomplete'),
    ),

    // магазины
    'shop' => array(
        'pattern' => '/shops',
        'action'  => array('Shop\IndexAction', 'execute'),
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
        'pattern' => '/product/delivery-info',
        'action'  => array('Product\DeliveryAction', 'execute'),
    ),

    // корзина
    'cart' => array(
        'pattern' => '/cart/', // TODO: сделать '/cart'
        'action'  => array('Cart\IndexAction', 'execute'),
    ),
    'cart.product.add' => array(
        'pattern' => '/cart/add/{productId}/_quantity/{quantity}', // TODO: сделать поприличнее - '/cart/add-product/{productId}/{quantity}'
        'action'  => array('Cart\ProductAction', 'add'),
    ),
);
