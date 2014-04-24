<?php

namespace EnterSite\Model\Partial\Cart {
    use EnterSite\Model\Partial;

    class ProductButton extends Partial\Widget {
        public $widgetType = 'productButton';
        /** @var string */
        public $id;
        /** @var string */
        public $spinnerWidgetId;
        /** @var string */
        public $text;
        /** @var string */
        public $url;
        /** @var string */
        public $class;
        /** @var string */
        public $dataUrl;
        /** @var string */
        public $dataValue;
    }
}
