<?php

namespace EnterSite\Model\Partial {
    use EnterSite\Model\Partial;

    class Link extends Partial\Widget {
        public $widgetType = 'link';
        /** @var string */
        public $name;
        /** @var string */
        public $url;
    }
}
