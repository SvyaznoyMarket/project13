<?php

namespace Model\Order\Service;

class Entity {
    /** @var int */
    private $id;
    /** @var int */
    private $price = 0;
    /** @var int */
    private $quantity = 0;
    /** @var int */
    private $productId;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('price', $data)) $this->setPrice($data['price']);
        if (array_key_exists('quantity', $data)) $this->setQuantity($data['quantity']);
        if (array_key_exists('product_id', $data)) $this->setProductId($data['product_id']);
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = (int)$id;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $price
     */
    public function setPrice($price) {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity) {
        $this->quantity = (int)$quantity;
    }

    /**
     * @return int
     */
    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * @param int $productId
     */
    public function setProductId($productId) {
        $this->productId = $productId ? (int)$productId : null;
    }

    /**
     * @return int
     */
    public function getProductId() {
        return $this->productId;
    }
}