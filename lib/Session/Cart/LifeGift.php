<?php

namespace Session\Cart;

class LifeGift {
    /** @var string */
    private $sessionName = 'user/cart/life-gift';
    /** @var \Http\Session */
    private $storage;
    /** @var \Model\Cart\Product\Entity[]|null */
    private $productsById = [];
    /** @var int */
    private $sum = 0;

    public function __construct() {
        $this->storage = \App::session();

        $this->storage->set($this->sessionName, array_merge([
            'product' => [],
            'sum'     => [],
        ], (array)$this->storage->get($this->sessionName)));
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

        $productData = [];
        foreach ($sessionData['product'] as $productId => $productItem) {
            $productData[] = [
                'id'       => $productId,
                'quantity' => $productItem['quantity'],
            ];
        }

        if (!(bool)$this->productsById) return;

        $response = null;
        \App::coreClientV2()->addQuery(
            'cart/get-price',
            ['geo_id' => \App::user()->getRegion()->getId()],
            [
                'product_list'  => $productData,
                'service_list'  => [],
                'warranty_list' => [],
            ],
            function ($data) use (&$response) {
                $response = $data;
            }
        );
        \App::coreClientV2()->execute();

        $response = array_merge([
            'product_list' => [],
            'price_total'  => 0,
        ], (array)$response);

        $this->sum = (int)$response['price_total'];

        foreach ($response['product_list'] as $productItem) {
            $this->productsById[$productItem['id']] = new \Model\Cart\Product\Entity($productItem);
        }


        foreach ($response['product_list'] as $productItem) {
            $productId = $productItem['id'];

            if (array_key_exists($productId, $this->productsById)) {
                $this->productsById[$productId]->setQuantity($productItem['quantity']);
            } else {
                $this->productsById[$productId] = new \Model\Cart\Product\Entity($productItem);
            }
        }
    }
}