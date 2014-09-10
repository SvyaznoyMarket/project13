<?php

namespace Session\Cart;

class OneClick {
    /** @var string */
    private $sessionName = 'user/cart/one-click';
    /** @var \Http\Session */
    private $storage;
    /** @var \Model\Cart\Product\Entity[]|null */
    private $productsById = [];
    /** @var int */
    private $sum = 0;
    /** @var \Model\Region\Entity */
    private $region;

    public function __construct() {
        $this->region = \App::user()->getRegion();

        $this->storage = \App::session();

        $this->storage->set($this->sessionName, array_merge([
            'product' => [],
            'sum'     => 0,
        ], (array)$this->storage->get($this->sessionName)));

        $this->calculate();
    }

    /**
     * @param \Model\Cart\Product\Entity $product
     */
    public function setProduct(\Model\Cart\Product\Entity $product) {
        $this->clear(); // возможно добавление только одного товара

        $sessionData = $this->storage->get($this->sessionName);

        if ($product->getQuantity() < 1) {
            if (isset($sessionData['product'][$product->getId()])) unset($sessionData['product'][$product->getId()]);
        } else {
            $sessionData['product'][$product->getId()] = ['quantity' => $product->getQuantity()];
        }

        $this->storage->set($this->sessionName, $sessionData);

        $this->calculate();
    }


    /**
     * @return array|\Model\Cart\Product\Entity[]
     */
    public function getProducts() {
        return $this->productsById;
    }

    /**
     * @param $productId
     * @return \Model\Cart\Product\Entity|null
     */
    public function getProductById($productId) {
        return isset($this->productsById[$productId]) ? $this->productsById[$productId] : null;
    }

    /**
     * @return int
     */
    public function getSum() {
        return $this->sum;
    }

    public function clear() {
        $this->storage->set($this->sessionName, null);
        $this->productsById = [];
        $this->sum = 0;
    }

    private function calculate() {
        $sessionData = $this->storage->get($this->sessionName);

        if (!(bool)$sessionData['product']) return;

        /** @var $productsById \Model\Product\Entity[] */
        $productsById = [];
        foreach (\RepositoryManager::product()->getCollectionById(array_keys($sessionData['product']), $this->region) as $product) {
            $productsById[$product->getId()] = $product;
        }

        foreach ($sessionData['product'] as $productId => $productItem) {
            if (!isset($productsById[$productId])) {
                \App::logger()->error(sprintf('Товар #%s не получен', $productId));
                continue;
            }

            if (!isset($this->productsById[$productId])) {
                $this->productsById[$productId] = new \Model\Cart\Product\Entity($productItem);
            }

            $cartProduct = $this->productsById[$productId];

            $cartProduct->setId($productId);
            $cartProduct->setPrice($productsById[$productId]->getPrice());
            $cartProduct->setQuantity($productItem['quantity']);
            $cartProduct->setSum($cartProduct->getPrice() * $cartProduct->getQuantity());

            $this->sum += $cartProduct->getSum();
        }
    }

    public function count() {
        return count($this->getProducts());
    }

    public function getProductData() {
        if ($this->getProducts() === null) return [];
        return array_map(function(\Model\Cart\Product\Entity $product){
            return ['id'=> $product->getId(), 'quantity'=>$product->getQuantity()];
        }, $this->getProducts());
    }

    public function setShop($shop) {
        $sessionData = $this->storage->get($this->sessionName);
        $sessionData['shop'] = (int)$shop;
        $this->storage->set($this->sessionName, $sessionData);
    }

    public function getShop() {
        $cart = $this->storage->get($this->sessionName);
        return isset($cart['shop']) ? (int)$cart['shop'] : null;
    }

}