<?php

namespace EnterSite\Model\Partial\Cart {
    use EnterSite\Model\Partial;

    class ProductButton extends Partial\Widget {
        public $widgetType = 'productButton';
        /** @var string */
        public $id;
        /** @var string */
        public $text;
        /** @var string */
        public $url;
        /** @var string */
        public $dataUrl;
        /** @var string */
        public $dataValue;
        /** @var bool */
        public $isDisabled;
        /** @var bool */
        public $isInShopOnly;
        /** @var bool */
        public $isInCart;
        /** @var bool */
        public $isQuick;
    }
}
