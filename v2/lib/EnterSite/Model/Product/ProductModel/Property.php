<?php

namespace EnterSite\Model\Product\ProductModel;

use EnterSite\Model\ImportArrayConstructorTrait;
use EnterSite\Model;

class Property {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $id;
    /** @var string */
    public $name;
    /** @var string */
    public $unit;
    /** @var bool */
    public $isImage;
    /** @var \EnterSite\Model\Product\ProductModel\Property\Option[] */
    public $options = [];

    /**
     * @param array $data
     */
    public function import(array $data = []) {
        if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
        if (array_key_exists('name', $data)) $this->name = (string)$data['name'];
        if (array_key_exists('unit', $data)) $this->unit = $data['unit'] ? (string)$data['unit'] : null;
        if (array_key_exists('is_image', $data)) $this->isImage = (bool)$data['is_image'];
        if (isset($data['option'][0])) {
            foreach ($data['option'] as $optionItem) {
                $this->options[] = new Property\Option($optionItem);
            }
        }
    }
}