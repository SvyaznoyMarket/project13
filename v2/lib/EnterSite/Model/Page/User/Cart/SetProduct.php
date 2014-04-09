<?php

namespace EnterSite\Model\Page\User\Cart {
    use EnterSite\Model\JsonPage;
    use EnterSite\Model\Partial;

    class SetProduct extends JsonPage {
        /** @var Partial\Cart\ProductButton|null */
        public $buyButton;
        /** @var Partial\Cart\ProductSpinner|null */
        public $buySpinner;
    }
}