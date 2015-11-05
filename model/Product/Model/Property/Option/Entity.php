<?php

namespace Model\Product\Model\Property\Option;

class Entity {
    /** @var string */
    public $value = '';
    /** @var \Model\Product\Entity|null */
    public $product;

    public function __construct($data = []) {
        if (isset($data['property_value'])) $this->value = (string)$data['property_value'];
        if (isset($data['product'])) {
            $this->product = new \Model\Product\Entity($data['product']);
            $this->product->importFromScms($data['product']);
        }
    }
}