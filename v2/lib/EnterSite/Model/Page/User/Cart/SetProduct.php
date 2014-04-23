<?php

namespace EnterSite\Model\Page\User\Cart {
    use EnterSite\Model\Partial;

    class SetProduct {
        /** @var Partial\Cart\ProductButton|null */
        public $buyButton;
        /** @var Partial\Cart\ProductSpinner|null */
        public $buySpinner;
    }
}