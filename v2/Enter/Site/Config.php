<?php

namespace Enter\Site {
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
            $this->retryTimeout = new \Enter\Site\CurlService\RetryTimeout();
        }
    }

    class CoreService extends \Enter\Site\CurlService {
    }

    class AdminService extends \Enter\Site\CurlService {
        /** @var bool */
        public $enabled;
    }

    class Config {
        /** @var Region */
        public $region;
        /** @var CoreService */
        public $coreService;
        /** @var AdminService  */
        public $adminService;

        public function __construct() {
            $this->region = new Region();
            $this->coreService = new CoreService();
            $this->adminService = new AdminService();
        }
    }
}

namespace Enter\Site\CurlService {
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
