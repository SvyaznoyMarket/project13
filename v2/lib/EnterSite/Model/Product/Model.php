<?php

namespace EnterSite\Model\Product;

use EnterSite\Model\ImportArrayConstructorTrait;
use EnterSite\Model as ObjectModel;

class Model {
    use ImportArrayConstructorTrait;

    /** @var ObjectModel\Product\Model\Property[] */
    public $properties = [];

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (isset($data['property'][0])) {
            foreach ($data['property'] as $propertyItem) {
                $this->properties[] = new ObjectModel\Product\Model\Property($propertyItem);
            }
        }
    }
}