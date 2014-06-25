<?php

namespace EnterSite\Model {
    use EnterSite\Model\ImportArrayConstructorTrait;

    class Promo {
        use ImportArrayConstructorTrait;

        /** @var int */
        public $id;
        /** @var int */
        public $typeId;
        /** @var string */
        public $name;
        /** @var string */
        public $url;
        /** @var string */
        public $image;
        /** @var Promo\Item[] */
        public $items = [];

        /**
         * @param array $data
         */
        public function import(array $data = []) {
            if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
            if (array_key_exists('type_id', $data)) $this->typeId = (int)$data['type_id'];
            if (array_key_exists('name', $data)) $this->name = (string)$data['name'];
            if (array_key_exists('media_image', $data)) $this->image = (string)$data['media_image'];
            if (array_key_exists('url', $data)) $this->url = (string)$data['url'];
            if (isset($data['item_list'][0])) {
                foreach ($data['item_list'] as $item) {
                    $this->items[] = new Promo\Item($item);
                }
            }
        }
    }
}



namespace EnterSite\Model\Promo {
    use EnterSite\Model\ImportArrayConstructorTrait;

    class Item {
        use ImportArrayConstructorTrait;

        const TYPE_PRODUCT = 1;
        //const TYPE_SERVICE = 2;
        const TYPE_PRODUCT_CATEGORY = 3;

        /** @var int */
        public $typeId;
        /** @var string */
        public $productId;
        /** @var string */
        //public $serviceId;
        /** @var string */
        public $productCategoryId;

        /**
         * @param array $data
         */
        public function import(array $data = []) {
            if (array_key_exists('type_id', $data)) $this->typeId = (int)$data['type_id'];
            if (array_key_exists('id', $data)) {
                switch ($this->typeId) {
                    case self::TYPE_PRODUCT:
                        $this->productId = (string)$data['id'];
                        break;
                    /*
                    case self::TYPE_SERVICE:
                        $this->serviceId = (string)$data['id'];
                        break;
                    */
                    case self::TYPE_PRODUCT_CATEGORY:
                        $this->productCategoryId = (string)$data['id'];
                        break;
                }
            }
        }
    }
}

