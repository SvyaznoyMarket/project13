<?php

namespace EnterSite\Model {
    class Page {
        /** @var string */
        public $name;
        /** @var string */
        public $path;
        /** @var string */
        public $title;
        /** @var string */
        public $header;
        /** @var Page\Meta[] */
        public $metas = [];
        /** @var string[] */
        public $styles = [];

        public function __construct() {

        }
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