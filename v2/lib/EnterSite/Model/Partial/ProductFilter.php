<?php

namespace EnterSite\Model\Partial {
    class ProductFilter {
        /** @var string */
        public $name;
        /** @var bool */
        public $isSliderType;
        /** @var bool */
        public $isListType;
        /** @var bool */
        public $isMultiple;
        /** @var string */
        public $dataValue;
        /** @var ProductFilter\Element[] */
        public $elements = [];
    }
}

namespace EnterSite\Model\Partial\ProductFilter {
    class Element {
        /** @var string */
        public $title;
        /** @var string */
        public $name;
        /** @var string */
        public $value;
        /** @var string */
        public $id;
        /** @var bool */
        public $isActive;
    }
}
