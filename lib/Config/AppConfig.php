<?php

namespace Config;

require_once __DIR__ . '/Oauth/VkontakteConfig.php';
require_once __DIR__ . '/Oauth/OdnoklassnikiConfig.php';
require_once __DIR__ . '/Oauth/FacebookConfig.php';
require_once __DIR__ . '/Oauth/TwitterConfig.php';


class AppConfig {

    public $richRelevance = [
        'enabled'       => false,
        'apiKey'        => '',
        'apiClientKey'  => '',
        'apiUrl'        => '',
        'timeout'       => 0,
        'retryCount'    => 0,
        'jsUrl'         => '',
        'rcs_cookie'    => 'enter_rich_rcs'
    ];

    /** @var int */
    public $degradation; // для отладки - неспользовать!

    /**
     * Секретный ключ для формирования сигнатуры
     * @var string
     * @hidden
     */
    public $secretKey;

    /**
     * Использование очереди для обработки запросов (через websockets)
     * @var bool
     */
    public $useNodeMQ = false;
    public $nodeMQ = [
        'host'   => null,
        'port'  => null
    ];
    /** Проект Lite
     * @var bool
     */
    public $lite = [
        'enabled' => null
    ];
    /**
     * @var string
     * @hidden
     */
    public $encoding;
    /** @var bool */
    public $debug;
    /** @var array */
    public $logger = [
        'pretty'      => null,
        'emptyChance' => null, // вероятность нулевого логирования (при высоких нагрузках)
    ];
    /**
     * @var string
     * @hidden
     */
    public $appName;
    /** @var string */
    public $appDir;
    /**
     * @var string
     * @hidden
     */
    public $configDir;
    /**
     * @var string
     * @hidden
     */
    public $libDir;
    /**
     * @var string
     * @hidden
     */
    public $dataDir;
    /**
     * @var string
     * @hidden
     */
    public $logDir;
    /**
     * @var string
     * @hidden
     */
    public $webDir;
    /**
     * @var string
     * @hidden
     */
    public $templateDir;
    /**
     * @var string
     * @hidden
     */
    public $controllerPrefix;
    /** @var string */
    public $routeUrlPrefix;
    /** @var string */
    public $authToken = [
        'name'     => null,
        'authorized_cookie' => null,
        'disposableTokenParam'  => null
    ];
    /** @var array */
    public $session = [
        'name'              => null,
        'cookie_lifetime'   => null,
        'cookie_domain'     => null,
        'compareKey'        => null,
        'favouriteKey'      => null,
    ];
    /** @var array */
    public $redirect301 = [
        'enabled' => null,
    ];
    /** @var array */
    public $mobileRedirect = [
        'enabled' => null,
    ];

    public $curlCache = [
        'enabled'    => null,
        'delayRatio' => [0],
    ];

    /**
     * @var array
     */
    public $coreV2 = [
        'url'          => null,
        'client_id'    => null,
        'timeout'      => null,
        'hugeTimeout'  => null,
        'retryTimeout' => [],
        'retryCount'   => null,
        'debug'        => null,
        'chunk_size'   => null,
    ];

    /**
     * @var array
     */
    public $eventService = [
        'enabled'   => null,
        'url'       => null,
        'client_id' => null,
        'timeout'   => null,
    ];

    /**
     * @var array
     */
    public $crm = [
        'url'          => null,
        'client_id'    => null,
        'timeout'      => null,
        'hugeTimeout'  => null,
        'retryTimeout' => [],
        'retryCount'   => null,
        'debug'        => null,
        'chunk_size'   => null,
    ];

    /**
     * @var array
     * @hidden
     */
    public $corePrivate = [
        'url'          => null,
        'client_id'    => null,
        'user'         => null,
        'password'     => null,
        'timeout'      => null,
        'retryTimeout' => [],
        'retryCount'   => null,
    ];

    /**
     * @var array
     */
    public $searchClient = [
        'url'          => null,
        'client_id'    => null,
        'timeout'      => null,
        'hugeTimeout'  => null,
        'retryTimeout' => [],
        'retryCount'   => null,
        'debug'        => null,
        'chunk_size'   => null,
    ];

