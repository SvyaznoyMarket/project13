<?php

namespace EnterSite\Model\Partial {
    class ProductFilter {
        /** @var string */
        public $token;
        /** @var string */
        public $name;
        /** @var string */
        public $unit;
        /** @var bool */
        public $isSliderType;
        /** @var bool */
        public $isListType;
        /** @var bool */
        public $isHiddenType;
        /** @var bool */
        public $isMultiple;
        /** @var bool */
        public $isOpened;
        /** @var bool */
        public $isPrice;
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
        public $minValue;
        /** @var string */
        public $maxValue;
        /** @var string */
        public $id;
        /** @var bool */
        public $isActive;
        /** @var string */
        public $deleteUrl;
    }
}
