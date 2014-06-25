<?php

namespace EnterSite\Model\Page\User {
    use EnterSite\Model\Partial;

    class Get {
        /** @var Get\User */
        public $user;
        /**
         * Виджеты, индексированные по css-селектору
         * @var Partial\Widget[]
         */
        public $widgets = [];

        public function __construct() {
            $this->user = new Get\User();
        }
    }
}

namespace EnterSite\Model\Page\User\Get {
    class User {
        /** @var string */
        public $sessionId;
    }
}