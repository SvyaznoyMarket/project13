<?php

namespace EnterSite\Model;

use EnterSite\Model\ImportArrayConstructorTrait;
use EnterSite\Model;

class Cart {
    use ImportArrayConstructorTrait;

    /** @var Model\Cart\Product[] */
    public $product;
    /** @var int */
    public $sum = 0;

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (isset($data['product'][0])) {
            foreach ($data['product'] as $productData) {
                if (empty($productData['id'])) continue;

                $this->product[] = new Model\Cart\Product($productData);
            }
        }
    }
}