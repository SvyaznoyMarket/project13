<?php

namespace Config;

//require_once __DIR__ . '/Oauth/VkontakteConfig.php';
//require_once __DIR__ . '/Oauth/OdnoklassnikiConfig.php';
//require_once __DIR__ . '/Oauth/FacebookConfig.php';

class AppConfig {
    /**
     * @var string
     * @hidden
     */
    public $encoding;
    /** @var bool */
    public $debug;
    /** @var array */
    public $logger = [
        'pretty' => null,
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
    /** @var string */
    public $cmsDir;
    /**
     * @var string
     * @hidden
     */
    public $controllerPrefix;
    /** @var string */
    public $routePrefix;
    /** @var string */
    public $authToken = [
        'name'     => null,
        'authorized_cookie' => null,
    ];
    /** @var string */
    public $sessionToken;
    /** @var array */
    public $session = [
        'name'            => null,
        'cookie_lifetime' => null,
        'cookie_domain'   => null,
    ];
    /** @var string */
    public $cacheCookieName = null;
    /** @var array */
    public $redirect301 = [
        'enabled' => null,
    ];
    /** @var array */
    public $mobileRedirect = [
        'enabled' => null,
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
    /** @var array */
    public $wordpress = [
        'url'            => null,
        'timeout'        => null,
        'throwException' => null,
        'retryTimeout' => [],
        'retryCount'   => null,
    ];
    /** @var array */
    public $dataStore = [
        'url'          => null,
        'timeout'      => null,
        'retryTimeout' => [],
        'retryCount'   => null,
    ];
    /** @var bool */
    public $connectTerminal = null;
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
        'optimizelyEnabled' => null,
    ];
    /** @var array */
    public $kissmentrics = [
        'enabled'    =>  null,
        'cookieName' => [
            'needUpdate' => null,
        ],
    ];
    /** @var array */
    public $pickpoint = [
        'url'          => null,
        'timeout'      => null,
        'retryTimeout' => [],
        'retryCount'   => null,
    ];
    /** @var array */
    public $shopScript = [
        'enabled'      => null,
        'url'          => null,
        'timeout'      => null,
        'retryTimeout' => [],
        'retryCount'   => null,
        'user'         => null,
        'password'     => null,
    ];
    /** @var array  */
    public $jsonLog = [
        'enabled' => null,
    ];
    /** @var array */
    public $googleAnalytics = [
        'enabled' => null,
    ];
    /** @var array */
    public $yandexMetrika = [
        'enabled' => null,
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
        ],
        'Admitad' => [
            'enabled' => null,
        ],
        'AdLens' => [
            'enabled' => null,
        ],
        'Сpaexchange' => [
            'enabled' => null,
        ],
        'Revolvermarketing' => [
            'enabled' => null,
        ],
    ];


