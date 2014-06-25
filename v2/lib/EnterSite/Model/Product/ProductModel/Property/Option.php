<?php

namespace EnterSite\Model\Product\ProductModel\Property;

use EnterSite\Model\ImportArrayConstructorTrait;
use EnterSite\Model;

class Option {
    use ImportArrayConstructorTrait;

    /** @var mixed */
    public $value;
    /** @var \EnterSite\Model\Product\ProductModel\Property\Option\Product|null */
    public $product;
    /** @var string */
    public $shownValue;

    /**
     * @param array $data
     */
    public function import(array $data = []) {
        if (array_key_exists('value', $data)) $this->value = $data['value'];
        if (isset($data['product']['id'])) $this->product = new Option\Product($data['product']);

        if (in_array($this->value, ['false', false], true)) {
            $this->shownValue = 'нет';
        } else if (in_array($this->value, ['true', true], true)) {
            $this->shownValue = 'да';
        } else {
            $this->shownValue = (string)$this->value;
        }
    }
}