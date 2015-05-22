<?php

return [
    // главная страница
    'homepage' => [
        'pattern' => '/',
        'action'  => ['Main\Action', 'index'],
    ],

    'homepage.recommendations' => [
        'pattern' => '/index/recommend',
        'action'  => ['Main\Action', 'recommendations'],
    ],

    'category.mainMenu' => [ // TODO: переименовать в mainMenu
        'pattern' => '/category/main_menu',
        'action'  => ['ProductCategory\MainMenuAction', 'execute'],
    ],
    'category.mainMenu.region' => [ // TODO: переименовать в mainMenu.region
        'pattern' => '/category/main_menu/{regionId}',
        'action'  => ['ProductCategory\MainMenuAction', 'execute'],
        'require' => [
            'regionId' => '\d+',
        ],
    ],
    'mainMenu.recommendation' => [
        'pattern' => '/main_menu/recommendations/{rootCategoryId}/{childIds}',
        'action'  => ['MainMenu\RecommendedAction', 'execute'],
        'require' => [
            'categoryId' => '\d+',
        ],
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
    // рекомендации в поиске
    'search.recommended' => [
        'pattern' => '/search/recommended',
        'action'  => ['Search\RecommendedAction', 'execute'],
    ],

    // инфо пользователя
    'user.info' => [
        'pattern' => '/ajax/user/info',
        'action'  => ['User\InfoAction', 'execute'],
    ],
    /*// Статус подписки пользователя, получить
    'user.subscribe.getStatus' => [
        'pattern' => '/ajax/subscribe/status/get',
        'action'  => ['User\InfoAction', 'getSubscribeStatus'],
    ],
    // Статус подписки пользователя, установить
    'user.subscribe.setStatus' => [
        'pattern' => '/ajax/subscribe/status/set/{status}',
        'action'  => ['User\InfoAction', 'setSubscribeStatus'],
    ],*/
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
    // регистрация c доп полями, аналогичка регистрации enterprize но не участвует coupon
    'user.registrationExtended' => [
        'pattern' => '/registrationExtended',
        'action'  => ['User\Action', 'registrationExtended'],
    ],
    // Изменение регистрационных данных
    'user.updateRegistration' => [
        'pattern' => '/updateRegistration',
        'action'  => ['User\Action', 'updateRegistration'],
    ],
    // регистрация корпоративного пользователя
    'user.registerCorporate' => [
        'pattern' => '/b2b',
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
    'user.get' => [
        'pattern' => '/user/get',
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

    // Регистрация поставщика
    'supplier.new' => [
        'pattern'   => '/supplier/new',
        'action'    => ['Supplier\NewAction', 'execute']
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

    // срезы. каталог товаров
    'product.category.slice' => [
        'pattern' => '/catalog/slice/{sliceToken}',
        'action'  => ['Slice\ShowAction', 'execute'],
        'require' => ['sliceToken' => '[\w\d-_]+'],
    ],

    // tchibo
    'tchibo' => [
        'pattern' => '/catalog/tchibo',
        'action'  => ['Tchibo\IndexAction', 'execute'],
    ],
    'tchibo.where_buy' => [
        'pattern'   => '/where_buy_tchibo',
        'action'    => ['Shop\Action', 'index']
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
        'require' => ['categoryPath' => '[\w\d-_]+\/[\w\d-_]+', 'brandToken' => '[\w\d-_]+'],
    ],
    // каталог товаров бренда
    'product.category.brand.infinity' => [
        'pattern' => '/ajax/catalog/{categoryPath}/{brandToken}/_infinity',
        'action'  => ['ProductCategory\Action', 'category'],
        'require' => ['categoryPath' => '[\w\d-_]+\/[\w\d-_]+', 'brandToken' => '[\w\d-_]+'],
    ],
    'product.category.brand.sliderInfinity' => [
        'pattern' => '/catalog/{categoryPath}/{brandToken}/_sliderInfinity',
        'action'  => ['ProductCategory\Action', 'category'],
        'require' => ['categoryPath' => '[\w\d-_]+\/[\w\d-_]+', 'brandToken' => '[\w\d-_]+'],
    ],

    // каталог товаров
    'product.gift' => [
        'pattern' => '/gift',
        'action'  => ['Gift\ProductCategory\Action', 'category'],
    ],

    // карточка товара
    'product' => [
        'pattern' => '/product/{productPath}',
        'action'  => ['Product\IndexAction', 'execute'],
        'require' => ['productPath' => '[\w\d-_]+\/{1}[\w\d-_]+'],
    ],
    // расчет доставки товара
    'old.product.delivery' => [
        'pattern' => '/product/delivery-info',
        'action'  => ['Product\OldDeliveryAction', 'info'],
        'method'  => ['POST'],
    ],
    'product.delivery_1click' => [
        'pattern' => '/product/delivery1click',
        'action'  => ['Product\OldDeliveryAction', 'oneClick'],
    ],
    // расчет доставки товара
    'product.delivery' => [
        'pattern' => '/ajax/product/delivery',
        'action'  => ['Product\DeliveryAction', 'execute'],
        'method'  => ['POST'],
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
    //reviews
    'product.review.create' => [
        'pattern' => '/product-reviews/create/{productUi}',
        'require' => ['productUi' => '[\w\d-_]+\/?[\w\d-_]+'],
        'action'  => ['Product\ReviewsAction', 'create'],
    ],
    'product.reviews' => [
        'pattern' => '/product-reviews/{productUi}',
        'require' => ['productUi' => '[\w\d-_]+\/?[\w\d-_]+'],
        'action'  => ['Product\ReviewsAction', 'execute'],
    ],
    'product.notification.lowerPrice' => [
        'pattern' => '/ajax/product-notification/{productId}',
        'require' => ['productId' => '\d+'],
        'action'  => ['Product\NotificationAction', 'lowerPrice'],
    ],
    'product.kit' => [
        'pattern' => '/ajax/product/kit/{productUi}',
        'action'  => ['Product\KitAction', 'execute'],
    ],

    // теги
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
    'cart.product.set' => [ // TODO cart.product.set изменмить Url
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
    // добавление товара в корзину
    'cart.oneClick.product.set' => [
        'pattern' => '/cart/one-click/add-product/{productId}',
        'action'  => ['Cart\OneClick\ProductAction', 'set'],
    ],
    // удаление товара из корзины
    'cart.oneClick.product.delete' => [
        'pattern' => '/cart/one-click/delete-product/{productId}',
        'action'  => ['Cart\OneClick\ProductAction', 'delete'],
    ],
    // изменение товара в корзине
    'cart.oneClick.product.change' => [
        'pattern' => '/cart/one-click/change-product/{productId}',
        'action'  => ['Cart\OneClick\ProductAction', 'change'],
    ],
    // добавление списка товаров в корзину
    'cart.oneClick.product.setList' => [
        'pattern' => '/cart/one-click/set-products',
        'action'  => ['Cart\OneClick\ProductAction', 'setList'],
    ],

    'cart.certificate.apply' => [
        'pattern' => '/cart/f1-certificate',
        'action'  => ['Cart\CertificateAction', 'apply'],
    ],
    'cart.certificate.delete' => [
        'pattern' => '/cart/f1-certificate/delete',
        'action'  => ['Cart\CertificateAction', 'delete'],
    ],
    'cart.sum' => [
        'pattern' => '/cart/sum',
        'action'  => ['Cart\SumAction', 'execute'],
    ],
    'cart.recommended' => [
        'pattern' => '/cart/recommended',
        'action'  => ['Cart\RecommendedAction', 'execute'],
    ],

    // оформление заказа: 1-й шаг - контактные данные
    'orderV3' => [
        'pattern' => '/order/new',
        'action'  => ['OrderV3\NewAction', 'execute'],
    ],
    // оформление заказа: 1-й шаг - один клик
    'orderV3.one-click' => [
        'pattern' => '/order/new/one-click',
        'action'  => ['OrderV3\NewAction', 'execute'],
    ],
    // оформление заказа: 2-й шаг - выбор доставки
    'orderV3.delivery' => [
        'pattern' => '/order/delivery',
        'action'  => ['OrderV3\DeliveryAction', 'execute'],
    ],
    // оформление заказа: 2-й шаг - выбор доставки
    'orderV3.delivery.one-click' => [
        'pattern' => '/order/delivery/one-click',
        'action'  => ['OrderV3\DeliveryAction', 'execute'],
    ],
    // оформление заказа: создание
    'orderV3.create' => [
        'pattern' => '/order/create',
        'action'  => ['OrderV3\CreateAction', 'execute'],
        'method'  => ['POST'],
    ],
    // оформление заказа: завершение, онлайн-оплата
    'orderV3.complete' => [
        'pattern' => '/order/complete',
        'action'  => ['OrderV3\CompleteAction', 'execute'],
    ],
    'orderV3.paymentForm' => [
        'pattern' => '/order/getPaymentForm',
        'action'  => ['OrderV3\CompleteAction', 'getPaymentForm'],
    ],
    // ошибки
    "orderV3.error" => [
        'pattern'   => '/order/error',
        'action'    => ['OrderV3\ErrorAction', 'execute']
    ],
    'orderV3.log'   => [
        'pattern'   => '/order/log',
        'action'    => ['OrderV3\OrderV3', 'logFromWeb'],
        'method'    => ['POST']
    ],
    'orderV3.update-credit'   => [
        'pattern'   => '/order/update-credit',
        'action'    => ['OrderV3\CompleteAction', 'updateCredit'],
        'method'    => ['POST']
    ],
    // Подари жизнь (новое оформление)
    'orderV3.lifegift' => [
        'pattern'   => '/order/life-gift/{productId}',
        'require' => ['productId' => '\d+'],
        'action'    => ['OrderV3\LifeGiftAction', 'execute']
    ],

    'orderV3.lifegift.complete' => [
        'pattern'   => '/order/life-gift/complete',
        'action'    => ['OrderV3\LifeGiftAction', 'complete']
    ],

    'orderV3.svyaznoyClub.complete' => [
        'pattern' => '/orders/svyaznoy-club',
        'action'  => ['OrderV3\CompleteAction', 'execute'],
        'method'  => ['GET'],
    ],

    'orderV3OneClick.delivery' => [
        'pattern' => '/order-1click/delivery',
        'action'  => ['OrderV3OneClick\DeliveryAction', 'execute'],
    ],
    'orderV3OneClick.create' => [
        'pattern' => '/order-1click/create',
        'action'  => ['OrderV3OneClick\CreateAction', 'execute'],
    ],
    'orderV3OneClick.form' => [
        'pattern' => '/order-1click/form/{productUid}',
        'action'  => ['OrderV3OneClick\FormAction', 'execute'],
    ],

    // заказ
    'order.oneClick.new' => [
        'pattern' => '/orders/one-click/new',
        'action'  => ['Order\OneClick\NewAction', 'execute'],
    ],
    'order' => [
        'pattern' => '/orders/new',
        'action'  => ['Order\NewAction', 'execute'],
    ],
    'order.complete' => [
        'pattern' => '/orders/complete',
        'action'  => ['Order\Action', 'complete'],
    ],

    'order.slot.create' => [
        'pattern' => '/orders/slot/create',
        'action'  => ['OrderSlot\Action', 'create'],
        'method'  => ['POST'],
    ],

    // промо каталоги
    'promo.show' => [
        'pattern' => '/promo/{promoToken}',
        'require' => ['categoryToken' => '[\w\d-_]+'],
        'action'  => ['Promo\IndexAction', 'execute'],
    ],

    // срезы
    'slice.category' => [
        'pattern' => '/slices/{sliceToken}/{categoryToken}',
        'require' => ['sliceToken' => '[\w\d-_]+', 'categoryToken' => '[\w\d-_]+'],
        'action'  => ['Slice\ShowAction', 'execute'],
    ],
    'slice.show' => [
        'pattern' => '/slices/{sliceToken}',
        'require' => ['sliceToken' => '[\w\d-_]+'],
        'action'  => ['Slice\ShowAction', 'execute'],
    ],

    // recommended products
    'product.recommended' => [
        'pattern' => '/product-recommended',
        'action' => ['Product\RecommendedAction', 'execute'],
    ],
    'product.upsale' => [
        'pattern' => '/ajax/upsale/{productId}',
        'action' => ['Product\UpsaleAction', 'execute'],
        'require' => ['productId' => '\d+'],
    ],

    'main.recommended' => [
        'pattern' => '/main/recommended',
        'action'  => ['Main\RecommendedAction', 'execute'],
    ],

    // smartchoice
    'product/smartchoice' => [
        'pattern' => '/ajax/product-smartchoice',
        'action' => ['Product\SmartChoiceAction', 'execute'],
    ],

    // редактирование данных пользователя
    'user.edit' => [
        'pattern' => '/private/edit',
        'action'  => ['User\EditAction', 'execute'],
    ],
    'user.edit.sclubNumber' => [
        'pattern' => '/ajax/user/edit-sclub-number',
        'action'  => ['User\EditAction', 'editSclubNumber'],
    ],
    // редактирование данных пользователя
    'user.orders' => [
        'pattern' => '/private/orders',
        'action'  => ['User\OrdersAction', 'execute'],
    ],
    // данные о заказе пользователя
    'user.order' => [
        'pattern'   => '/private/order/{orderId}',
        'action'    => ['User\OrderAction', 'execute'],
        'require'   => ['orderId' => '\d+']
    ],
    'user.recommend' => [
        'pattern'   => '/private/recommends',
        'action'    => ['User\RecommendAction', 'execute'],
    ],
    // подписки пользователя
    'user.subscriptions' => [
        'pattern' => '/private/subscriptions',
        'action'  => ['User\SubscriptionsAction', 'execute'],
    ],
    // адвокат клиента
    // @deprecated
    'user.consultation' => [
        'pattern' => '/private/consultation',
        'action'  => ['User\ConsultationAction', 'execute'],
    ],

    // маршрутизатор нескольких запросов
    'route' => [
        'pattern' => '/route',
        'action'  => ['RouteAction', 'execute'],
        'method'  => ['POST'],
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
    'subscribe.confirm' => [
        'pattern' => '/subscribe/confirm',
        'action'  => ['Subscribe\Action', 'confirm'],
    ],

    // qrcode
    'qrcode' => [
        'pattern' => '/qr/{qrcode}',
        'action'  => ['Qrcode\Action', 'execute'],
    ],

    'debug.query' => [
        'pattern' => '/debug/query',
        'action'  => ['QueryAction', 'index'],
    ],

    'debug.query.show' => [
        'pattern' => '/debug/query/{queryToken}',
        'action'  => ['QueryAction', 'show'],
    ],

    'debug.log' => [
        'pattern' => '/debug/log/{id}',
        'action'  => ['LogAction', 'execute'],
        'method'  => ['POST'],
    ],

    'debug.info' => [
        'pattern' => '/debug/info',
        'action'  => ['DebugAction', 'info'],
    ],
    'debug.session' => [
        'pattern' => '/debug/session',
        'action'  => ['DebugAction', 'session'],
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

    // enterprize
    'enterprize' => [
        'pattern' => '/enterprize',
        'action'  => ['Enterprize\IndexAction', 'execute'],
    ],
    'enterprize.create' => [
        'pattern' => '/enterprize/create',
        'action'  => ['Enterprize\CouponAction', 'create'],
    ],
    'enterprize.complete' => [
        'pattern' => '/enterprize/complete',
        'action'  => ['Enterprize\CouponAction', 'complete'],
    ],
    'enterprize.fail' => [
        'pattern' => '/enterprize/fail',
        'action'  => ['Enterprize\CouponAction', 'fail'],
    ],
    // enterprize retail
    'enterprize.retail.show' => [
        'pattern' => '/fishka',
        'action'  => ['Enterprize\RetailClient', 'show'],
    ],
    'enterprize.retail.create' => [
        'pattern' => '/enterprize/retail/create',
        'action'  => ['Enterprize\RetailClient', 'create'],
        'method'  => ['POST'],
    ],
    // enterprize form
    'enterprize.form.update' => [
        'pattern' => '/enterprize/form/update',
        'action'  => ['Enterprize\FormAction', 'update'],
        'method'  => ['POST'],
    ],
    'enterprize.form.show' => [
        'pattern' => '/enterprize/form/{enterprizeToken}',
        'action'  => ['Enterprize\FormAction', 'show'],
    ],
    // enterprize confirmPhone
    'enterprize.confirmPhone.create' => [
        'pattern' => '/enterprize/confirm-phone/create',
        'action'  => ['Enterprize\ConfirmPhoneAction', 'create'],
        'method'  => ['POST'],
    ],
    'enterprize.confirmPhone.check' => [
        'pattern' => '/enterprize/confirm-phone/check',
        'action'  => ['Enterprize\ConfirmPhoneAction', 'check'],
        'method'  => ['POST'],
    ],
    'enterprize.confirmPhone.show' => [
        'pattern' => '/enterprize/confirm-phone',
        'action'  => ['Enterprize\ConfirmPhoneAction', 'show'],
    ],
    // enterprize confirmEmail
    'enterprize.confirmEmail.create' => [
        'pattern' => '/enterprize/confirm-email/create',
        'action'  => ['Enterprize\ConfirmEmailAction', 'create'],
        'method'  => ['POST'],
    ],
    'enterprize.confirmEmail.check' => [
        'pattern' => '/enterprize/confirm-email/check',
        'action'  => ['Enterprize\ConfirmEmailAction', 'check'],
        //'method'  => ['POST'],
    ],
    'enterprize.confirmEmail.warn' => [
        'pattern' => '/enterprize/confirm-email/warn',
        'action'  => ['Enterprize\ConfirmEmailAction', 'warn'],
        ],
    'enterprize.confirmEmail.show' => [
        'pattern' => '/enterprize/confirm-email',
        'action'  => ['Enterprize\ConfirmEmailAction', 'show'],
    ],

    /** confirmation without coupon */
    // получение тела формы
    'enterprize.confirmAll.form' => [
        'pattern' => '/enterprize/confirm-wc/form',
        'action'  => ['Enterprize\ConfirmAction', 'form'],
    ],
    // подтверждаем телефон
    'enterprize.confirmAll.createPhone' => [
        'pattern' => '/enterprize/confirm-wc/create-phone',
        'action'  => ['Enterprize\ConfirmAction', 'createConfirmPhone'],
    ],
    // подтверждаем телефон
    'enterprize.confirmAll.phone' => [
        'pattern' => '/enterprize/confirm-wc/phone',
        'action'  => ['Enterprize\ConfirmAction', 'confirmPhone'],
    ],
    // запрашиваем подтверждение email
    'enterprize.confirmAll.createEmail' => [
        'pattern' => '/enterprize/confirm-wc/create-email',
        'action'  => ['Enterprize\ConfirmAction', 'createConfirmEmail'],
    ],
    // подтверждаем email
    'enterprize.confirmAll.email' => [
        'pattern' => '/enterprize/confirm-wc/email',
        'action'  => ['Enterprize\ConfirmAction', 'confirmEmail'],
    ],
    // делаем пользователя участником программы
    'enterprize.confirmAll.enterprize' => [
        'pattern' => '/enterprize/confirm-wc/setEnterprize',
        'action'  => ['Enterprize\ConfirmAction', 'setEnterprize'],
    ],
    // получения состояния "подтержденности" данных
    'enterprize.confirmAll.state' => [
        'pattern' => '/enterprize/confirm-wc/state',
        'action'  => ['Enterprize\ConfirmAction', 'state'],
    ],

    'enterprize.show' => [
        'pattern' => '/enterprize/{enterprizeToken}',
        'action'  => ['Enterprize\ShowAction', 'execute'],
    ],

    'enterprize.slider' => [
        'pattern' => '/enterprize-slider/{enterprizeToken}',
        'action'  => ['Enterprize\SliderAction', 'execute'],
    ],

    'mobidengi' => [
        'pattern' => '/tele2',
        'action'  => ['Mobidengi\IndexAction', 'execute'],
    ],

    // git pull
    'git.pull' => [
        'pattern' => '/git/pull',
        'action'  => ['GitAction', 'pull'],
        'method'  => ['GET'],
    ],
    // git checkout
    'git.checkout' => [
        'pattern' => '/git/checkout/{version}',
        'action'  => ['GitAction', 'checkout'],
        'method'  => ['GET'],
        'require' => [
            'version' => '\d+',
        ],
    ],

	/**
	 * Фотоконкурс
	 */
	'pc.homepage' => [
        'pattern' => '/contest',
        'action'  => ['Photocontest\IndexAction', 'index'],
		'require' => [
            'order'	=> '\w{1}',
			'page'	=> '\d{1,2}'
        ],
    ],
	
	'pc.service.safeKey' => [
        'pattern' => '/contest/sk',
        'action'  => ['Photocontest\PhotoAction', 'safeKey'],
    ],
	
	'pc.photo.unvote' => [
        'pattern' => '/contest/unvote/{id}',
        'action'  => ['Photocontest\PhotoAction', 'unvote'],
		'require' => [
            'id'		=> '\d+'
        ],
    ],
	
	'pc.photo.vote' => [
        'pattern' => '/contest/vote/{id}',
        'action'  => ['Photocontest\PhotoAction', 'vote'],
		'require' => [
            'id'		=> '\d+'
        ],
    ],
	
	'pc.photo.create' => [
        'pattern' => '/contest/{contestRoute}/add',
        'action'  => ['Photocontest\PhotoAction', 'create'],
		'require' => [
            'contestRoute'	=> '[A-z0-9_]+',
        ],
    ],
	
	
	'pc.photo.show' => [
        'pattern' => '/contest/{contestRoute}/{id}',
        'action'  => ['Photocontest\PhotoAction', 'show'],
		'require' => [
            'id'		=> '\d+',
			'contestRoute'		=> '[A-z0-9_]+',
        ],
    ],

	'pc.contest'	=> [
		'pattern'	=> '/contest/{contestRoute}',
		'action'  => ['Photocontest\IndexAction', 'contest'],
		'require' => [
            'contestRoute'	=> '[A-z0-9_]+',
			'order'	=> '\w{1}',
			'page'	=> '\d{1,2}'
        ],
	],
	
	/**
	 * game.center
	 */
	'game.slots' => [
		'pattern'	=> '/game/slots',
		'action'	=> ['Game\BanditAction', 'index'],
	],
	'game.slots.init' => [
		'pattern'	=> '/game/slots/init',
		'action'	=> ['Game\BanditAction', 'init'],
	],
	'game.slots.play' => [
		'pattern'	=> '/game/slots/play',
		'action'	=> ['Game\BanditAction', 'play'],
	],

    'bandit' => [
        'pattern' => '/bandit',
        'action'  => ['Bandit\IndexAction', 'execute'],
    ],

    'favorite.add' => [
        'pattern' => '/favorite/add-product',
        'action'  => ['Favorite\SetAction', 'execute'],
    ],

    'compare' => [
        'pattern' => '/compare',
        'action'  => ['Compare\CompareAction', 'execute'],
    ],
    'compare.add' => [
        'pattern' => '/compare/add-product/{productId}',
        'action'  => ['Compare\CompareAction', 'add'],
        'require' => ['productId' => '\d+']
    ],
    'compare.delete' => [
        'pattern' => '/compare/delete-product/{productId}',
        'action'  => ['Compare\CompareAction', 'delete'],
        'require' => ['productId' => '\d+']
    ],
    'compare.clear' => [
        'pattern' => '/compare/clear',
        'action'  => ['Compare\CompareAction', 'clear'],
    ],

    // Внешние ссылки на карточку товара
    'product.internal' => [
        'pattern'   => '/internal/product',
        'action'    => ['Product\ShowInternalAction', 'execute'],
    ],

    // Переключение АБ-тестов
    'switch' => [
        'pattern'   => '/switch',
        'action'    => ['SwitchAction', 'execute']
    ],

	//content (должен быть в самом конце, иначе под паттерн попадут другие страницы)
    'content' => [
        'pattern' => '/{token}',
        'action'  => ['Content\Action', 'execute'],
    ],
];
