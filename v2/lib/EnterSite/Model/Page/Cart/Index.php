<?php

namespace EnterSite\Model\Page\Cart {
    use EnterSite\Model\Page;
    use EnterSite\Model\Partial;

    class Index extends Page\DefaultLayout {
        /** @var Partial\ProductCard[] */
        public $productCards = [];
    }
}
