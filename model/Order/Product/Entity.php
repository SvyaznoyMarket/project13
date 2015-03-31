<?php

namespace Model\Order\Product;

class Entity {
    /** @var int */
    private $id;
    /** @var int */
    private $price = 0;
    /** @var int */
    private $sum = 0;
    /** @var int */
    private $quantity = 0;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('price', $data)) $this->setPrice($data['price']);
        if (array_key_exists('quantity', $data)) $this->setQuantity($data['quantity']);

        if (array_key_exists('sum', $data)) { // TODO: осторожно, костыль для ядра
            $this->setSum($data['sum']);
        } else {
            $this->setSum($this->getPrice() * $this->getQuantity());
        }
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
     * @param int $sum
     */
    public function setSum($sum) {
        $this->sum = (string)$sum;
    }

    /**
     * @return int
     */
    public function getSum() {
        return $this->sum;
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

}