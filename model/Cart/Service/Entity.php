<?php

namespace Model\Cart\Service;

class Entity {
    /** @var int */
    private $id;
    /** @var int */
    private $quantity;
    /** @var int */
    private $price;
    /** @var int  */
    private $sum;
    /** @var bool */
    private $isBuyable = true;

    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('quantity', $data)) $this->setQuantity($data['quantity']);
        if (array_key_exists('price', $data)) $this->setPrice($data['price']);
        if (array_key_exists('sum', $data)) $this->setSum($data['sum']);
        if (array_key_exists('error', $data)){
            // TODO: подумать - а надо ли это
            $this->setQuantity(0);

            $this->setIsBuyable(false);
        }
    }

    public function setId($id) {
        $this->id = (int)$id;
    }

    public function getId() {
        return $this->id;
    }

    /**
     * @param int $price
     */
    public function setPrice($price) {
        $this->price = (int)$price;
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
     * @param bool $isBuyable
     */
    public function setIsBuyable($isBuyable) {
        $this->isBuyable = (bool)$isBuyable;
    }

    /**
     * @return bool
     */
    public function getIsBuyable() {
        return $this->isBuyable;
    }

    /**
     * @param int $sum
     */
    public function setSum($sum) {
        $this->sum = (int)$sum;
    }

    /**
     * @return int
     */
    public function getSum() {
        return $this->sum;
    }
}
