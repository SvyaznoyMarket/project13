<?php

namespace Model\Cart\Product;

class Entity {
    /** @var int */
    private $id;
    /** @var int */
    private $quantity;
    /** @var int */
    private $price;
    /** @var int  */
    private $sum;
    /** @var int  */
    private $deliverySum;
    /** @var bool */
    private $isBuyable = true;

    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('quantity', $data)) $this->setQuantity($data['quantity']);
        if (array_key_exists('price', $data)) $this->setPrice($data['price']);
        if (array_key_exists('sum', $data)) $this->setSum($data['sum']);
        if (array_key_exists('deliverySum', $data)) $this->setDeliverySum($data['deliverySum']);
        if (array_key_exists('error', $data)){
            // TODO: подумать - а надо ли это
            $this->setQuantity(0);

            $this->setIsBuyable(false);
        }
    }

    /**
     * @param $id
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
     * @param int $deliverySum
     */
    public function setDeliverySum($deliverySum) {
        $this->deliverySum = (int)$deliverySum;
    }

    /**
     * @return int
     */
    public function getDeliverySum() {
        return $this->deliverySum;
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
     * @param int $sum
     */
    public function setSum($sum) {
        if (false !== strpos($sum, '.00')) {
            $sum = (string)intval($sum);
        }

        $this->sum = (string)$sum;
    }

    /**
     * @return int
     */
    public function getSum() {
        return $this->sum;
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

}
