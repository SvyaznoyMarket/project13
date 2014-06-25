<?php

namespace EnterSite\Model\Page\Product {
    use EnterSite\Model\Partial;

    class ListByFilter {
        /**
         * Виджеты, индексированные по css-селектору
         * @var Partial\Widget[]
         */
        public $widgets = [];
        /** @var string[] */
        public $productCards = [];
        /** @var int */
        public $count;
        /** @var int */
        public $page;
        /** @var int */
        public $limit;

    }
}