    /** @var array */
    public $reviewsStore = [
        'url'          => null,
        'timeout'      => null,
        'retryTimeout' => [],
        'retryCount'   => null,
    ];
    /** @var array */
    public $company = [
        'phone' => null,
        'moscowPhone' => null,
        'icq'   => null,
    ];
    /** @var array */
    public $analytics = [
        'enabled'           => null,
    ];
    /** @var array */
    public $googleAnalytics = [
        'enabled' => null,
        'secondary.enabled' => null,
    ];
    /** @var array */
    public $googleAnalyticsTchibo = [
        'enabled' => null,
    ];
    /** @var array */
    public $yandexMetrika = [
        'enabled' => null,
    ];
    /** @var array */
    public $googleTagManager = [
        'enabled' => null,
        'containerId' => null,
    ];
    /** @var array */
    public $partners = [
        'livetex' => [
            'enabled' => null,
            'liveTexID' => null,
            'login' => null,
            'password' => null,
        ],
        'criteo' => [
            'enabled' => null,
        ],
        'RetailRocket' => [
            'account' => null,
            'apiUrl' => null,
            'timeout' => null,
            'cookieLifetime' => null,
            'userEmail' => [
                'cookieName' => null,
            ],
        ],
        'TagMan' => [
            'enabled' => null,
        ],
        'admitad' => [
            'enabled' => null,
        ],
    ];

    /** @var array */
    public $adFox = [
        'enabled' => null,
    ];
    /** @var array */
    public $partner = [
        'cookieName'     => null,
        'cookieLifetime' => null,
    ];
    /** @var string */
    public $mainHost = null;
    /** @var string */
    public $mobileHost = null;
    /** @var string */
    public $description = null;
    /** @var array */
    /**
     * @var Oauth\VkontakteConfig
     * @hidden
     */
    public $vkontakteOauth;
    /**
     * @var Oauth\OdnoklassnikiConfig
     * @hidden
     */
    public $odnoklassnikiOauth;
    /**
     * @var Oauth\FacebookConfig
     * @hidden
     */
    public $facebookOauth;
    /**
     * @var Oauth\TwitterConfig
     * @hidden
     */
    public $twitterOauth;
    /** @var array */
    public $onlineCall = [
        'enabled' => null,
    ];
    /** @var array */
    public $region = [
        'cookieName'     => null,
        'cookieLifetime' => null,
        'defaultId'      => null,
        'autoresolve'    => null,
        'cache'          => null, // кешировать запросы к основным городам
    ];
    /** @var array */
    public $shop = [
        'cookieName'     => null,
        'cookieLifetime' => null,
        'autoresolve'    => null,
        'enabled'        => null,
    ];
    /** @var array */
    public $search = [
        'itemLimit' => null,
    ];
    /** @var array */
    public $product = [
        'itemsPerPage'           => null,
        'showAccessories'        => null,
        'showRelated'            => null,
        'getModelInListing'      => null, // запрашивать модели для листинга
        'getModelInCard'         => null, // запрашивать модели для карточки
        'deliveryCalc'           => null, // расчитывать доставку в карточке товара
        'showDeliveryPrice'      => null, // выводить цену доставки в карточке товара
        'smartChoiceEnabled'     => null,
        'breadcrumbsEnabled'     => null,
        'itemsInSlider'          => null,
        'itemsInCategorySlider'  => null,
        'totalCount'             => null,
        'allowBuyOnlyInshop'     => null, // позволять покупать товар, который находится только в магазине
        'reviewEnabled'          => null, // отзывы о товаре
        'creditEnabledInCard'    => null, // кнопка "Купить в кредит" в карточке товара
        'pushReview'             => null, // возможность добавлять отзывы
        'lowerPriceNotification' => null,
        'recommendationPull'     => null, // подтягивать информацию о рекоммендованных товарах
        'recommendationPush'     => null, // отправлять данные для расчета рекоммендованных товаров
        'recommendationProductLimit' => null,
    ];
    /** @var array */
    public $banner = [
        'timeout' => null,
        'checkStatus' => null, // проверять доступность баннеров SITE-5458
    ];
    /** @var array */
    public $payment = [
        'creditEnabled' => null,
        'blockedIds'    => [],
    ];
    /**
     * @var array
     * @hidden
     */
    public $creditProvider = [
        'kupivkredit' => [
            'partnerId'   => null,
            'partnerName' => null,
            'signature'   => null,
        ],
    ];

