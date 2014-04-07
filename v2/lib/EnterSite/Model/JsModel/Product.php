<?php

namespace EnterSite\Model\JsModel {
    use EnterSite\Model\ImportArrayConstructorTrait;

    class Product {
        use ImportArrayConstructorTrait;

        /** @var string */
        public $id;
        /** @var string */
        public $name;
        /** @var string */
        public $token;
        /** @var bool */
        public $inCart;
        /** @var Product\Cart */
        public $cart;
        /** @var Product\BuyButton|null */
        public $buyButton;
        /** @var Product\BuySpinner|null */
        public $buySpinner;

        /**
         * @param array $data
         */
        public function import(array $data) {
            if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
            if (array_key_exists('name', $data)) $this->name = (string)$data['name'];
            if (array_key_exists('token', $data)) $this->token = (string)$data['token'];
            if (array_key_exists('inCart', $data)) $this->inCart = (bool)$data['inCart'];
            if (isset($data['cart']) && is_array($data['cart'])) $this->cart = new Product\Cart($data['cart']);
            if (isset($data['buyButton']['selector'])) $this->buyButton = new Product\BuyButton($data['buyButton']);
            if (isset($data['buySpinner']['selector'])) $this->buySpinner = new Product\BuySpinner($data['buySpinner']);
        }
    }
}

namespace EnterSite\Model\JsModel\Product {
    use EnterSite\Model\ImportArrayConstructorTrait;
    use EnterSite\Model\Partial;

    class Cart {
        use ImportArrayConstructorTrait;

        /** @var string */
        public $setUrl;
        /** @var string */
        public $deleteUrl;
        /** @var int */
        public $quantity;

        /**
         * @param array $data
         */
        public function import(array $data) {
            if (array_key_exists('setUrl', $data)) $this->setUrl = (string)$data['setUrl'];
            if (array_key_exists('deleteUrl', $data)) $this->deleteUrl = (string)$data['deleteUrl'];
            if (array_key_exists('quantity', $data)) $this->quantity = (int)$data['quantity'];
        }
    }

    class BuyButton {
        use ImportArrayConstructorTrait;

        /** @var string */
        public $selector;
        /** @var Partial\Cart\ProductButton */
        public $templateData;

        /**
         * @param array $data
         */
        public function import(array $data) {
            if (array_key_exists('selector', $data)) $this->selector = (string)$data['selector'];
            if (isset($data['templateData']) && is_array($data['templateData'])) $this->templateData = new Partial\Cart\ProductButton($data['templateData']);
        }
    }

    class BuySpinner {
        use ImportArrayConstructorTrait;

        /** @var string */
        public $selector;
        /** @var Partial\Cart\ProductSpinner */
        public $templateData;

        /**
         * @param array $data
         */
        public function import(array $data) {
            if (array_key_exists('selector', $data)) $this->selector = (string)$data['selector'];
            if (isset($data['templateData']) && is_array($data['templateData'])) $this->templateData = new Partial\Cart\ProductSpinner($data['templateData']);
        }
    }
}

