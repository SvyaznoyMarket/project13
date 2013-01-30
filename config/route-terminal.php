<?php

return array(
    // главная страница
    'homepage' => array(
        'pattern' => '/',
        'action'  => array('Main\IndexAction', 'execute'),
    ),

    // карточка товара
    'product.show' => array(
        'pattern' => '/product/{productId}',
        'action'  => array('Product\IndexAction', 'execute'),
        'require' => array('productPath' => '[\d]+'),
    ),
);
