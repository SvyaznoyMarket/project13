<?php

namespace EnterSite\Config {
    use EnterSite\Config\Application\Logger;
    use EnterSite\Config\Application\Region;
    use EnterSite\Config\Application\CoreService;
    use EnterSite\Config\Application\CmsService;
    use EnterSite\Config\Application\AdminService;
    use EnterSite\Config\Application\ReviewService;
    use EnterSite\Config\Application\MustacheRenderer;
    use EnterSite\Config\Application\Product;
    use EnterSite\Config\Application\ProductReview;

    class Application {
        /** @var string */
        public $requestId;
        /** @var string */
        public $dir;
        /** @var string */
        public $hostname;
        /** @var Logger */
        public $logger;
        /** @var Region */
        public $region;
        /** @var CoreService */
        public $coreService;
        /** @var CmsService  */
        public $cmsService;
        /** @var AdminService  */
        public $adminService;
        /** @var ReviewService  */
        public $reviewService;
        /** @var MustacheRenderer  */
        public $mustacheRenderer;
        /** @var Product */
        public $product;
        /** @var ProductReview */
        public $productReview;

        public function __construct() {
            $this->logger = new Logger();

            $this->region = new Region();

            $this->coreService = new CoreService();
            $this->cmsService = new CmsService();
            $this->adminService = new AdminService();
            $this->reviewService = new ReviewService();

            $this->mustacheRenderer = new MustacheRenderer();

            $this->product = new Product();
            $this->productReview = new ProductReview();
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
        public $retryTimeout;
        /** @var int */
        public $retryCount;

        public function __construct() {
            $this->retryTimeout = new \EnterSite\Config\Application\CurlService\RetryTimeout();
        }
    }

    class CoreService extends CurlService {
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
         * @var int
         * @name Количество элементов на страницу
         */
        public $itemPerPage;
    }

    class ProductReview {
        /** @var bool */
        public $enabled;
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
