<?php

return array(
    // главная страница
    'homepage' => array(
        'pattern' => '/',
        'action'  => array('Main\IndexAction', 'execute'),
    ),

    'category.mainMenu' => array(
        'pattern' => '/category/main_menu',
        'action'  => array('ProductCategory\MainMenuAction', 'execute'),
    ),

    // поиск
    'search' => array(
        'pattern' => '/search',
        'action'  => array('Search\Action', 'execute'),
    ),
    // поиск бесконечная прокрутка
    'search.infinity' => array(
        'pattern' => '/search/_infinity',
        'action'  => array('Search\Action', 'execute'),
    ),

    // инфо пользователя
    'user.info' => array(
        'pattern' => '/user/shortinfo',
        'action'  => array('User\InfoAction', 'execute'),
    ),
    // вход пользователя
    'user.login' => array(
        'pattern' => '/login',
        'action'  => array('User\Action', 'login'),
    ),
    // регистрация пользователя
    'user.register' => array(
        'pattern' => '/register',
        'action'  => array('User\Action', 'register'),
    ),
    // регистрация корпоративного пользователя
    'user.registerCorporate' => array(
        'pattern' => '/corporate-register',
        'action'  => array('User\Action', 'registerCorporate'),
    ),
    // выход пользователя
    'user.logout' => array(
        'pattern' => '/logout',
        'action'  => array('User\Action', 'logout'),
        'method'  => array('GET'),
    ),
    // восстановление пароля
    'user.forgot' => array(
        'pattern' => '/request-password',
        'action'  => array('User\Action', 'forgot'),
    ),
    // сброс пароля
    'user.reset' => array(
        'pattern' => '/reset-password',
        'action'  => array('User\Action', 'reset'),
    ),
    // личный кабинет
    'user' => array(
        'pattern' => '/private/',
        'action'  => array('User\IndexAction', 'execute'),
    ),

    // регион
    'region.init' => array(
        'pattern' => '/region/init',
        'action'  => array('Region\Action', 'init'),
    ),
    // смена региона
    'region.change' => array(
        'pattern' => '/region/change/{regionId}',
        'action'  => array('Region\Action', 'change'),
    ),
    // автоподстановка региона
    'region.autocomplete' => array(
        'pattern' => '/region/autocomplete',
        'action'  => array('Region\Action', 'autocomplete'),
    ),
    // сменя региона по прямой ссылке
    'region.redirect' => array(
        'pattern' => '/reg/{regionId}/{redirectTo}',
        'action'  => array('Region\Action', 'redirect'),
        'require' => array(
            'regionId'   => '\d+',
            'redirectTo' => '.+',
        ),
    ),

    // магазины
    'shop' => array(
        'pattern' => '/shops',
        'action'  => array('Shop\Action', 'index'),
    ),
    'shop.region' => array(
        'pattern' => '/shops/{regionId}', // TODO: regionId --> regionToken
        'action'  => array('Shop\Action', 'region'),
    ),
    'shop.show' => array(
        'pattern' => '/shops/{regionToken}/{shopToken}',
        'action'  => array('Shop\Action', 'show'),
    ),

    // каталог товаров
    'product.category' => array(
        'pattern' => '/catalog/{categoryPath}/',
        'action'  => array('ProductCategory\Action', 'category'),
        'require' => array('categoryPath' => '[\w\d-_]+\/?[\w\d-_]+'),
    ),
    // слайдер товаров
    'product.category.slider' => array(
        'pattern' => '/catalog/{categoryPath}/_slider',
        'action'  => array('ProductCategory\Action', 'slider'),
        'require' => array('categoryPath' => '[\w\d-_]+\/?[\w\d-_]+'),
    ),
    // общее количество отфильтрованных товаров
    'product.category.count' => array(
        'pattern' => '/catalog/{categoryPath}/_count',
        'action'  => array('ProductCategory\Action', 'count'),
        'require' => array('categoryPath' => '[\w\d-_]+\/?[\w\d-_]+'),
    ),
    // показывать глобальный список товаров
    'product.category.global' => array(
        'pattern' => '/catalog/{categoryPath}/_global',
        'action'  => array('ProductCategory\Action', 'setGlobal'),
        'require' => array('categoryPath' => '[\w\d-_]+\/?[\w\d-_]+'),
    ),
    // каталог товаров с бесконечной прокруткой
    'product.category.infinity' => array(
        'pattern' => '/catalog/{categoryPath}/_infinity',
        'action'  => array('ProductCategory\Action', 'category'),
        'require' => array('categoryPath' => '[\w\d-_]+\/?[\w\d-_]+'),
    ),
    // карточка товара
    'product' => array(
        'pattern' => '/product/{productPath}',
        'action'  => array('Product\IndexAction', 'execute'),
        'require' => array('productPath' => '[\w\d-_]+\/{1}[\w\d-_]+'),
    ),
    // карточка линии товара
    'product.line' => array(
        'pattern' => '/line/{lineToken}',
        'action'  => array('Product\LineAction', 'execute'),
    ),
    // расчет доставки товара
    'product.delivery' => array(
        'pattern' => '/product/delivery-info',
        'action'  => array('Product\DeliveryAction', 'info'),
        'method'  => array('POST'),
    ),
    'product.delivery_1click' => array(
        'pattern' => '/product/delivery1click',
        'action'  => array('Product\DeliveryAction', 'oneClick'),
    ),
    'product.stock' => array(
        'pattern' => '/product/{productPath}/stock',
        'action'  => array('Product\StockAction', 'execute'),
        'require' => array('productPath' => '[\w\d-_]+\/{1}[\w\d-_]+'),
    ),
    'product.accessory' => array(
        'pattern' => '/products/accessories/{productToken}',
        'action'  => array('Product\AccessoryAction', 'execute'),
        'require' => array('productToken' => '[\w\d-_]+'),
    ),
    'product.related' => array(
        'pattern' => '/products/related/{productToken}',
        'action'  => array('Product\RelatedAction', 'execute'),
        'require' => array('productToken' => '[\w\d-_]+'),
    ),
    'product.comment' => array(
        'pattern' => '/product/{productPath}/comments',
        'action'  => array('Product\CommentAction', 'execute'),
        'require' => array('productPath' => '[\w\d-_]+\/{1}[\w\d-_]+'),
    ),
    'product.set' => array(
        'pattern' => '/products/set/{productBarcodes}',
        'action'  => array('Product\SetAction', 'execute'),
    ),
    'tag' => array(
        'pattern' => '/tags/{tagToken}',
        'action'  => array('Tag\Action', 'index'),
    ),
    'tag.infinity' => array(
        'pattern' => '/tags/{tagToken}/_infinity',
        'action'  => array('Tag\Action', 'index'),
    ),
    'tag.category' => array(
        'pattern' => '/tags/{tagToken}/{categoryToken}',
        'action'  => array('Tag\Action', 'index'),
    ),
    'tag.category.infinity' => array(
        'pattern' => '/tags/{tagToken}/{categoryToken}/_infinity',
        'action'  => array('Tag\Action', 'index'),
    ),
    'product.rating.create_total' => array(
        'pattern' => '/product-rating/createtotal/{productId}/{rating}',
        'require' => array('productId' => '\d+', 'rating' => '\d+'),
    ),

    // проверка сертификата
    'certificate.check' => array(
        'pattern' => '/certificate-check',
        'action'  => array('Certificate\Action', 'check'),
        'method'  => array('POST'),
    ),

    // корзина
    'cart' => array(
        'pattern' => '/cart/', // TODO: сделать '/cart'
        'action'  => array('Cart\IndexAction', 'execute'),
    ),
    // добавление товара в корзину
    'cart.product.add' => array(
        'pattern' => '/cart/add/{productId}/_quantity/{quantity}', // TODO: сделать поприличнее - '/cart/add-product/{productId}/{quantity}'
        'action'  => array('Cart\ProductAction', 'set'),
    ),
    // удаление товара из корзины
    'cart.product.delete' => array(
        'pattern' => '/cart/delete/{productId}/_service/', // TODO: сделать поприличнее - '/cart/delete-product/{productId}'
        'action'  => array('Cart\ProductAction', 'delete'),
    ),
    'old.cart.product.add' => array(
        'pattern' => '/cart/add/{productId}/_quantity/', // TODO: Убить, когда полностью переедем на dark, переписать js с учетом наличия кол-ва
        'action'  => array('Cart\ProductAction', 'set'),
    ),
    // удаление услуги из корзины
    'cart.service.delete' => array(
        'pattern' => '/cart/delete_service/{productId}/_service/{serviceId}',
        'require' => array('productId' => '\d+', 'serviceId' => '\d+'),
        'action'  => array('Cart\ServiceAction', 'delete'),
    ),
    // добавление услуги в корзину
    'cart.service.add' => array(
        'pattern' => '/cart/add_service/{productId}/_service/{serviceId}/_quantity/{quantity}',
        'require' => array(
            'productId' => '\d+',
            'serviceId' => '\d+',
        ),
        'action'  => array('Cart\ServiceAction', 'set'),
    ),
    'cart.warranty.set' => array(
        'pattern' => '/cart/warranty/{productId}/set/{warrantyId}',
        'require' => array('productId' => '\d+', 'warrantyId' => '\d+'),
    ),
    'cart.warranty.delete' => array(
        'pattern' => '/cart/warranty/{productId}/delete/{warrantyId}',
        'require' => array('productId' => '\d+', 'warrantyId' => '\d+'),
    ),

    // заказ
    'order.1click' => array(
        'pattern' => '/orders/1click',
        'action'  => array('Order\OneClickAction', 'execute'),
        'method'  => array('POST'),
    ),
    'order.create' => array(
        'pattern' => '/orders/new',
        'action'  => array('Order\Action', 'create'),
    ),
    'order.externalCreate' => array(
        'pattern' => '/orders/create-external',
        'action'  => array('Order\ExternalCreateAction', 'execute'),
    ),
    'order.complete' => array(
        'pattern' => '/orders/complete',
        'action'  => array('Order\Action', 'complete'),
    ),
    'order.paymentComplete' => array(
        'pattern' => '/orders/payment/{orderNumber}',
        'action'  => array('Order\Action', 'paymentComplete'),
    ),
    'order.bill' => array(
        'pattern' => '/private/orders/{orderNumber}/bill',
        'action'  => array('Order\BillAction', 'execute'),
    ),

    // услуги
    'service' => array(
        'pattern' => '/f1',
        'action'  => array('Service\Action', 'index'),
    ),
    'service.category' => array(
        'pattern' => '/f1/{categoryToken}',
        'require' => array('categoryToken' => '[\w\d-_]+'),
        'action'  => array('Service\Action', 'category'),
    ),
    'service.show' => array(
        'pattern' => '/f1/show/{serviceToken}',
        'require' => array('serviceToken' => '[\w\d-_]+'),
        'action'  => array('Service\Action', 'show'),
    ),

    // smartengine
    'smartengine.pull.product_alsoViewed' => array(
        'pattern' => '/product-also-viewed/{productId}',
        'action' => array('Smartengine\Action', 'pullProductAlsoViewed'),
        'require' => array('productId' => '\d+'),
    ),
    'smartengine.push.product_view' => array(
        'pattern' => '/product-view/{productId}',
        'action' => array('Smartengine\Action', 'pushView'),
        'require' => array('productId' => '\d+'),
    ),
    'smartengine.push.buy' => array(
        'pattern' => '/product-buy',
        'action'  => array('Smartengine\Action', 'pushBuy'),
        'method'  => array('POST'),
    ),

    // редактирование данных пользователя
    'user.edit' => array(
        'pattern' => '/private/edit',
        'action'  => array('User\EditAction', 'execute'),
    ),
    // редактирование данных пользователя
    'user.order' => array(
        'pattern' => '/private/orders',
        'action'  => array('User\OrderAction', 'execute'),
    ),
    // адвокат клиента
    'user.consultation' => array(
        //'pattern' => '/private/consultation',
        'pattern' => '/private/consultation/legal',
        'action'  => array('User\ConsultationAction', 'execute'),
    ),
    // изменение пароля пользователя
    'user.changePassword' => array(
        'pattern' => '/private/password',
        'action'  => array('User\ChangePasswordAction', 'execute'),
    ),

    //подписка на уцененные товары
    'refurbished' => array(
        'pattern' => '/refurbished',
        'action'  => array('Refurbished\Action', 'execute'),
    ),
    'refurbished.subscribe' => array(
        'pattern' => '/refurbished/subscribe',
        'action'  => array('Refurbished\Action', 'subscribe'),
    ),

    // подписка
    'user.subscribe' => array(
        'pattern' => '/private/subscribe',
        'action'  => array('User\SubscribeAction', 'execute'),
        'method'  => array('POST'),
    ),

    // qrcode
    'qrcode' => array(
        'pattern' => '/qr/{qrcode}',
        'action'  => array('Qrcode\Action', 'execute'),
    ),

    //content
    'content' => array(
        'pattern' => '/{token}',
        'action'  => array('Content\Action', 'execute'),
    )
);
