<?php

namespace EnterSite\Model\Page\Product {
    use EnterSite\Model\Partial;

    class RecommendedList {
        /** @var Partial\ProductSlider|null */
        public $alsoBoughtSlider;
        /** @var Partial\ProductSlider|null */
        public $alsoViewedSlider;
        /** @var Partial\ProductSlider|null */
        public $similarSlider;
    }
}
