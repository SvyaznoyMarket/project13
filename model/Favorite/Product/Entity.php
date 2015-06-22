<?php

namespace Model\Favorite\Product;

class Entity {
    /** @var string */
    public $ui;
    /** @var bool */
    public $isFavourite;

    public function __construct($data) {
        if (isset($data['uid'])) $this->ui = $data['uid'];
        if (isset($data['is_favorite'])) $this->isFavourite = $data['is_favorite'];
    }
}