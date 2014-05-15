<?php

namespace EnterSite\Model;

use EnterSite\Model\ImportArrayConstructorTrait;
use EnterSite\Model;

class Cart implements \Countable {
    use ImportArrayConstructorTrait;

    /** @var Model\Cart\Product[] */
    public $product = [];
    /** @var float */
    public $sum;

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (isset($data['product_list'][0])) {
            foreach ($data['product_list'] as $productData) {
                if (empty($productData['id'])) continue;

                $this->product[] = new Model\Cart\Product($productData);
            }
        }
        if (array_key_exists('sum', $data)) $this->sum = (float)$data['sum'];
    }

    /**
     * @return int
     */
    public function count() {
        return count($this->product);
    }
}