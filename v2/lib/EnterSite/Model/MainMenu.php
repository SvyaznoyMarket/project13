<?php

namespace EnterSite\Model {
    use EnterSite\Model\ImportArrayConstructorTrait;
    use EnterSite\Model;

    class MainMenu {
        use ImportArrayConstructorTrait;

        /** @var Model\MainMenu\Element[] */
        public $elements = [];

        /**
         * @param array $data
         */
        public function import(array $data) {
            if (isset($data['items'][0])) {
                foreach ($data['items'] as $elementItem) {
                    $this->elements[] = new Model\MainMenu\Element($elementItem);
                }
            }
        }
    }
}

namespace EnterSite\Model\MainMenu {
    use EnterSite\Model\ImportArrayConstructorTrait;
    use EnterSite\Model;

    class Element {
        use ImportArrayConstructorTrait;

        /** @var string */
        public $name;
        /** @var string */
        public $url;
        /** @var string */
        public $css;
        /** @var string */
        public $cssHover;
        /** @var Model\MainMenu\Element[] */
        public $children = [];

        /**
         * @param array $data
         */
        public function import(array $data) {
            if (array_key_exists('name', $data)) $this->name = (string)$data['name'];
            if (array_key_exists('link', $data)) $this->url = (string)$data['link'];
            if (array_key_exists('css', $data)) $this->css = (string)$data['css'];
            if (array_key_exists('cssHover', $data)) $this->cssHover = (string)$data['cssHover'];
        }
    }
}
