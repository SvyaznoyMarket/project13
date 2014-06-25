<?php

namespace EnterSite\Model\Partial\Cart {
    use EnterSite\Model\Partial;

    class ProductLink extends Partial\Widget {
        public $widgetType = 'productLink';
        /** @var string */
        public $id;
        /** @var string */
        public $quantity;
        /** @var string */
        public $url;
    }
}
