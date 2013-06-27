<?php

return [
    // главная страница
    'homepage' => [
        'pattern' => '/',
        'action'  => ['Main\IndexAction', 'execute'],
    ],

    'category.mainMenu' => [
        'pattern' => '/category/main_menu',
        'action'  => ['ProductCategory\MainMenuAction', 'execute'],
    ],

    // поиск
    'search' => [
        'pattern' => '/search',
        'action'  => ['Search\Action', 'execute'],
    ],
    // поиск бесконечная прокрутка
    'search.infinity' => [
        'pattern' => '/search/_infinity',
        'action'  => ['Search\Action', 'execute'],
    ],
    // автоподстановка поиска
    'search.autocomplete' => [
        'pattern' => '/search/autocomplete',
        'action'  => ['Search\Action', 'autocomplete'],
    ],

    // инфо пользователя
    'user.info' => [
        'pattern' => '/user/shortinfo',
        'action'  => ['User\InfoAction', 'execute'],
    ],
    // вход пользователя
    'user.login' => [
        'pattern' => '/login',
        'action'  => ['User\Action', 'login'],
    ],
    // регистрация пользователя
    'user.register' => [
        'pattern' => '/register',
        'action'  => ['User\Action', 'register'],
    ],
    // регистрация корпоративного пользователя
    'user.registerCorporate' => [
        'pattern' => '/corporate-register',
        'action'  => ['User\Action', 'registerCorporate'],
    ],
    // выход пользователя
    'user.logout' => [
        'pattern' => '/logout',
        'action'  => ['User\Action', 'logout'],
        'method'  => ['GET'],
    ],
    // восстановление пароля
    'user.forgot' => [
        'pattern' => '/request-password',
        'action'  => ['User\Action', 'forgot'],
    ],
    // сброс пароля
    'user.reset' => [
        'pattern' => '/reset-password',
        'action'  => ['User\Action', 'reset'],
    ],
    // личный кабинет
    'user' => [
        'pattern' => '/private',
        'action'  => ['User\IndexAction', 'execute'],
    ],
    // данные по авторизованному пользователю
    'user.getAuth' => [
        'pattern' => '/user/get-auth',
        'action'  => ['User\GetAction', 'execute'],
    ],
    // вход через социальные сети
    'user.login.external' => [
        'pattern' => '/login-{providerName}',
        'action'  => ['User\ExternalLoginAction', 'execute'],
    ],
    // ответ от социальных сетей при входе пользователя
    'user.login.external.response' => [
        'pattern' => '/login-{providerName}/response',
        'action'  => ['User\ExternalLoginResponseAction', 'execute'],
    ],

    // регион
    'region.init' => [
        'pattern' => '/region/init',
        'action'  => ['Region\Action', 'init'],
    ],
    // смена региона
    'region.change' => [
        'pattern' => '/region/change/{regionId}',
        'action'  => ['Region\Action', 'change'],
    ],
    // автоподстановка региона
    'region.autocomplete' => [
        'pattern' => '/region/autocomplete',
        'action'  => ['Region\Action', 'autocomplete'],
    ],
    // автоопределение города
    'region.autoresolve' => [
        'pattern' => '/region/autoresolve',
        'action'  => ['Region\Action', 'autoresolve'],
    ],
    // сменя региона по прямой ссылке
    'region.redirect' => [
        'pattern' => '/reg/{regionId}{redirectTo}',
        'action'  => ['Region\Action', 'redirect'],
        'require' => [
            'regionId'   => '\d+',
            'redirectTo' => '.+',
        ],
    ],

    // магазины
    'shop' => [
        'pattern' => '/shops',
        'action'  => ['Shop\Action', 'index'],
    ],
    'shop.region' => [ // deprecated
        'pattern' => '/shops/{regionId}',
        'action'  => ['Shop\Action', 'region'],
    ],
    'shop.show' => [
        'pattern' => '/shops/{regionToken}/{shopToken}',
        'action'  => ['Shop\Action', 'show'],
    ],

    // показывать глобальный список товаров
    'product.category.global.short' => [
        'pattern' => '/catalog/{categoryPath}/_global',
        'action'  => ['ProductCategory\Action', 'setGlobal'],
        'require' => ['categoryPath' => '[\w\d-_]+'],
    ],
    // показывать глобальный список товаров
    'product.category.global' => [
        'pattern' => '/catalog/{categoryPath}/_global',
        'action'  => ['ProductCategory\Action', 'setGlobal'],
        'require' => ['categoryPath' => '[\w\d-_]+\/[\w\d-_]+'],
    ],
    // показывать товары на складе
    'product.category.instore' => [
        'pattern' => '/catalog/{categoryPath}/_instore',
        'action'  => ['ProductCategory\Action', 'setInstore'],
        'require' => ['categoryPath' => '[\w\d-_]+\/?[\w\d-_]+'],
    ],
    // показать товары в конкретном магазине
    'product.category.shop' => [
        'pattern' => '/catalog/{categoryPath}/_inshop',
        'action'  => ['ProductCategory\Action', 'setShopId'],
        'require' => ['categoryPath' => '[\w\d-_]+\/?[\w\d-_]+'],
    ],
    // каталог товаров
    'product.category' => [
        'pattern' => '/catalog/{categoryPath}',
        'action'  => ['ProductCategory\Action', 'category'],
        'require' => ['categoryPath' => '[\w\d-_]+\/?[\w\d-_]+'],
    ],
    // бесконечная листалка в категориях
    'product.category.sliderInfinity' => [
        'pattern' => '/catalog/{categoryPath}/_sliderInfinity',
        'action'  => ['ProductCategory\Action', 'category'],
        'require' => ['categoryPath' => '[\w\d-_]+\/?[\w\d-_]+'],
    ],
    // слайдер товаров
    'product.category.slider' => [
        'pattern' => '/ajax/catalog/{categoryPath}/_slider',
        'action'  => ['ProductCategory\Action', 'slider'],
        'require' => ['categoryPath' => '[\w\d-_]+\/?[\w\d-_]+'],
    ],
    // общее количество отфильтрованных товаров
    'product.category.count' => [
        'pattern' => '/ajax/catalog/{categoryPath}/_count',
        'action'  => ['ProductCategory\Action', 'count'],
        'require' => ['categoryPath' => '[\w\d-_]+\/?[\w\d-_]+'],
    ],
    // каталог товаров с бесконечной прокруткой
    'product.category.infinity' => [
        'pattern' => '/ajax/catalog/{categoryPath}/_infinity',
        'action'  => ['ProductCategory\Action', 'category'],
        'require' => ['categoryPath' => '[\w\d-_]+\/?[\w\d-_]+'],
    ],
    // каталог товаров бренда
    'product.category.brand' => [
        'pattern' => '/catalog/{categoryPath}/{brandToken}',
        'action'  => ['ProductCategory\Action', 'category'],
        'require' => ['categoryPath' => '[\w\d-_]+\/[\w\d-_]+', 'brand' => '[\w\d-_]+'],
    ],
    // каталог товаров бренда
    'product.category.brand.infinity' => [
        'pattern' => '/ajax/catalog/{categoryPath}/{brandToken}/_infinity',
        'action'  => ['ProductCategory\Action', 'category'],
        'require' => ['categoryPath' => '[\w\d-_]+\/[\w\d-_]+', 'brand' => '[\w\d-_]+'],
    ],
    // слайдер рекомендованных товаров
    'product.category.recommended.slider' => [
        'pattern' => '/ajax/catalog/{categoryPath}/_slider-recommended',
        'action'  => ['Crossss\ProductCategoryAction', 'recommended'],
        'require' => ['categoryPath' => '[\w\d-_]+\/?[\w\d-_]+'],
    ],

    // карточка товара
    'product' => [
        'pattern' => '/product/{productPath}',
        'action'  => ['Product\IndexAction', 'execute'],
        'require' => ['productPath' => '[\w\d-_]+\/{1}[\w\d-_]+'],
    ],
    // карточка линии товара
    'product.line' => [
        'pattern' => '/line/{lineToken}',
        'action'  => ['Product\LineAction', 'execute'],
    ],
    // расчет доставки товара
    'product.delivery' => [
        'pattern' => '/product/delivery-info',
        'action'  => ['Product\DeliveryAction', 'info'],
        'method'  => ['POST'],
    ],
    'product.delivery_1click' => [
        'pattern' => '/product/delivery1click',
        'action'  => ['Product\DeliveryAction', 'oneClick'],
    ],
    'product.stock' => [
        'pattern' => '/product/{productPath}/stock',
        'action'  => ['Product\StockAction', 'execute'],
        'require' => ['productPath' => '[\w\d-_]+\/{1}[\w\d-_]+'],
    ],
    'product.accessory' => [
        'pattern' => '/products/accessories/{productToken}',
        'action'  => ['Product\AccessoryAction', 'execute'],
        'require' => ['productToken' => '[\w\d-_]+'],
    ],
    'product.accessory.jewel' => [
        'pattern' => '/jewel/products/accessories/{productToken}',
        'action'  => ['Jewel\Product\AccessoryAction', 'execute'],
        'require' => ['productToken' => '[\w\d-_]+'],
    ],
    'product.related' => [
        'pattern' => '/products/related/{productToken}',
        'action'  => ['Product\RelatedAction', 'execute'],
        'require' => ['productToken' => '[\w\d-_]+'],
    ],
    'product.related.jewel' => [
        'pattern' => '/jewel/products/related/{productToken}',
        'action'  => ['Jewel\Product\RelatedAction', 'execute'],
        'require' => ['productToken' => '[\w\d-_]+'],
    ],
    'product.comment' => [
        'pattern' => '/product/{productPath}/comments',
        'action'  => ['Product\CommentAction', 'execute'],
        'require' => ['productPath' => '[\w\d-_]+\/{1}[\w\d-_]+'],
    ],
    'product.set' => [
        'pattern' => '/products/set/{productBarcodes}',
        'action'  => ['Product\SetAction', 'execute'],
    ],
    'product.widget' => [
        'pattern' => '/products/widget/{productBarcodes}',
        'action'  => ['Product\SetAction', 'widget'],
    ],
    'product.upsell' => [
        'pattern' => '/tocart/{productToken}',
        'action'  => ['Product\UpsellAction', 'execute'],
        'require' => ['productToken' => '[\w\d-_]+'],
    ],
    //reviews
    'product.reviews' => [
        'pattern' => '/product-reviews/{productId}',
        'require' => ['productId' => '\d+'],
        'action'  => ['Product\ReviewsAction', 'execute'],
    ],
    'product.notification.lowerPrice' => [
        'pattern' => '/ajax/product-notification/{productId}',
        'require' => ['productId' => '\d+'],
        'action'  => ['Product\NotificationAction', 'lowerPrice'],
    ],

    'tag' => [
        'pattern' => '/tags/{tagToken}',
        'action'  => ['Tag\Action', 'index'],
    ],
    'tag.infinity' => [
        'pattern' => '/tags/{tagToken}/_infinity',
        'action'  => ['Tag\Action', 'index'],
    ],
    'tag.category' => [
        'pattern' => '/tags/{tagToken}/{categoryToken}',
        'action'  => ['Tag\Action', 'index'],
    ],
    'tag.category.infinity' => [
        'pattern' => '/tags/{tagToken}/{categoryToken}/_infinity',
        'action'  => ['Tag\Action', 'index'],
    ],
    'product.rating.create_total' => [
        'pattern' => '/product-rating/createtotal/{productId}/{rating}',
        'require' => ['productId' => '\d+', 'rating' => '\d+'],
        'action'  => ['Product\RatingAction', 'createTotal'],
    ],

    // проверка сертификата
    'certificate.check' => [
        'pattern' => '/certificate-check',
        'action'  => ['Certificate\Action', 'check'],
        'method'  => ['POST'],
    ],

    // корзина
    'cart' => [
        'pattern' => '/cart',
        'action'  => ['Cart\IndexAction', 'execute'],
    ],
    'cart.info' => [
        'pattern' => '/cart/info',
        'action'  => ['Cart\InfoAction', 'execute'],
    ],
    // очистка корзины
    'cart.clear' => [
        'pattern' => '/cart/clear',
        'action'  => ['Cart\ClearAction', 'execute'],
    ],
    // добавление товара в корзину
    'cart.product.add' => [
        'pattern' => '/cart/add-product/{productId}',
        'action'  => ['Cart\ProductAction', 'set'],
    ],
    // удаление товара из корзины
    'cart.product.delete' => [
        'pattern' => '/cart/delete-product/{productId}',
        'action'  => ['Cart\ProductAction', 'delete'],
    ],
    // добавление списка товаров в корзину
    'cart.product.setList' => [
        'pattern' => '/cart/set-products',
        'action'  => ['Cart\ProductAction', 'setList'],
    ],
    // добавление услуги в корзину
    'cart.service.add' => [
        'pattern' => '/cart/add-service/{serviceId}/for-product/{productId}',
        'require' => ['productId' => '\d+', 'serviceId' => '\d+'],
        'action'  => ['Cart\ServiceAction', 'set'],
    ],
    // удаление услуги из корзины
    'cart.service.delete' => [
        'pattern' => '/cart/delete-service/{serviceId}/for-product/{productId}',
        'require' => ['productId' => '\d+', 'serviceId' => '\d+'],
        'action'  => ['Cart\ServiceAction', 'delete'],
    ],
    // добавление гарантии в корзину
    'cart.warranty.set' => [
        'pattern' => '/cart/add-warranty/{warrantyId}/for-product/{productId}/',
        'require' => ['productId' => '\d+', 'warrantyId' => '\d+'],
        'action'  => ['Cart\WarrantyAction', 'set'],
    ],
    // удаление гарантии из корзины
    'cart.warranty.delete' => [
        'pattern' => '/cart/delete-warranty/{warrantyId}/for-product/{productId}',
        'require' => ['productId' => '\d+', 'warrantyId' => '\d+'],
        'action'  => ['Cart\WarrantyAction', 'delete'],
    ],
    'cart.certificate.apply' => [
        'pattern' => '/cart/f1-certificate',
        'action'  => ['Cart\CertificateAction', 'apply'],
    ],
    'cart.certificate.delete' => [
        'pattern' => '/cart/f1-certificate/delete',
        'action'  => ['Cart\CertificateAction', 'delete'],
    ],
    'cart.coupon.apply' => [
        'pattern' => '/cart/coupon',
        'action'  => ['Cart\CouponAction', 'apply'],
    ],
    'cart.coupon.delete' => [
        'pattern' => '/cart/coupon/delete',
        'action'  => ['Cart\CouponAction', 'delete'],
    ],
    'cart.sum' => [
        'pattern' => '/cart/sum',
        'action'  => ['Cart\SumAction', 'execute'],
    ],

    // заказ
    'order.1click' => [
        'pattern' => '/orders/1click',
        'action'  => ['Order\OneClickAction', 'execute'],
        'method'  => ['POST'],
    ],
    'order.create' => [
        'pattern' => '/orders/new',
        'action'  => ['Order\Action', 'create'],
    ],
    'order.externalCreate' => [
        'pattern' => '/orders/create-external',
        'action'  => ['Order\ExternalCreateAction', 'execute'],
    ],
    'order.complete' => [
        'pattern' => '/orders/complete',
        'action'  => ['Order\Action', 'complete'],
    ],
    'order.paymentComplete' => [
        'pattern' => '/orders/payment/{orderNumber}',
        'action'  => ['Order\Action', 'paymentComplete'],
    ],
    'order.bill' => [
        'pattern' => '/private/orders/{orderNumber}/bill',
        'action'  => ['Order\BillAction', 'execute'],
    ],

    // услуги
    'service' => [
        'pattern' => '/f1',
        'action'  => ['Service\Action', 'index'],
    ],
    'service.category' => [
        'pattern' => '/f1/{categoryToken}',
        'require' => ['categoryToken' => '[\w\d-_]+'],
        'action'  => ['Service\Action', 'category'],
    ],
    'service.show' => [
        'pattern' => '/f1/show/{serviceToken}',
        'require' => ['serviceToken' => '[\w\d-_]+'],
        'action'  => ['Service\Action', 'show'],
    ],

    // промо каталоги
    'promo.show' => [
        'pattern' => '/promo/{promoToken}',
        'require' => ['categoryToken' => '[\w\d-_]+'],
        'action'  => ['Promo\IndexAction', 'execute'],
    ],

    // smartengine
    'product.recommended' => [
        'pattern' => '/product-also-viewed/{productId}',
        'action' => ['Product\RecommendedAction', 'execute'],
        'require' => ['productId' => '\d+'],
    ],
    'product.recommended.jewel' => [
        'pattern' => '/jewel/product-also-viewed/{productId}',
        'action' => ['Jewel\Product\RecommendedAction', 'execute'],
        'require' => ['productId' => '\d+'],
    ],
    'smartengine.pull.product_similar' => [
        'pattern' => '/product-similar/{productId}',
        'action' => ['Smartengine\Action', 'pullProductSimilar'],
        'require' => ['productId' => '\d+'],
    ],
    'smartengine.push.product_view' => [
        'pattern' => '/product-view/{productId}',
        'action' => ['Smartengine\Action', 'pushView'],
        'require' => ['productId' => '\d+'],
    ],
    'smartengine.push.buy' => [
        'pattern' => '/product-buy',
        'action'  => ['Smartengine\Action', 'pushBuy'],
        'method'  => ['POST'],
    ],

    // редактирование данных пользователя
    'user.edit' => [
        'pattern' => '/private/edit',
        'action'  => ['User\EditAction', 'execute'],
    ],
    // редактирование данных пользователя
    'user.order' => [
        'pattern' => '/private/orders',
        'action'  => ['User\OrderAction', 'execute'],
    ],
    // адвокат клиента
    'user.consultation' => [
        'pattern' => '/private/consultation',
        'action'  => ['User\ConsultationAction', 'execute'],
    ],
    // изменение пароля пользователя
    'user.changePassword' => [
        'pattern' => '/private/password',
        'action'  => ['User\ChangePasswordAction', 'execute'],
    ],

    // подписка
    'friendship' => [
        'pattern' => '/be-friends',
        'action'  => ['Friendship\Action', 'execute'],
    ],

    //подписка на уцененные товары
    'refurbished' => [
        'pattern' => '/refurbished',
        'action'  => ['Refurbished\Action', 'execute'],
    ],
    'refurbished.subscribe' => [
        'pattern' => '/refurbished/subscribe',
        'action'  => ['Refurbished\Action', 'subscribe'],
    ],

    // подписка
    'user.subscribe' => [
        'pattern' => '/private/subscribe',
        'action'  => ['User\SubscribeAction', 'execute'],
        'method'  => ['POST'],
    ],
    // подписка
    'subscribe.create' => [
        'pattern' => '/subscribe/create',
        'action'  => ['Subscribe\Action', 'create'],
        'method'  => ['POST'],
    ],
    'subscribe.cancel' => [
        'pattern' => '/subscribe/cancel',
        'action'  => ['Subscribe\Action', 'cancel'],
    ],
    'subscribe.confirm' => [
        'pattern' => '/subscribe/confirm',
        'action'  => ['Subscribe\Action', 'confirm'],
    ],

    // qrcode
    'qrcode' => [
        'pattern' => '/qr/{qrcode}',
        'action'  => ['Qrcode\Action', 'execute'],
    ],

    'debug-curl' => [
        'pattern' => '/debug/curl',
        'action'  => ['CurlAction', 'execute'],
        'method'  => ['POST'],
    ],

    'debug-log' => [
        'pattern' => '/debug/log/{id}',
        'action'  => ['LogAction', 'execute'],
        'method'  => ['POST'],
    ],

    //cron
    'cron-index' => [
        'pattern' => '/cron',
        'action'  => ['Cron\IndexAction', 'execute'],
    ],
    'cron-task' => [
        'pattern' => '/cron/{task}',
        'action'  => ['Cron\Action', 'execute'],
    ],
    'cron-task-links' => [
        'pattern' => '/cron/{task}/links',
        'action'  => ['Cron\LinksAction', 'execute'],
    ],

    //content
    'content' => [
        'pattern' => '/{token}',
        'action'  => ['Content\Action', 'execute'],
    ],


];
