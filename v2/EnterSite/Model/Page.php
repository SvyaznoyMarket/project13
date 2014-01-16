<?php

namespace EnterSite\Model {
    class Page {
        /**
         * @name Название
         * @var string
         */
        public $name;
        /**
         * @name Путь
         * @var string
         */
        public $path;
        /**
         * @name Заголовок
         * @var string
         */
        public $title;
        /**
         * @name Заголовок h1
         * @var string
         */
        public $header;
        /**
         * @var array Meta[]
         */
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