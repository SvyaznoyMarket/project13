<?php

namespace Model\Product\Model\Property\Option;

class Entity {

    /** @var string */
    private $value;
    /** @var Product\Entity[] */
    private $product;

    public function __construct(array $data = []) {
        if (array_key_exists('value', $data)) $this->setValue($data['value']);
        if (array_key_exists('product', $data) && (bool)$data['product']) $this->setProduct(new Product\Entity($data['product']));
    }

    /**
     * @param Product\Entity $product
     */
    public function setProduct($product) {
        $this->product = $product;
    }

    /**
     * @return Product\Entity
     */
    public function getProduct() {
        return $this->product;
    }

    /**
     * @param string $value
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    public function getHumanizedName() {
        if (in_array($this->value, array('false', false), true)) {
            return 'нет';
        }
        if (in_array($this->value, array('true', true), true)) {
            return 'да';
        }

        return $this->value;
    }
}
