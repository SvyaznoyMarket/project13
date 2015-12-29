<?php

namespace Model\Product\Stock;

class Entity {
    /** @var int */
    private $shopId;
    /** @var int */
    private $quantity;
    /** @var int */
    private $quantityShowroom;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('shop_id', $data))             $this->setShopId($data['shop_id']);
        if (array_key_exists('quantity', $data))            $this->setQuantity($data['quantity']);
        if (array_key_exists('quantity_showroom', $data))   $this->setQuantityShowroom($data['quantity_showroom']);
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * @param int $quantityShowroom
     */
    public function setQuantityShowroom($quantityShowroom) {
        $this->quantityShowroom = $quantityShowroom;
    }

    /**
     * @return int
     */
    public function getQuantityShowroom() {
        return $this->quantityShowroom;
    }

    /**
     * @param int $shopId
     */
    public function setShopId($shopId) {
        $this->shopId = $shopId;
    }

    /**
     * @return int
     */
    public function getShopId() {
        return $this->shopId;
    }
}
