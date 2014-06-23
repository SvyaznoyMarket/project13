<?php

namespace EnterTerminal\Model {
    use EnterSite\Model\ImportArrayConstructorTrait;
    use EnterTerminal\Model;

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

namespace EnterTerminal\Model\MainMenu {
    use EnterSite\Model\ImportArrayConstructorTrait;
    use EnterTerminal\Model;

    class Element {
        use ImportArrayConstructorTrait;

        /** @var string */
        public $type;
        /** @var string */
        public $id;
        /** @var string */
        public $name;
        /** @var string */
        public $char;
        /** @var string */
        public $image;
        /** @var string */
        public $url;
        /** @var int */
        public $level = 1;
        /** @var string */
        public $style;
        /** @var string */
        public $styleHover;
        /** @var string */
        public $class;
        /** @var string */
        public $classHover;
        /** @var Model\MainMenu\Element[] */
        public $children = [];

        /**
         * @param array $data
         */
        public function import(array $data) {
            if (array_key_exists('type', $data)) $this->type = (string)$data['type'];
            if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
            if (array_key_exists('name', $data)) $this->name = (string)$data['name'];
            if (array_key_exists('char', $data)) $this->char = (string)$data['char'];
            if (array_key_exists('image', $data)) $this->image = (string)$data['image'];
            if (array_key_exists('link', $data)) $this->url = (string)$data['link'];
            if (array_key_exists('style', $data)) $this->style = (string)$data['style'];
            if (array_key_exists('styleHover', $data)) $this->styleHover = (string)$data['styleHover'];
            if (array_key_exists('class', $data)) $this->class = (string)$data['class'];
            if (array_key_exists('classHover', $data)) $this->classHover = (string)$data['classHover'];
        }
    }
}