    /** @var array */
    public $cart = [
        'productLimit' => null, // максимальное количество товаров в корзине, при котором добавляемый товар не будет вытеснять первый товар из корзины
        'sessionName'  => null,
        'checkStock'   => null, // проверять количество товара при добавлении в корзину
        'updateTime'   => null, // период обновления корзины, минуты
        'oneClickOnly' => null, // только одноклик
    ];
    /** @var array */
    public $user = [
        'enabled'           => true,
        'tokenSessionKey'   => null,
        'corporateRegister' => null,
        'defaultRoute'      => null,
        'infoCookieName'    => null, // название куки с информацией о количестве заказов, избранных товаров и т.д.
    ];

    /**
     * @var array
     * @hidden
     */
    public $abTest = [
        'enabled'     => null,
        'cookieName'  => null,
        'tests'       => [],
    ];

    /** @var array */
    public $subscribe = [
        'enabled'    => null,
        'getChannel' => null,
        'cookieName' => null,
    ];

    /** @var array */
    public $mainMenu = [
        'recommendationsEnabled' => null,
        'maxLevel'               => null,
    ];
    /** @var array  */
    public $order = [
        'cookieName'              => null,
        'sessionName'             => null,
        'enableMetaTag'           => null,
        'maxSumOnline'            => null,
        'splitSessionKey'         => null,
        'splitUndoSessionKey'     => null,
        'splitAddressAdditionSessionKey' => null,
        'oneClickSplitSessionKey' => null,
        'creditStatusSessionKey'  => null,
        'channelSessionKey'       => null,
        'checkCertificate'        => null, // проверять сертификаты
    ];
    
    /** @var array */
    public $kladr = [
        'token' => null,
        'key' => null,
        'itemLimit' => 6,
    ];

    /** @var array */
    public $tchibo = [
        'rowWidth'   => null,
        'rowHeight'  => null,
        'rowPadding' => null,
        'analyticsEnabled' => null,
    ];

    /** @var array */
    public $tchiboSlider = [
        'analytics' => [
            'enabled' => null,
            'use_page_visibility' => null
        ],
    ];

    /** @var bool */
    public $preview = null;

    /** @var array */
    public $svyaznoyClub = [
        'cookieLifetime' => null,
        'userTicket' => [
            'cookieName' => null,
        ],
        'cardNumber' => [
            'cookieName' => null,
        ]
    ];

    /** @var array */
    public $fileStorage = [
        'url'          => null,
        'timeout'      => null,
        'retryTimeout' => [],
        'retryCount'   => null,
    ];

    /** @var array */
    public $flocktory = [
        'site_id'       => null,
        'postcheckout'  => null,
        'precheckout'   => null,
    ];

    /** @var array */
    public $scms = [
        'url'          => null,
        'timeout'      => null,
        'retryTimeout' => [],
        'retryCount'   => null,
    ];

    /** @var array */
    public $scmsV2 = [
        'url'          => null,
        'timeout'      => null,
        'retryTimeout' => [],
        'retryCount'   => null,
    ];

    /** @var string|null */
    public $rootCategoryUi;

    /** @var array */
    public $oauthEnabled = [
        'vkontakte'    => null,
        'facebook'     => null,
    ];

    /** @var array */
    public $scmsSeo = [
        'url'          => null,
        'timeout'      => null,
        'retryTimeout' => [],
        'retryCount'   => null,
    ];

    /** @var array */
    public $siteVersionSwitcher = [
        'cookieName'     => null,
        'cookieLifetime' => null,
    ];

    /**
     * Минимальная сумма заказа (для Воронежа)
     * @var int
     */
    public $minOrderSum = 0;

    /**
     * Обратный звонок
     * @var array
     */
    public $userCallback = [
        'timeFrom' => null,
        'timeTo'   => null,
    ];

    public function __construct() {

        $this->vkontakteOauth = new OAuth\VkontakteConfig();
        $this->odnoklassnikiOauth = new OAuth\OdnoklassnikiConfig();
        $this->facebookOauth = new OAuth\FacebookConfig();
        $this->twitterOauth = new OAuth\TwitterConfig();
    }

    public function __set($name, $value) {
        throw new \LogicException(sprintf('Неизвестный параметр "%s".', $name));
    }

    public function __get($name) {
        throw new \LogicException(sprintf('Неизвестный параметр "%s".', $name));
    }
}