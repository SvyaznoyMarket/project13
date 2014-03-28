<?php

namespace EnterSite\Model\Page\Product {
    use EnterSite\Model\JsonPage;

    class RecommendedList extends JsonPage {
        /** @var RecommendedList\Block */
        public $alsoBought;
        /** @var RecommendedList\Block */
        public $alsoViewed;
        /** @var RecommendedList\Block */
        public $similar;
    }
}

namespace EnterSite\Model\Page\Product\RecommendedList {
    class Block {
        /** @var string */
        public $content;
        /** @var int */
        public $count = 0;
    }
}