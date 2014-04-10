<?php

namespace EnterSite\Model\Page\User {
    use EnterSite\Model\JsonPage;
    use EnterSite\Model\Partial;

    class Get extends JsonPage {
        /** @var Partial\Cart\ProductButton[] */
        public $buyButtons = [];
        /** @var Partial\Cart\ProductSpinner[] */
        public $buySpinners;
    }
}