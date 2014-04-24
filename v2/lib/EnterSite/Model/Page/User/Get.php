<?php

namespace EnterSite\Model\Page\User {
    use EnterSite\Model\Partial;

    class Get {
        /**
         * Виджеты, индексированные по css-селектору
         * @var Get\WidgetContainer
         */
        public $widgetContainer;

        public function __construct() {
            $this->widgetContainer = new Get\WidgetContainer();
        }
    }
}

namespace EnterSite\Model\Page\User\Get {
    use EnterSite\Model\Partial;

    class WidgetContainer {
        /** @var Partial\UserBlock[] */
        public $userBlocks = [];
        /** @var Partial\Cart\ProductButton[] */
        public $buyButtons = [];
        /** @var Partial\Cart\ProductSpinner[] */
        public $buySpinners = [];
    }
}