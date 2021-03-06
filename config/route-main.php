<?php

return [
    'homepage' => [
        'urls' => ['/'],
        'action'  => ['Main\Action', 'index'],
    ],

    'ssi.userConfig' => [
        'urls' => ['/ssi/user-config'],
        'action'  => ['Ssi\UserConfigAction', 'execute'],
    ],
    'ssi.navigation' => [
        'urls' => ['/ssi/navigation'],
        'action'  => ['MainMenu\Get', 'execute'],
    ],
    'ssi.main.categoryBlock' => [
        'urls' => ['/ssi/main/category-block'],
        'action'  => ['Main\CategoryBlock', 'execute'],
    ],

    'mainMenu.recommendation' => [
        'urls' => ['/main_menu/recommendations/{rootCategoryId}/{childIds}'],
        'action'  => ['MainMenu\RecommendedAction', 'execute'],
        'require' => [
            'categoryId' => '\d+',
        ],
    ],

    // поиск
    'search' => [
        'urls' => ['/search'],
        'outFilters' => ['page' => '(?:[2-9]|\d{2,})'],
        'action'  => ['Search\Action', 'execute'],
    ],
    // автоподстановка поиска
    'search.autocomplete' => [
        'urls' => ['/search/autocomplete'],
        'action'  => ['Search\Autocomplete', 'execute'],
    ],
    // рекомендации в поиске
    'search.recommended' => [
        'urls' => ['/search/recommended'],
        'action'  => ['Search\RecommendedAction', 'execute'],
    ],
    // вход пользователя
    'user.login' => [
        'urls' => ['/login'],
        'action'  => ['User\Action', 'login'],
    ],
    // регистрация пользователя
    'user.register' => [
        'urls' => ['/register'],
        'action'  => ['User\Action', 'register'],
    ],
    // выход пользователя
    'user.logout' => [
        'urls' => ['/logout'],
        'action'  => ['User\Action', 'logout'],
        'method'  => ['GET'],
    ],
    // восстановление пароля
    'user.forgot' => [
        'urls' => ['/request-password'],
        'action'  => ['User\Action', 'forgot'],
    ],
    // проверка авторизации
    'user.checkAuth' => [
        'urls' => ['/user/check-auth'],
        'action'  => ['User\CheckAuthAction', 'execute'],
    ],
    // личный кабинет
    'user' => [
        'urls' => ['/private'],
        'action'  => ['User\IndexAction', 'execute'],
    ],
    // вход через социальные сети
    'user.login.external' => [
        'urls' => ['/login-{providerName}'],
        'action'  => ['User\ExternalLoginAction', 'execute'],
    ],
    // ответ от социальных сетей при входе пользователя
    'user.login.external.response' => [
        'urls' => ['/login-{providerName}/response'],
        'action'  => ['User\ExternalLoginResponseAction', 'execute'],
    ],
    'user.update' => [
        'urls' => ['/private/update'],
        'action'  => ['User\UpdateAction', 'execute'],
        'method'  => ['POST'],
    ],
    'user.update.password' => [
        'urls' => ['/private/update-password'],
        'action'  => ['User\UpdatePasswordAction', 'execute'],
        'method'  => ['POST'],
    ],

    // Регистрация поставщика
    'supplier.new' => [
        'urls'   => ['/supplier/new'],
        'action'    => ['Supplier\NewAction', 'execute']
    ],

    // Кабинет поставщика
    'supplier.cabinet' => [
        'urls'   => ['/supplier/cabinet'],
        'action'    => ['Supplier\CabinetAction', 'index']
    ],

    // Загрузка прайс-листа
    'supplier.load' => [
        'urls'   => ['/supplier/load'],
        'action'    => ['Supplier\CabinetAction', 'load'],
        'method'  => ['POST'],
    ],

    // Обновление данных о поставщике
    'supplier.update' => [
        'urls'   => ['/supplier/update'],
        'action'    => ['Supplier\CabinetAction', 'update'],
        'method'  => ['POST'],
    ],

    // смена региона
    'region.change' => [
        'urls' => ['/region/change/{regionId}'],
        'action'  => ['Region\Action', 'change'],
    ],
    // автоподстановка региона
    'region.autocomplete' => [
        'urls' => ['/region/autocomplete'],
        'action'  => ['Region\Action', 'autocomplete'],
    ],
    // автоопределение города
    'region.autoresolve' => [
        'urls' => ['/region/autoresolve'],
        'action'  => ['Region\Action', 'autoresolve'],
    ],
    // сменя региона по прямой ссылке
    'region.redirect' => [
        'urls' => ['/reg/{regionId}{redirectTo}'],
        'action'  => ['Region\Action', 'redirect'],
        'require' => [
            'regionId'   => '\d+',
            'redirectTo' => '.+',
        ],
    ],

    // магазины
    'shop' => [
        'urls' => ['/shops'],
        'action'  => ['Shop', 'execute'],
    ],
    'shop.region' => [ // deprecated
        'urls' => ['/shops/{regionId}'],
        'action'  => ['Shop\Region', 'execute'],
        'require' => [
            'regionId'   => '\d+',
        ],
    ],
    'shop.region.show' => [ // deprecated
        'urls' => ['/shops/{regionToken}/{shopToken}'],
        'action'  => ['Shop\Region\Show', 'execute'],
    ],
    'shop.show' => [
        'urls' => ['/shops/{pointToken}'],
        'action'  => ['Shop\Show', 'execute'],
    ],
    'shop.send' => [
        'urls' => ['/ajax/shops/{pointUi}/send'],
        'action'  => ['Shop\Send', 'execute'],
        'method'  => ['POST'],
    ],
    'tchibo' => [
        'urls' => ['/catalog/tchibo'],
        'action'  => ['Tchibo\IndexAction', 'execute'],
    ],
    'sale.all'  => [
        'urls'   => ['/secretsale'],
        'action'    => ['ClosedSale\SaleAction', 'index']
    ],
    'sale.one'  => [
        'urls'   => [
            '/secretsale/{uid}',
            '/secretsale/{uid}/page-{page}',
        ],
        'action'    => ['ClosedSale\SaleAction', 'show'],
        'require' => ['uid' => '[\w\d-_]+', 'page' => '\d+'],
        'outFilters' => ['page' => '(?:[2-9]|\d{2,})'],
    ],
    'product.gift' => [
        'urls' => [
            '/gift',
            '/gift/page-{page}',
        ],
        'require' => ['page' => '\d+'],
        'outFilters' => ['page' => '(?:[2-9]|\d{2,})'],
        'action'  => ['Gift\ProductCategory\Action', 'category'],
    ],
    'product.set' => [
        'urls' => [
            '/products/set/{productBarcodes}',
            '/products/set/{productBarcodes}/page-{page}',
        ],
        'require' => ['page' => '\d+'],
        'outFilters' => ['page' => '(?:[2-9]|\d{2,})'],
        'action'  => ['Product\SetAction', 'execute'],
    ],
    'tag' => [
        'urls' => [
            '/tags/{tagToken}',
            '/tags/{tagToken}/page-{page}',
            '/tags/{tagToken}/{categoryToken}',
            '/tags/{tagToken}/{categoryToken}/page-{page}',
        ],
        'require' => ['page' => '\d+'],
        'outFilters' => ['page' => '(?:[2-9]|\d{2,})'],
        'action'  => ['Tag\Action', 'index'],
    ],
    'slice' => [
        'urls' => [
            '/slices/{sliceToken}',
            '/slices/{sliceToken}/page-{page}',
            '/slices/{sliceToken}/{categoryToken}',
            '/slices/{sliceToken}/{categoryToken}/page-{page}',
        ],
        'require' => ['sliceToken' => '[\w\d-_]+', 'categoryToken' => '[\w\d-_]+', 'page' => '\d+'],
        'outFilters' => ['page' => '(?:[2-9]|\d{2,})'],
        'action'  => ['Slice\Action', 'execute'],
    ],
    'product.category.slice' => [
        'urls' => [
            '/catalog/slice/{sliceToken}',
            '/catalog/slice/{sliceToken}/page-{page}',
            '/catalog/slice/{sliceToken}/brand-{brandToken}',
            '/catalog/slice/{sliceToken}/brand-{brandToken}/page-{page}',
        ],
        'require' => ['sliceToken' => '[\w\d-_]+', 'brandToken' => '[\w\d-_]+', 'page' => '\d+'],
        'outFilters' => ['page' => '(?:[2-9]|\d{2,})'],
        'action'  => ['Slice\Action', 'execute'],
    ],
    'product.category' => [
        'urls' => [
            '/catalog/{categoryPath}/page-{page}',
            '/catalog/{categoryPath}',
            '/catalog/{categoryPath}/{brandToken}/page-{page}',
            '/catalog/{categoryPath}/{brandToken}',
        ],
        'require' => ['categoryPath' => '[\w\d-_]+\/?[\w\d-_]+', 'brandToken' => '[\w\d-_]+', 'page' => '\d+'],
        'outFilters' => ['page' => '(?:[2-9]|\d{2,})'],
        'action'  => ['ProductCategory\Action', 'category'],
    ],
    //
    'ajax.category.listing.product' => [
        'urls' => ['/ajax/category/{categoryUi}/listing/product/{productUi}'],
        'action'  => ['Category\Listing\Product', 'execute'],
    ],
    // карточка товара
    'product' => [
        'urls' => ['/product/{productPath}'],
        'action'  => ['Product\IndexAction', 'execute'],
        'require' => ['productPath' => '[\w\d-_]+(?:\/[\w\d-_]+)?'],
    ],
    // расчет доставки товара
    'product.delivery' => [
        'urls' => ['/ajax/product/delivery'],
        'action'  => ['Product\DeliveryAction', 'execute'],
        'method'  => ['POST'],
    ],
    'ajax.product.delivery.map' => [
        'urls' => ['/ajax/product/{productUi}/delivery/map'],
        'action'  => ['Product\DeliveryAction', 'map']
    ],
    'ajax.product.property' => [
        'urls' => ['/ajax/product/{productUi}/property/{propertyId}'],
        'action'  => ['Product\Property', 'execute'],
    ],
    'ajax.order.payment.start' => [
        'urls' => ['/ajax/order/{orderAccessToken}/payment/{paymentMethodId}/start'],
        'action'  => ['OrderV3\Payment\StartAction', 'execute'],
    ],
    'order.payment.start' => [
        'urls' => ['/order/payment/fail'],
        'action'  => ['OrderV3\Payment\FailAction', 'execute'],
    ],
    //reviews
    'product.review.create' => [
        'urls' => ['/product-reviews/create/{productUi}'],
        'require' => ['productUi' => '[\w\d-_]+\/?[\w\d-_]+'],
        'action'  => ['Product\ReviewsAction', 'create'],
    ],
    'product.review.vote' => [
        'urls' => ['/product-reviews/vote'],
        'action'  => ['Product\ReviewsAction', 'vote'],
    ],
    'product.reviews' => [
        'urls' => ['/product-reviews/{productUi}'],
        'require' => ['productUi' => '[\w\d-_]+\/?[\w\d-_]+'],
        'action'  => ['Product\ReviewsAction', 'execute'],
    ],
    'product.notification.lowerPrice' => [
        'urls' => ['/ajax/product-notification/{productId}'],
        'require' => ['productId' => '\d+'],
        'action'  => ['Product\NotificationAction', 'lowerPrice'],
    ],
    'product.kit' => [
        'urls' => ['/ajax/product/kit/{productUi}'],
        'action'  => ['Product\KitAction', 'execute'],
    ],
    'product.viewed' => [
        'urls'   => ['/ajax/product/viewed'],
        'action'    => ['Product\ViewedAction', 'execute']
    ],

    // проверка сертификата
    'certificate.check' => [
        'urls' => ['/certificate-check'],
        'action'  => ['Certificate\Action', 'check'],
        'method'  => ['POST'],
    ],

    // корзина
    'cart' => [
        'urls' => ['/cart'],
        'action'  => ['Cart\IndexAction', 'execute'],
    ],
    // добавление списка товаров в корзину
    'cart.product.setList' => [
        'urls' => ['/cart/set-products'],
        'action'  => ['Cart\ProductAction', 'setList'],
    ],
    'cart.recommended' => [
        'urls' => ['/cart/recommended'],
        'action'  => ['Cart\RecommendedAction', 'execute'],
    ],

    // оформление заказа: 1-й шаг - контактные данные
    'orderV3' => [
        'urls' => ['/order/new'],
        'action'  => ['OrderV3\NewAction', 'execute'],
    ],
    // оформление заказа: 2-й шаг - выбор доставки
    'orderV3.delivery' => [
        'urls' => ['/order/delivery'],
        'action'  => ['OrderV3\DeliveryAction', 'execute'],
    ],
    // оформление заказа: создание
    'orderV3.create' => [
        'urls' => ['/order/create'],
        'action'  => ['OrderV3\CreateAction', 'execute'],
        'method'  => ['POST'],
    ],
    // оформление заказа: завершение, онлайн-оплата
    'orderV3.complete' => [
        'urls' => ['/order/complete'],
        'action'  => ['OrderV3\CompleteAction', 'execute'],
    ],
    'orderV3.paymentForm' => [
        'urls' => ['/order/getPaymentForm'],
        'action'  => ['OrderV3\CompleteAction', 'getPaymentForm'],
    ],
    'orderV3.update-credit'   => [
        'urls'   => ['/order/update-credit'],
        'action'    => ['OrderV3\CompleteAction', 'updateCredit'],
        'method'    => ['POST']
    ],
    'orderV3.set-credit-status'   => [
        'urls'   => ['/order/set-credit-status'],
        'action'    => ['OrderV3\CompleteAction', 'setCreditStatus'],
        'method'    => ['POST']
    ],
    'orderV3OneClick.delivery' => [
        'urls' => ['/order-1click/delivery'],
        'action'  => ['OrderV3OneClick\DeliveryAction', 'execute'],
    ],
    'orderV3OneClick.create' => [
        'urls' => ['/order-1click/create'],
        'action'  => ['OrderV3OneClick\CreateAction', 'execute'],
    ],
    'orderV3OneClick.form' => [
        'urls' => ['/order-1click/form/{productUid}'],
        'action'  => ['OrderV3OneClick\FormAction', 'execute'],
    ],
    'orderV3.status' => [
        'urls' => ['/order/status'],
        'action'  => ['OrderV3\StatusAction', 'execute'],
    ],

    'order.new' => [
        'urls' => ['/orders/new'],
        'action'  => ['Order\Action', 'newAction'],
    ],
    'order.complete' => [
        'urls' => ['/orders/complete'],
        'action'  => ['Order\Action', 'completeAction'],
    ],

    'order.slot.create' => [
        'urls' => ['/orders/slot/create'],
        'action'  => ['OrderSlot\Action', 'create'],
        'method'  => ['POST'],
    ],

    // промо каталоги
    'promo.show' => [
        'urls' => ['/promo/{promoToken}'],
        'require' => ['categoryToken' => '[\w\d-_]+'],
        'action'  => ['Promo\IndexAction', 'execute'],
    ],

    // recommended products
    'product.recommended' => [
        'urls' => ['/product-recommended'],
        'action' => ['Product\RecommendedAction', 'execute'],
    ],
    'product.upsale' => [
        'urls' => ['/ajax/upsale/{productId}'],
        'action' => ['Product\UpsaleAction', 'execute'],
        'require' => ['productId' => '\d+'],
    ],

    'main.recommended' => [
        'urls' => ['/main/recommended'],
        'action'  => ['Main\RecommendedAction', 'execute'],
    ],

    // smartchoice
    'product/smartchoice' => [
        'urls' => ['/ajax/product-smartchoice'],
        'action' => ['Product\SmartChoiceAction', 'execute'],
    ],

    // редактирование данных пользователя
    'user.edit' => [
        'urls' => ['/private/edit'],
        'action'  => ['User\EditAction', 'execute'],
    ],
    // редактирование данных пользователя
    'user.orders' => [
        'urls' => ['/private/orders'],
        'action'  => ['User\Order\IndexAction', 'execute'],
    ],
    'user.order.cancel' => [
        'urls' => ['/private/orders/cancel'],
        'action'  => ['User\Order\CancelAction', 'execute'],
    ],
    'user.favorites' => [
        'urls' => ['/private/favorites'],
        'action'  => ['User\FavoriteAction', 'get'],
    ],
    // данные о заказе пользователя
    'user.order' => [
        'urls'   => ['/private/order/{orderId}'],
        'action'    => ['User\Order\ShowAction', 'execute'],
        'require'   => ['orderId' => '\d+']
    ],
    // подписки пользователя
    'user.subscriptions' => [
        'urls' => ['/private/subscriptions'],
        'action'  => ['User\SubscriptionsAction', 'execute'],
    ],
    'user.notification.addProduct' => [
        'urls' => ['/private/notification/add-product'],
        'action'  => ['User\Notification\AddProductAction', 'execute'],
        'method'  => ['POST'],
    ],
    'user.address' => [
        'urls' => ['/private/address'],
        'action'  => ['User\Address\IndexAction', 'execute'],
    ],
    'user.address.create' => [
        'urls' => ['/private/address/create'],
        'action'  => ['User\Address\CreateAction', 'execute'],
    ],
    'user.address.delete' => [
        'urls' => ['/private/address/delete'],
        'action'  => ['User\Address\DeleteAction', 'execute'],
    ],
    'user.message' => [
        'urls' => ['/private/messages'],
        'action'  => ['User\Message\IndexAction', 'execute'],
    ],
    'user.unauthorizedInfo' => [
        'urls' => ['/user/unauthorized-info'],
        'action'  => ['User\UnauthorizedInfoAction', 'execute'],
    ],

    // маршрутизатор нескольких запросов
    'route' => [
        'urls' => ['/route'],
        'action'  => ['RouteAction', 'execute'],
        'method'  => ['POST'],
    ],

    // подписка
    'friendship' => [
        'urls' => ['/be-friends'],
        'action'  => ['Friendship\Action', 'execute'],
    ],

    // подписка
    'subscribe.create' => [
        'urls' => ['/subscribe/create'],
        'action'  => ['Subscribe\Action', 'create'],
        'method'  => ['POST'],
    ],
    // используется в письмах
    'subscribe.confirm' => [
        'urls' => ['/subscribe/confirm'],
        'action'  => ['Subscribe\Action', 'confirm'],
    ],
    // используется в письмах
    'subscribe.delete' => [
        'urls' => ['/subscribe/delete'],
        'action'  => ['Subscribe\Action', 'delete'],
    ],

    'event.push' => [
        'urls' => ['/event/push'],
        'action'  => ['EventAction', 'push'],
        'method'  => ['POST'],
    ],

    'debug.query' => [
        'urls' => ['/debug/query'],
        'action'  => ['QueryAction', 'index'],
    ],
    'debug.query.json' => [
        'urls' => ['/debug/query/{queryToken}.json'],
        'action'  => ['QueryAction', 'getJson'],
    ],
    'debug.query.show' => [
        'urls' => ['/debug/query/{queryToken}'],
        'action'  => ['QueryAction', 'show'],
    ],
    'subscribe.friend.show' => [
        'urls' => ['/enter-friends'],
        'action'  => ['Subscribe\FriendAction', 'show'],
    ],
    'subscribe.friend.create' => [
        'urls' => ['/enter-friends/create'],
        'action'  => ['Subscribe\FriendAction', 'create'],
        'method'  => ['POST'],
    ],

    'mobidengi' => [
        'urls' => ['/tele2'],
        'action'  => ['Mobidengi\IndexAction', 'execute'],
    ],
    'favorite.add' => [
        'urls' => ['/favorite/add-product/{productUi}'],
        'action'  => ['Favorite\SetAction', 'execute'],
        'require' => [
            'productUi' => '[\w\d-_]+',
        ],
    ],
    'favorite.delete' => [
        'urls' => ['/favorite/delete-product/{productUi}'],
        'action'  => ['Favorite\DeleteAction', 'execute'],
        'require' => [
            'productUi' => '[\w\d-_]+',
        ],
    ],
    'favorite.deleteProducts' => [
        'urls' => ['/favorite/delete-product-list'],
        'action'  => ['Favorite\DeleteListAction', 'execute'],
        'method'  => ['POST'],
    ],

    'wishlist.create' => [
        'urls' => ['/wishlist/create'],
        'action'  => ['Wishlist\CreateAction', 'execute'],
        'method'    => ['POST'],
    ],
    'wishlist.delete' => [
        'urls' => ['/wishlist/delete'],
        'action'  => ['Wishlist\DeleteAction', 'execute'],
        'method'    => ['POST'],
    ],
    'wishlist.addProduct' => [
        'urls' => ['/wishlist/add-product'],
        'action'  => ['Wishlist\AddProductAction', 'execute'],
        'method'  => ['POST'],
    ],
    'wishlist.deleteProduct' => [
        'urls' => ['/wishlist/delete-product'],
        'action'  => ['Wishlist\DeleteProductAction', 'execute'],
        'method'  => ['POST'],
    ],
    'wishlist.show' => [
        'urls' => ['/wishlist/{wishlistToken}'],
        'action'  => ['Wishlist\ShowAction', 'execute'],
        'require' => [
            'wishlistToken' => '[\w\d-_]+',
        ],
    ],

    'compare' => [
        'urls' => ['/compare'],
        'action'  => ['Compare\CompareAction', 'execute'],
    ],
    'compare.add' => [
        'urls' => ['/compare/add-product/{productId}'],
        'action'  => ['Compare\CompareAction', 'add'],
        'require' => ['productId' => '\d+']
    ],
    'compare.delete' => [
        'urls' => ['/compare/delete-product/{productId}'],
        'action'  => ['Compare\CompareAction', 'delete'],
        'require' => ['productId' => '\d+']
    ],
    'recommended' => [
        'urls' => ['/ajax/recommended'],
        'action'  => ['Recommended', 'execute'],
    ],

    // обратный звонок
    'user.callback.create' => [
        'urls'   => ['/user-callback/create'],
        'action'    => ['UserCallback\CreateAction', 'execute'],
        'method'    => ['POST']
    ],

    // Переключение АБ-тестов
    'switch' => [
        'urls'   => ['/switch'],
        'action'    => ['SwitchAction', 'execute']
    ],

	//content (должен быть в самом конце, иначе под паттерн попадут другие страницы)
    'content' => [
        'urls' => ['/{token}'],
        'action'  => ['Content\Action', 'execute'],
    ],
];
