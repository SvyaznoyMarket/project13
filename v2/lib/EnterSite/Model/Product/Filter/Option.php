<?php

namespace EnterSite\Model\Product\Filter;

use EnterSite\Model\ImportArrayConstructorTrait;

class Option {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $id;
    /** @var string */
    public $token;
    /** @var string */
    public $name;
    /** @var int */
    public $quantity;
    /** @var int */
    public $globalQuantity;
    /** @var string|null */
    public $image;

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
        if (array_key_exists('token', $data)) $this->token = (string)$data['token'];
        if (array_key_exists('name', $data)) $this->name = (string)$data['name'];
        if (array_key_exists('quantity', $data)) $this->quantity = (int)$data['quantity'];
        if (array_key_exists('global', $data)) $this->globalQuantity = (int)$data['global'];
        if (array_key_exists('image', $data)) $this->image = (string)$data['image'];
    }
}