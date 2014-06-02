<?php

namespace EnterSite\Model\Partial {

    use EnterSite\Model\Partial;

    class SortingBlock extends Partial\Widget {
        public $widgetType = 'productSorting';
        /** @var SortingBlock\Sorting */
        public $sorting;
        /** @var SortingBlock\Sorting[] */
        public $sortings = [];
    }
}

namespace EnterSite\Model\Partial\SortingBlock {
    class Sorting {
        /** @var string */
        public $name;
        /** @var string */
        public $url;
        /** @var string */
        public $dataValue;
        /** @var bool */
        public $isActive;
    }
}