<?php

namespace EnterSite\Model\Page\Product {
    use EnterSite\Model\JsonPage;
    use EnterSite\Model\Partial;

    class RecommendedList extends JsonPage {
        /** @var Partial\ProductSlider|null */
        public $alsoBoughtSlider;
        /** @var Partial\ProductSlider|null */
        public $alsoViewedSlider;
        /** @var Partial\ProductSlider|null */
        public $similarSlider;
    }
}
