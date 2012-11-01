<?php

namespace Config;

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
    public $authToken = array(
        'name'     => null,
        'lifetime' => null,
    );
    /** @var string */
    public $sessionToken;
    /** @var array */
    public $session = array(
        'name'            => null,
        'cookie_lifetime' => null,
    );
    /** @var array */
    public $coreV1 = array(
        'url'          => null,
        'client_id'    => null,
        'consumer_key' => null,
        'signature'    => null,
    );
    /** @var array */
    public $coreV2 = array(
        'url'       => null,
        'client_id' => null,
    );
    /** @var array */
    public $wordpress = array(
        'url' => null,
    );
    /** @var array */
    public $company = array(
        'phone' => null,
    );
    /** @var array */
    public $googleAnalytics = array(
        'enabled' => null,
    );
    /** @var array */
    public $yandexMetrika = array(
        'enabled' => null,
    );
    /** @var array */
    public $asset = array(
        'timestampEnabled' => null,
    );
    /** @var array */
    public $onlineCall = array(
        'enabled' => null,
    );
    /** @var array */
    public $region = array(
        'cookieName'     => null,
        'cookieLifetime' => null,
        'defaultId'      => null,
    );
    /** @var array */
    public $mediaHost = array();
    /** @var array */
    public $product = array(
        'itemsPerPage'          => null,
        'showAccessories'       => null,
        'showRelated'           => null,
        'itemsInSlider'         => null,
        'itemsInCategorySlider' => null,
        'minCreditPrice'        => null,
        'totalCount'            => null,
        // глобальный (без учета региона) список товаров
        'globalListEnabled'     => null,
    );
    /** @var array */
    public $productPhoto = array(
        'url' => array(),
    );
    /** @var array */
    public $productPhoto3d = array(
        'url' => array(),
    );
    /** @var array */
    public $productLabel = array(
        'url' => array(),
    );
    /** @var array */
    public $productCategory = array(
        'url' => array(),
    );
    /** @var array */
    public $service = array(
        'url'                 => array(),
        'minPriceForDelivery' => null,
    );
    /** @var array */
    public $shopPhoto = array(
        'url' => array(),
    );
    /** @var array */
    public $smartEngine = array(
        'pull' => null,
        'push' => null,
    );
    /** @var array */
    public $warranty = array(
        'enabled' => null,
    );

    public function __set($name, $value) {
        throw new \LogicException(sprintf('Неизвестный параметр "%s".', $name));
    }

    public function __get($name) {
        throw new \LogicException(sprintf('Неизвестный параметр "%s".', $name));
    }
}