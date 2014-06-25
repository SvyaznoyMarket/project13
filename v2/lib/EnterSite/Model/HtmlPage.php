<?php

namespace EnterSite\Model {
    class HtmlPage extends Page {
        /** @var string */
        public $title;
        /** @var string */
        public $header;
        /** @var HtmlPage\Meta[] */
        public $metas = [];
        /** @var string[] */
        public $styles = [];

        public function __construct() {

        }
    }
}

namespace EnterSite\Model\HtmlPage {
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