<?php

class AppConfig {
    /** @var string */
    public $env = 'dev';

    /** @var bool */
    public $debug = true;

    /** @var string */
    public $appName = 'Enter';

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
    public $authToken = array(
        'name'     => 'enter_auth',
        'lifetime' => 15552000 // 180 дней,
    );

    /** @var string */
    public $sessionToken = 'enter';

    /** @var array */
    public $coreV1 = array(
        'url'          => 'http://core.ent3.ru/v1/json',
        'client_id'    => 'site',
        'consumer_key' => 'test',
        'signature'    => 'test',
    );

    /** @var array */
    public $coreV2 = array(
        'url'       => 'http://core.ent3.ru/index.php/v2/',
        'client_id' => 'site',
    );

    public $wordpress = array(
        'url' => 'http://content.ent3.ru/',
    );

    /** @var array */
    public $googleAnalytics = array(
        'enabled' => false,
    );

    /** @var array */
    public $yandexMetrika = array(
        'enabled' => false,
    );

    /** @var array */
    public $onlineCall = array(
        'enabled' => false,
    );

    /** @var array */
    public $region = array(
        'cookieName'     => 'geoshop',
        'cookieLifetime' => 31536000, // 365 дней
        'defaultId'      => 14974,
    );

    public $mediaHost = array(
        0 => 'http://fs01.enter.ru',
        1 => 'http://fs02.enter.ru',
        2 => 'http://fs03.enter.ru',
        3 => 'http://fs04.enter.ru',
        4 => 'http://fs05.enter.ru',
        5 => 'http://fs06.enter.ru',
        6 => 'http://fs07.enter.ru',
        7 => 'http://fs08.enter.ru',
        8 => 'http://fs09.enter.ru',
        9 => 'http://fs10.enter.ru',
    );

    /** @var array */
    public $product = array(
        'itemsPerPage'          => 18,
        'showAccessories'       => true,
        'showRelated'           => true,
        'itemsInSlider'         => 5,
        'itemsInCategorySlider' => 3,
        'minCreditPrice'        => 3000,
    );

    /** @var array */
    public $productPhoto = array(
        'url' => array(
            0 => '/1/1/60/',
            1 => '/1/1/120/',
            2 => '/1/1/163/',
            3 => '/1/1/500/',
            4 => '/1/1/2500/',
        ),
    );

    /** @var array */
    public $productPhoto3d = array(
        'url' => array(
            0 => '/1/2/500/',
            1 => '/1/2/2500/',
        ),
    );

    /** @var array */
    public $productLabel = array(
        'url' => array(
            0 => 'http://fs01.enter.ru/7/1/66x23/',
            1 => 'http://fs01.enter.ru/7/1/124x38/',
        ),
    );

    /** @var array */
    public $productCategory = array(
        'url' => array(
            0 => 'http://fs01.enter.ru/6/1/163/',
        ),
    );

    /** @var array */
    public $service = array(
        'url' => array(
            0 => 'http://fs01.enter.ru/11/1/160/',
            1 => 'http://fs01.enter.ru/11/1/500/',
            2 => 'http://fs01.enter.ru/11/1/120/',
        ),
    );

    /** @var array */
    public $shopPhoto = array(
        'url' => array(
            0 => 'http://fs01.enter.ru/8/1/40/',
            1 => 'http://fs01.enter.ru/8/1/120/',
            2 => 'http://fs01.enter.ru/8/1/163/',
            3 => 'http://fs01.enter.ru/8/1/500/',
            4 => 'http://fs01.enter.ru/8/1/2500/',
            5 => 'http://fs01.enter.ru/8/1/original/',
        ),
    );

    final public function __construct() {
        $this->initialize();
    }

    protected function initialize() {
        mb_internal_encoding('UTF-8');

        $this->appDir = realpath(__DIR__ . '/..');
        $this->configDir = $this->appDir . '/config';
        $this->libDir = $this->appDir . '/lib';
        $this->dataDir = $this->appDir . '/data';
        $this->logDir = realpath($this->appDir . '/../log');
    }

    public function __set($name, $value) {
        throw new \LogicException(sprintf('Неизвестный параметр "%s".', $name));
    }

    public function __get($name) {
        throw new \LogicException(sprintf('Неизвестный параметр "%s".', $name));
    }
}