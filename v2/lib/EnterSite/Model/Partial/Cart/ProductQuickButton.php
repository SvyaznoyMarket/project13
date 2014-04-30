<?php

namespace EnterSite\Model\Partial\Cart {
    use EnterSite\Model\Partial;

    class ProductQuickButton extends Partial\Widget {
        public $widgetType = 'productQuickButton';
        /** @var string */
        public $id;
        /** @var string */
        public $url;
        /** @var string */
        public $dataUrl;
        /** @var string */
        public $dataValue;
    }
}
