<?php

namespace Enter\Site\Config {
    class Application {
        /** @var Region */
        public $region;
        /** @var CoreService */
        public $coreService;
        /** @var CmsService  */
        public $cmsService;
        /** @var AdminService  */
        public $adminService;
        /** @var ProductList */
        public $productList;

        public function __construct() {
            $this->region = new Region();

            $this->coreService = new CoreService();
            $this->cmsService = new CmsService();
            $this->adminService = new AdminService();

            $this->productList = new ProductList();
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
            $this->retryTimeout = new \Enter\Site\Config\CurlService\RetryTimeout();
        }
    }

    class CoreService extends \Enter\Site\Config\CurlService {
    }

    class CmsService extends \Enter\Site\Config\CurlService {
    }

    class AdminService extends \Enter\Site\Config\CurlService {
        /** @var bool */
        public $enabled;
    }

    class ProductList {
        /**
         * @var int
         * @name Количество элементов на страницу
         */
        public $itemPerPage;
    }
}

namespace Enter\Site\Config\CurlService {
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
