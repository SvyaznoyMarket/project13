<?php

namespace Model\Wishlist\Product;

class Entity {
    /** @var string */
    public $ui;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_keys($data, 'ui')) $this->ui = (string)$data['ui'];
    }
}