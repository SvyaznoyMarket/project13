<?php

namespace EnterSite\Model\Page\User {
    use EnterSite\Model\Partial;

    class Get {
        /** @var Partial\UserBlock */
        public $userBlock;
        /** @var Partial\Cart\ProductButton[] */
        public $buyButtons = [];
        /** @var Partial\Cart\ProductSpinner[] */
        public $buySpinners = [];

        public function __construct() {
            $this->userBlock = new Partial\UserBlock();
        }
    }
}
