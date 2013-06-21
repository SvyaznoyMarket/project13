<?php

namespace Model\Product\ShopState;

class Entity {
    /** @var \Model\Shop\Entity */
    private $shop;
    /** @var int */
    private $quantity;
    /** @var int */
    private $quantityInShowroom;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('shop', $data)) $this->setShop($data['shop']);
        if (array_key_exists('quantity', $data)) $this->setQuantity($data['quantity']);
        if (array_key_exists('quantity_showroom', $data)) $this->setQuantityInShowroom($data['quantity_showroom']);
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
     * @param int $quantityInShowroom
     */
    public function setQuantityInShowroom($quantityInShowroom) {
        $this->quantityInShowroom = (int)$quantityInShowroom;
    }

    /**
     * @return int
     */
    public function getQuantityInShowroom() {
        return $this->quantityInShowroom;
    }

    /**
     * @param \Model\Shop\Entity $shop
     */
    public function setShop(\Model\Shop\Entity $shop) {
        $this->shop = $shop;
    }

    /**
     * @return \Model\Shop\Entity
     */
    public function getShop() {
        return $this->shop;
    }
}
