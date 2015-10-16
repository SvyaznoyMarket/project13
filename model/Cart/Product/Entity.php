<?php

namespace Model\Cart\Product {
    class Entity {
        /** @var int|null */
        public $id;
        /** @var string|null */
        public $ui;
        /** @var string|null */
        public $article;
        /** @var string|null */
        public $barcode;
        /** @var string|null */
        public $name;
        /** @var string|null */
        public $brandName;
        /** @var string|null */
        public $image;
        /** @var string|null */
        public $url;
        /** @var bool */
        public $isSlot = false;
        /** @var bool */
        public $isOnlyFromPartner = false;
        /** @var \Model\Cart\Product\Category|null */
        public $rootCategory;
        /** @var \Model\Cart\Product\Category|null */
        public $category;
        /** @var string|null */
        public $categoryPath;
        /** @var float|null */
        public $price;
        /** @var mixed */
        public $sender;
        /** @var mixed */
        public $sender2;
        /** @var mixed */
        public $credit;
        /** @var mixed */
        public $referer;
        /** @var int|null */
        public $quantity;
        /**
         * Содержит true, если при последнем обновлении корзины бэкэнд ответил успешно, но не вернул товар
         * @var bool
         */
        public $isGone = false;
        /** @var bool */
        public $isAvailable = true;
        /** @var mixed */
        public $added = null;

        public function __construct($data = []) {
            if (isset($data['id'])) $this->id = (int)$data['id'];
            if (isset($data['ui'])) $this->ui = (string)$data['ui'];
            if (isset($data['article'])) $this->article = (string)$data['article'];
            if (isset($data['barcode'])) $this->barcode = $data['barcode'];
            if (isset($data['name'])) $this->name = (string)$data['name'];
            if (isset($data['brandName'])) $this->brandName = $data['brandName'];
            if (isset($data['image'])) $this->image = (string)$data['image'];
            if (isset($data['url'])) $this->url = (string)$data['url'];
            if (isset($data['isSlot'])) $this->isSlot = (bool)$data['isSlot'];
            if (isset($data['isOnlyFromPartner'])) $this->isOnlyFromPartner = (bool)$data['isOnlyFromPartner'];
            if (isset($data['rootCategory'])) $this->rootCategory = new \Model\Cart\Product\Category($data['rootCategory']);
            if (isset($data['category'])) $this->category = new \Model\Cart\Product\Category($data['category']);
            if (isset($data['categoryPath'])) $this->categoryPath = $data['categoryPath'];
            if (isset($data['price'])) $this->price = (float)$data['price'];
            if (isset($data['sender'])) $this->sender = $data['sender'];
            if (isset($data['sender2'])) $this->sender2 = $data['sender2'];
            if (isset($data['credit'])) $this->credit = $data['credit'];
            if (isset($data['referer'])) $this->referer = $data['referer'];
            if (isset($data['quantity'])) $this->quantity = (int)$data['quantity'];
            if (isset($data['isGone'])) $this->isGone = (bool)$data['isGone'];
            if (isset($data['isAvailable'])) $this->isAvailable = (bool)$data['isAvailable'];
            if (isset($data['added'])) $this->added = $data['added'];

            // TODO это ещё актуально? Может в сессии старых пользователей может содержаться error?
            if (array_key_exists('error', $data)){
                // TODO: подумать - а надо ли это
                $this->quantity = 0;
            }
        }
    }
}

namespace Model\Cart\Product {
    class Category {
        /** @var string */
        public $id;
        /** @var string */
        public $name;

        public function __construct($data = []) {
            if (isset($data['id'])) $this->id = (string)$data['id'];
            if (isset($data['name'])) $this->name = (string)$data['name'];
        }
    }
}
