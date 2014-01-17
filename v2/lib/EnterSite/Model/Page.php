<?php

namespace EnterSite\Model {
    use EnterSite\Model\Page\Meta;

    class Page {
        /** @var string */
        public $name;
        /** @var string */
        public $path;
        /** @var string */
        public $title;
        /** @var string */
        public $header;
        /** @var Meta[] */
        public $meta = [];
    }
}

namespace EnterSite\Model\Page {
    class Meta {
        /** @var string */
        public $charset;
        /** @var string */
        public $content;
        /** @var string */
        public $httpEquiv;
        /** @var string */
        public $name;
    }
}