    /**
     * @var array
     * @hidden
     */
    public $myThings = [
        'feeByCategory' => [],
        'cookieName'    =>  null,
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
    /** @var array */
    /**
     * @var Oauth\VkontakteConfig
     * @hidden
     */
    //public $vkontakteOauth;
    /**
     * @var Oauth\OdnoklassnikiConfig
     * @hidden
     */
    //public $odnoklassnikiOauth;
    /**
     * @var Oauth\FacebookConfig
     * @hidden
     */
    //public $facebookOauth;
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
    ];
    /** @var array */
    public $shop = [
        'cookieName'     => null,
        'cookieLifetime' => null,
        'autoresolve'    => null,
        'enabled'        => null,
    ];
    /** @var bool */
    public $loadMediaHost = null;
    /** @var array */
    public $mediaHost = [];
    /** @var array */
    public $search = [
        'itemLimit' => null,
    ];
    /** @var array */
    public $product = [
        'itemsPerPage'           => null,
        'showAccessories'        => null,
        'showRelated'            => null,
        'itemsInSlider'          => null,
        'itemsInCategorySlider'  => null,
        'minCreditPrice'         => null,
        'totalCount'             => null,
        'globalListEnabled'      => null, // глобальный (без учета региона) список товаров
        'showAveragePrice'       => null,
        'allowBuyOnlyInshop'     => null, // позволять покупать товар, который находится только в магазине
        'reviewEnabled'          => null, // отзывы о товаре
        'pushReview'             => null, // возможность добавлять отзывы
        'lowerPriceNotification' => null,
        'furnitureConstructor'   => null, // конструктор шкафов-купе
        'recommendationPull'     => null, // подтягивать информацию о рекоммендованных товарах
        'recommendationPush'     => null, // отправлять данные для расчета рекоммендованных товаров
    ];
    /** @var array */
    public $productPhoto = [
        'url' => [],
    ];
    /** @var array */
    public $productPhoto3d = [
        'url' => [],
    ];
    /** @var array */
    public $productLabel = [
        'url' => [],
    ];
    /** @var array */
    public $productCategory = [
        'url' => [],
    ];
    /** @var array */
    public $service = [
        'url'                 => [],
        'minPriceForDelivery' => null,
    ];
    /** @var array */
    public $serviceCategory = [
        'url' => [],
    ];
    /** @var array */
    public $shopPhoto = [
        'url' => [],
    ];
    /** @var array */
    public $banner = [
        'timeout' => null,
        'url'     => [],
    ];
    /** @var array */
    public $payment = [
        'creditEnabled'    => null,
        'paypalECS' => null,
        'blockedIds'       => [],
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
    /**
     * @var array
     * @hidden
     */
    public $paymentPsb = [
        'terminal'     => null,
        'merchant'     => null,
        'merchantName' => null,
        'key'          => null,
        'payUrl'       => null,
    ];
    /**
     * @var array
     * @hidden
     */
    public $paymentPsbInvoice = [
        'contractorId' => null,
        'key'          => null,
        'payUrl'       => null,
    ];
    /**
     * @var array
     * @hidden
     */
    public $smartengine = [
        'apiUrl'         => null,
        'apiKey'         => null,
        'tenantid'       => null,
        'logEnabled'     => null,
        'logDataEnabled' => null,
    ];

    /** @var array */
    public $warranty = [
        'enabled' => null,
    ];
    /** @var array */
    public $f1Certificate = [
        'enabled' => null,
    ];
    /** @var array */
    public $coupon = [
        'enabled' => null,
    ];
    /** @var array */
    public $blackcard = [
        'enabled' => null,
    ];
    /** @var array */
    public $cart = [
        'productLimit'    => null, // максимальное количество товаров в корзине, при котором добавляемый товар не будет вытеснять первый товар из корзины
    ];
    /** @var array */
    public $user = [
        'corporateRegister' => null,
    ];

    /**
     * @var array
     * @hidden
     */
    public $abtest = [
        'cookieName'  => null,
        'bestBefore'  => null,
        'enabled'     => null,
        'checkPeriod' => null,
        'test'        => [],
    ];

    /**
     * @var array
     * @hidden
     */
    public $database = [
        'host'     => null,
        'name'     => null,
        'user'     => null,
        'password' => null,
    ];
    /** @var array */
    public $subscribe = [
        'enabled'    => null,
        'cookieName' => null,
    ];

    /**
     * @var array
     * @hidden
     */
    public $queue = [
        'pidFile' => null,
        'workerLimit' => null,
        'maxLockTime' => null,
    ];

    /** @var boolean */
    public $requestMainMenu = null;
    /** @var array  */
    public $order = [
        'cookieName'     => null,
        'sessionName'    => null,
        'enableMetaTag'  => null,
        'maxSumOnline'   => null,
    ];
    /** @var bool */
    public $newDeliveryCalc;
    /**
     * @var array
     * @hidden
     */
    public $maybe3d = [
        'xmlUrl' => null,
        'customerId' => null,
        'swfUrl' => null,
        'cmsFolder' => null,
        'timeout' => null,
    ];
    /** @var array */
    public $img3d = [
        'cmsFolder' => null,
    ];
    /** @var array */
    public $tag = [
        'numSidebarCategoriesShown' => null,
    ];

    /** @var array */
    public $sphinx = [
        'showFacets' => null,
        'showListingSearchBar' => null,
    ];

    /**
     * @name Акция "ПодариЖизнь"
     * @var array
     */
    public $lifeGift = [
        'enabled'  => null,
        'regionId' => null,
        'labelId'  => null,
    ];

    /**
     * @name Enterprize SITE-2622
     * @var array
     */
    public $enterprize = [
        'enabled' => null,
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
    ];

    /** @var boolean */
    public $preview = null;

    public function __construct() {
        //$this->vkontakteOauth = new OAuth\VkontakteConfig();
        //$this->odnoklassnikiOauth = new OAuth\OdnoklassnikiConfig();
        //$this->facebookOauth = new OAuth\FacebookConfig();
    }

    public function __set($name, $value) {
        throw new \LogicException(sprintf('Неизвестный параметр "%s".', $name));
    }

    public function __get($name) {
        throw new \LogicException(sprintf('Неизвестный параметр "%s".', $name));
    }
}