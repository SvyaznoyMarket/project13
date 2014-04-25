<?php

namespace EnterSite\Model\Partial\Cart {
    use EnterSite\Model\Partial;

    class ProductSpinner extends Partial\Widget {
        public $widgetType = 'productSpinner';
        /** @var string */
        public $id;
        /** @var string */
        public $buttonId;
        /** @var string */
        public $class;
        /** @var int */
        public $value;
        /** @var bool */
        public $isDisabled;
    }
}
