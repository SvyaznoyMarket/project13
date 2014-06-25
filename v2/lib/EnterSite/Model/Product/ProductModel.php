<?php

namespace EnterSite\Model\Product;

use EnterSite\Model\ImportArrayConstructorTrait;
use EnterSite\Model;

class ProductModel {
    use ImportArrayConstructorTrait;

    /** @var Model\Product\ProductModel\Property[] */
    public $properties = [];

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (isset($data['property'][0])) {
            foreach ($data['property'] as $propertyItem) {
                $this->properties[] = new Model\Product\ProductModel\Property($propertyItem);
            }
        }
    }
}