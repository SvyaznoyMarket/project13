<?php

namespace EnterSite\Model\Cart;

use EnterSite\Model\ImportArrayConstructorTrait;

class Product {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $id;
    /** @var int */
    public $quantity;
    /** @var int */
    public $price;
    /** @var float */
    public $sum;

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
        if (array_key_exists('quantity', $data)) $this->quantity = (int)$data['quantity'];
        if (array_key_exists('price', $data)) $this->price = (int)$data['price'];
        if (array_key_exists('sum', $data)) $this->sum = (float)$data['sum'];
    }
}