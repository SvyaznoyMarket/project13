<?php

namespace EnterSite\Config {
    class Application {
        /** @var string */
        public $requestId;
        /** @var string */
        public $dir;
        /** @var string */
        public $environment;
        /**
         * Уровень отладки: 0 - нет, 1 - по умолчанию, 2 - подробно
         * @var int
         */
        public $debugLevel;
        /** @var string */
        public $hostname;
        /** @var Application\Logger */
        public $logger;
        /** @var Application\Session */
        public $session;
        /** @var Application\UserToken */
        public $userToken;
        /** @var Application\Region */
        public $region;
        /** @var Application\Credit */
        public $credit;
        /** @var Application\Curl */
        public $curl;
        /** @var Application\CoreService */
        public $coreService;
        /** @var Application\CmsService */
        public $cmsService;
        /** @var Application\AdminService */
        public $adminService;
        /** @var Application\ReviewService */
        public $reviewService;
        /** @var Application\ContentService */
        public $contentService;
        /** @var Application\RetailRocketService */
        public $retailRocketService;
        /** @var Application\MustacheRenderer */
        public $mustacheRenderer;
        /** @var array */
        public $mediaHosts = [];
        /** @var Application\Product */
        public $product;
        /** @var Application\ProductReview */
        public $productReview;
        /** @var Application\ProductPhoto */
        public $productPhoto;
        /** @var Application\ProductCategoryPhoto */
        public $productCategoryPhoto;
        /** @var Application\Search */
        public $search;
        /** @var Application\Promo */
        public $promo;
        /** @var Application\DirecCredit */
        public $directCredit;

        public function __construct() {
            $this->logger = new Application\Logger();

            $this->session = new Application\Session();
            $this->userToken = new Application\UserToken();

            $this->region = new Application\Region();
            $this->credit = new Application\Credit();

            $this->curl = new Application\Curl();

            $this->coreService = new Application\CoreService();
            $this->cmsService = new Application\CmsService();
            $this->adminService = new Application\AdminService();
            $this->reviewService = new Application\ReviewService();
            $this->contentService = new Application\ContentService();
            $this->retailRocketService = new Application\RetailRocketService();

            $this->mustacheRenderer = new Application\MustacheRenderer();

            $this->product = new Application\Product();
            $this->productReview = new Application\ProductReview();
            $this->productPhoto = new Application\ProductPhoto();
            $this->productCategoryPhoto = new Application\ProductCategoryPhoto();
            $this->search = new Application\Search();
            $this->promo = new Application\Promo();

            $this->directCredit = new Application\DirecCredit();
        }
    }
}

namespace EnterSite\Config\Application {
    class Logger {
        /** @var Logger\FileAppender */
        public $fileAppender;

        public function __construct() {
            $this->fileAppender = new Logger\FileAppender();
        }
    }

    class Session {
        /** @var string */
        public $name;
        /** @var int */
        public $cookieLifetime;
        /** @var string */
        public $cookieDomain;
    }

    class UserToken {
        /**
         * Кука авторизованного пользователя
         * @var string
         */
        public $authCookieName;
    }

    class Region {
        /** @var string */
        public $defaultId;
        /** @var string */
        public $cookieName;
    }

    class Credit {
        /** @var string */
        public $cookieName;
    }

    class Curl {
        /** @var int */
        public $queryChunkSize;
        /** @var bool */
        public $logResponse;
        /** @var float */
        public $timeout;
        /** @var float */
        public $retryTimeout;
        /** @var int */
        public $retryCount;
    }

    class CurlService {
        /** @var string */
        public $url;
        /** @var string */
        public $user;
        /** @var string */
        public $password;
        /** @var float */
        public $timeout;
        /** @var float */
        public $hugeTimeout;

        public function __construct() {}
    }

    class CoreService extends CurlService {
        /** @var string */
        public $clientId;
    }

    class CmsService extends CurlService {
    }

    class AdminService extends CurlService {
        /** @var bool */
        public $enabled;
    }

    class ReviewService extends CurlService {
    }

    class ContentService extends CurlService {
    }

    class RetailRocketService extends CurlService {
        /** @var string */
        public $account;
    }

    class MustacheRenderer {
        /** @var string */
        public $dir;
        /** @var string */
        public $templateDir;
        /** @var string */
        public $cacheDir;
        /** @var string */
        public $templateClassPrefix;
    }

    class Product {
        /**
         * Количество элементов на страницу
         * @var int
         */
        public $itemPerPage;
        /**
         * Количество элементов в слайдере
         * @var int
         */
        public $itemsInSlider;
    }

    class ProductPhoto {
        /**
         * @var array
         */
        public $urlPaths = [];
    }

    class ProductReview {
        /** @var bool */
        public $enabled;
        /**
         * Количество элементов в карточке товара
         * @var int
         */
        public $itemsInCard;
    }

    class ProductCategoryPhoto {
        /**
         * @var array
         */
        public $urlPaths = [];
    }

    class Search {
        /** @var int */
        public $minPhraseLength;
    }

    class Promo {
        /**
         * @var array
         */
        public $urlPaths = [];
    }

    class DirecCredit {
        /** @var bool */
        public $enabled;
        /** @var int */
        public $minPrice;
        /** @var string */
        public $partnerId;
    }
}

namespace EnterSite\Config\Application\Logger {
    abstract class BaseAppender {
        /** @var bool */
        public $enabled;
    }

    class FileAppender extends BaseAppender {
        /** @var string */
        public $file;
    }
}
