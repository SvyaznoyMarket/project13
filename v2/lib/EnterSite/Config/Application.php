<?php

namespace EnterSite\Config {
    use EnterSite\Config\Application\Region;
    use EnterSite\Config\Application\CoreService;
    use EnterSite\Config\Application\CmsService;
    use EnterSite\Config\Application\AdminService;
    use EnterSite\Config\Application\MustacheRenderer;
    use EnterSite\Config\Application\ProductList;

    class Application {
        /** @var string */
        public $dir;
        /** @var string */
        public $hostname;
        /** @var Region */
        public $region;
        /** @var CoreService */
        public $coreService;
        /** @var CmsService  */
        public $cmsService;
        /** @var AdminService  */
        public $adminService;
        /** @var MustacheRenderer  */
        public $mustacheRenderer;
        /** @var ProductList */
        public $productList;

        public function __construct() {
            $this->region = new Region();

            $this->coreService = new CoreService();
            $this->cmsService = new CmsService();
            $this->adminService = new AdminService();

            $this->mustacheRenderer = new MustacheRenderer();

            $this->productList = new ProductList();
        }
    }
}

namespace EnterSite\Config\Application {
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

    class ProductList {
        /**
         * @var int
         * @name Количество элементов на страницу
         */
        public $itemPerPage;
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
