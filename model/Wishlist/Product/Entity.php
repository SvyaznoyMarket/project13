<?php

namespace Model\Wishlist\Product;

class Entity {
    /** @var string */
    public $ui;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('uid', $data)) $this->ui = (string)$data['uid'];
    }
}