<?php

namespace EnterSite\Model\Partial\Cart {
    use EnterSite\Model\Partial;

    class ProductSum extends Partial\Widget {
        public $widgetType = 'cartProductSum';
        /** @var float */
        public $value;
        /** @var string */
        public $shownValue;
    }
}
