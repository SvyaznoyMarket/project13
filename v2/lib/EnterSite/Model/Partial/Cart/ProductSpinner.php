<?php

namespace EnterSite\Model\Partial\Cart;

use EnterSite\Model\ImportArrayConstructorTrait;

class ProductSpinner {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $id;

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
    }
}