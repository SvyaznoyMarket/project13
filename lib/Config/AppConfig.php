<?php

namespace Config;

require_once __DIR__ . '/Oauth/VkontakteConfig.php';
require_once __DIR__ . '/Oauth/OdnoklassnikiConfig.php';
require_once __DIR__ . '/Oauth/FacebookConfig.php';

class AppConfig {
    /** @var string */
    public $encoding;
    /** @var bool */
    public $debug;
    /** @var string */
    public $appName;
    /** @var string */
    public $appDir;
    /** @var string */
    public $configDir;
    /** @var string */
    public $libDir;
    /** @var string */
    public $dataDir;
    /** @var string */
    public $logDir;
    /** @var string */
    public $webDir;
    /** @var string */
    public $templateDir;
    /** @var string */
    public $cmsDir;
    /** @var string */
    public $controllerPrefix;
    /** @var string */
    public $routePrefix;
    /** @var string */
    public $authToken = [
        'name'     => null,
    ];
    /** @var string */
    public $sessionToken;
    /** @var array */
    public $session = [
        'name'            => null,
        'cookie_lifetime' => null,
    ];
    /** @var string */
    public $cacheCookieName = null;
    /** @var array */
    public $coreV2 = [
        'url'          => null,
        'client_id'    => null,
        'timeout'      => null,
        'retryTimeout' => [],
        'retryCount'   => null,
    ];
    /** @var array */
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
    ];
    /** @var array */
    public $dataStore = [
        'url'          => null,
        'timeout'      => null,
        'retryTimeout' => [],
        'retryCount'   => null,
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
        'optimizelyEnabled' => null,
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
    public $myThings = [
        'feeByCategory' => [],
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
    /** @var Oauth\VkontakteConfig */
    public $vkontakteOauth;
    /** @var Oauth\OdnoklassnikiConfig */
    public $odnoklassnikiOauth;
    /** @var Oauth\FacebookConfig */
    public $facebookOauth;
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
        // глобальный (без учета региона) список товаров
        'globalListEnabled'      => null,
        'showAveragePrice'       => null,
        'allowBuyOnlyInshop'     => null, // позволять покупать товар, который находится только в магазине
        'reviewEnabled'          => null, // отзывы о товаре
        'lowerPriceNotification' => null,
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
        'creditEnabled' => null,
    ];
    /** @var array */
    public $creditProvider = [
        'kupivkredit' => [
            'partnerId'   => null,
            'partnerName' => null,
            'signature'   => null,
        ],
    ];
    /** @var array */
    public $paymentPsb = [
        'terminal'     => null,
        'merchant'     => null,
        'merchantName' => null,
        'key'          => null,
        'payUrl'       => null,
    ];
    public $paymentPsbInvoice = [
        'contractorId' => null,
        'key'          => null,
        'payUrl'       => null,
    ];
    /** @var array */
    public $smartengine = [
        'pull'           => null,
        'push'           => null,
        'apiUrl'         => null,
        'apiKey'         => null,
        'tenantid'       => null,
        'logEnabled'     => null,
        'logDataEnabled' => null,
    ];
    /** @var array */
    public $crossss = [
        'enabled' => null,
        'timeout' => null,
        'apiUrl'  => null,
        'apiKey'  => null,
        'id'      => null,
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
    public $cart = [
        'productLimit'    => null, // максимальное количество товаров в корзине, при котором добавляемый товар не будет вытеснять первый товар из корзины
    ];
    /** @var array */
    public $user = [
        'corporateRegister' => null,
    ];
    /** @var array */
    public $abtest = [
        'cookieName' => null,
        'bestBefore' => null,
        'enabled'    => null,
        'test'       => [],
    ];
    /** @var array */
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
    /** @var array */
    public $queue = [
        'pidFile' => null,
        'workerLimit' => null,
        'maxLockTime' => null,
    ];
    /** @var boolean */
    public $requestMainMenu = null;
    /** @var array */
    public $mobileModify = [ // SITE-1035
        'enabled' => null,
    ];
    /** @var array  */
    public $order = [
        'enableMetaTag'   => null,
    ];
    /** @var array */
    public $maybe3d = [
        'xmlUrl' => null,
        'customerId' => null,
        'swfUrl' => null,
        'cmsFolder' => null,
        'timeout' => null,
    ];

    public function __construct() {
        $this->vkontakteOauth = new OAuth\VkontakteConfig();
        $this->odnoklassnikiOauth = new OAuth\OdnoklassnikiConfig();
        $this->facebookOauth = new OAuth\FacebookConfig();
    }

    public function __set($name, $value) {
        throw new \LogicException(sprintf('Неизвестный параметр "%s".', $name));
    }

    public function __get($name) {
        throw new \LogicException(sprintf('Неизвестный параметр "%s".', $name));
    }
}