<?php

namespace Config;

require_once __DIR__ . '/Oauth/VkontakteConfig.php';
require_once __DIR__ . '/Oauth/OdnoklassnikiConfig.php';
require_once __DIR__ . '/Oauth/FacebookConfig.php';
require_once __DIR__ . '/Oauth/TwitterConfig.php';


class AppConfig {
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
    /** @var array */
    public $session = [
        'name'            => null,
        'cookie_lifetime' => null,
        'cookie_domain'   => null,
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
    ];
    /** @var array */
    public $pickpoint = [
        'url'          => null,
        'timeout'      => null,
        'retryTimeout' => [],
        'retryCount'   => null,
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
        'Сpaexchange' => [
            'enabled' => null,
        ],
        'TagMan' => [
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
        'showAveragePrice'       => null,
        'allowBuyOnlyInshop'     => null, // позволять покупать товар, который находится только в магазине
        'reviewEnabled'          => null, // отзывы о товаре
        'pushReview'             => null, // возможность добавлять отзывы
        'lowerPriceNotification' => null,
        'recommendationPull'     => null, // подтягивать информацию о рекоммендованных товарах
        'recommendationPush'     => null, // отправлять данные для расчета рекоммендованных товаров
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

    /** @var array */
    public $f1Certificate = [
        'enabled' => null,
    ];
    /** @var array */
    public $cart = [
        'productLimit' => null, // максимальное количество товаров в корзине, при котором добавляемый товар не будет вытеснять первый товар из корзины
        'sessionName'  => null,
        'checkStock'   => null, // проверять количество товара при добавлении в корзину
        'updateTime'   => null, // период обновления корзины, минуты
    ];
    /** @var array */
    public $user = [
        'corporateRegister' => null,
        'defaultRoute'  => null
    ];

    /**
     * @var array
     * @hidden
     */
    public $abTest = [
        'cookieName'  => null,
        'tests'       => [],
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

    /** @var bool */
    public $mainMenu = [
        'requestMenu'               => null, // запрос главного меню из
        'recommendationsEnabled'    => null
    ];
    /** @var bool */
    public $newOrder;
    /** @var array  */
    public $order = [
        'cookieName'              => null,
        'sessionName'             => null,
        'enableMetaTag'           => null,
        'maxSumOnline'            => null,
        'splitSessionKey'         => null,
        'oneClickSplitSessionKey' => null,
        'sessionInfoOnComplete'   => null, // краткая инфа о заказе на странице order.complete
    ];
    /** @var bool */
    public $newDeliveryCalc;

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
     * @name Enterprize array SITE-2622
     * @var array
     */
    public $enterprize = [
        'enabled' => null,
        'formDataSessionKey' => null,
        'itemsInSlider' => null,
        'showSlider' => null,
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
            'use_page_visibility' => null,
            'collection_view' => [
                'enabled' => null,
                'tchiboOnly' => null
            ],
            'collection_click' => [
                'enabled' => null,
                'tchiboOnly' => null
            ],
            'product_click' => [
                'enabled' => null,
                'tchiboOnly' => null
            ],
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
	
	/**
	 * @var array настройки фотоконкурса
	 */
	public $photoContest = [
		'client'	=> []
	];

    /** @var array */
    public $fileStorage = [
        'url'          => null,
        'timeout'      => null,
        'retryTimeout' => [],
        'retryCount'   => null,
    ];

    /** @var array */
    public $flocktoryExchange = [
        'enabled' => null,
    ];

    /** @var array */
    public $flocktoryPostCheckout = [
        'enabled' => null,
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

    /** @var array */
    public $bandit = [
        'enabled'     => null,
    ];

    /** Платный самовывоз
     * @var array */
    public $self_delivery = [
        'limit'     => 0,
        'regions'   => []
    ];

    /** Минимальная сумма заказа (для Воронежа)
     * @var int
     */
    public $minOrderSum = 0;

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