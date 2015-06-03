<?php

namespace Model\Favorite\Product;

class Entity {
    /** @var string */
    public $ui;

    public function __construct($data) {
        if (isset($data['uid'])) $this->ui = (string)$data['uid'];
    }
}