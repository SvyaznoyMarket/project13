<?php

namespace EnterSite\Model\Page {
    use EnterSite\Model\HtmlPage;

    class DefaultLayout extends HtmlPage {
        /** @var DefaultLayout\JsModel */
        public $jsModel;
        /** @var DefaultLayout\TemplateBlock */
        public $templateBlock;
        /** @var DefaultLayout\BreadcrumbBlock|null */
        public $breadcrumbBlock;
        /** @var DefaultLayout\MainMenu */
        public $mainMenu;
        /** @var DefaultLayout\Search */
        public $search;
        /** @var DefaultLayout\Content */
        public $content;

        public function __construct() {
            parent::__construct();

            $this->jsModel = new DefaultLayout\JsModel();
            $this->templateBlock = new DefaultLayout\TemplateBlock();
            $this->mainMenu = new DefaultLayout\MainMenu();
            $this->search = new DefaultLayout\Search();
            $this->content = new DefaultLayout\Content();
        }
    }
}

namespace EnterSite\Model\Page\DefaultLayout {
    class JsModel {
    }

    /**
     * Шаблоны mustache для блоков <script id="templateId" type="text/html" />
     */
    class TemplateBlock {
        /** @var string */
        public $cartBuyButton;
    }

    class BreadcrumbBlock {
        /** @var BreadcrumbBlock\Breadcrumb[] */
        public $breadcrumbs = [];
    }

    class MainMenu {
    }

    class Search {
        /** @var string */
        public $inputPlaceholder;
        /** @var Search\Hint[] */
        public $hints = [];
    }

    class Content {
        /** @var string */
        public $title;

        public function __construct() {}
    }
}

namespace EnterSite\Model\Page\DefaultLayout\BreadcrumbBlock {
    class Breadcrumb {
        /** @var string */
        public $name;
        /** @var string */
        public $url;
    }
}

namespace EnterSite\Model\Page\DefaultLayout\Search {
    class Hint {
        /** @var string */
        public $name;
        /** @var string */
        public $url;
    }
}