<?php

namespace EnterSite\Config {
    class Application {
        /** @var string */
        public $requestId;
        /** @var string */
        public $dir;
        /** @var string */
        public $hostname;
        /** @var Application\Logger */
        public $logger;
        /** @var Application\Region */
        public $region;
        /** @var Application\CoreService */
        public $coreService;
        /** @var Application\CmsService */
        public $cmsService;
        /** @var Application\AdminService */
        public $adminService;
        /** @var Application\ReviewService */
        public $reviewService;
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

        public function __construct() {
            $this->logger = new Application\Logger();

            $this->region = new Application\Region();

            $this->coreService = new Application\CoreService();
            $this->cmsService = new Application\CmsService();
            $this->adminService = new Application\AdminService();
            $this->reviewService = new Application\ReviewService();

            $this->mustacheRenderer = new Application\MustacheRenderer();

            $this->product = new Application\Product();
            $this->productPhoto = new Application\ProductPhoto();
            $this->productReview = new Application\ProductReview();
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

    class Region {
        /** @var string */
        public $defaultId;
        /** @var string */
        public $cookieName;
    }

    class CurlService {
        /** @var string */
        public $url;
        /** @var string */
        public $user;
        /** @var string */
        public $password;
        /** @var int */
        public $timeout;
        /** @var int */
        public $hugeTimeout;
        /** @var int */
        public $retryTimeout;
        /** @var int */
        public $retryCount;

        public function __construct() {
            $this->retryTimeout = new CurlService\RetryTimeout();
        }
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
         * @name Количество элементов на страницу
         * @var int
         */
        public $itemPerPage;
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
         * @name Количество элементов в карточке товара
         * @var int
         */
        public $itemsInCard;
    }
}

namespace EnterSite\Config\Application\Logger {
    class FileAppender {
        /** @var string */
        public $file;
    }
}

namespace EnterSite\Config\Application\CurlService {
    class RetryTimeout {
        /** @var int */
        public $default;
        /** @var int */
        public $tiny;
        /** @var int */
        public $short;
        /** @var int */
        public $medium;
        /** @var int */
        public $long;
        /** @var int */
        public $huge;
    }
}
