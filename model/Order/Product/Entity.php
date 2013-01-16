<?php

namespace Model\Order\Product;

class Entity {
    /** @var int */
    private $id;
    /** @var int */
    private $price = 0;
    /** @var int */
    private $quantity = 0;
    /** @var int */
    private $warrantyId;
    /** @var int */
    private $warrantyPrice;
    /** @var int */
    private $warrantyQuantity;

    /**
     * @param array $data
     */
    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('price', $data)) $this->setPrice($data['price']);
        if (array_key_exists('quantity', $data)) $this->setQuantity($data['quantity']);
        if (array_key_exists('warranty_id', $data)) $this->setWarrantyId($data['warranty_id']);
        if (array_key_exists('warranty_price', $data)) $this->setWarrantyPrice($data['warranty_price']);
        if (array_key_exists('warranty_quantity', $data)) $this->setWarrantyQuantity($data['warranty_quantity']);
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
     * @param int $warrantyId
     */
    public function setWarrantyId($warrantyId) {
        $this->warrantyId = (int)$warrantyId;
    }

    /**
     * @return int
     */
    public function getWarrantyId() {
        return $this->warrantyId;
    }

    /**
     * @param int $warrantyPrice
     */
    public function setWarrantyPrice($warrantyPrice) {
        $this->warrantyPrice = (int)$warrantyPrice;
    }

    /**
     * @return int
     */
    public function getWarrantyPrice() {
        return $this->warrantyPrice;
    }

    /**
     * @param int $warrantyQuantity
     */
    public function setWarrantyQuantity($warrantyQuantity) {
        $this->warrantyQuantity = (int)$warrantyQuantity;
    }

    /**
     * @return int
     */
    public function getWarrantyQuantity() {
        return $this->warrantyQuantity;
    }
}