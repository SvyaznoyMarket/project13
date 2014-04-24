<?php

namespace EnterSite\Model\Partial {
    use EnterSite\Model\Partial;

    class UserBlock extends Partial\Widget {
        public $widgetType = 'userBlock';
        /** @var bool */
        public $isUserAuthorized;
        /** @var bool */
        public $isCartNotEmpty;
        /** @var Partial\Link */
        public $userLink;
        /** @var UserBlock\Cart */
        public $cart;

        public function __construct() {
            $this->widgetId = 'id-userBlock';
            $this->userLink = new Partial\Link();
            $this->cart = new UserBlock\Cart();
        }
    }
}

namespace EnterSite\Model\Partial\UserBlock {
    class Cart {
        /** @var string */
        public $url;
        /** @var int */
        public $quantity;
        /** @var int */
        public $sum;
        /** @var string */
        public $shownSum;
    }
}