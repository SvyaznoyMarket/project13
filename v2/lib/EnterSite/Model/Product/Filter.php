<?php

namespace EnterSite\Model\Product;

use EnterSite\Model\ImportArrayConstructorTrait;
use EnterSite\Model;

class Filter {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $id;
    /** @var string */
    public $name;
    /** @var Model\Product\Filter\Option[] */
    public $option = [];

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
        if (array_key_exists('name', $data)) $this->name = (string)$data['name'];
        if (isset($data['options'][0])) {
            foreach ($data['options'] as $optionItem) {
                $this->option[] = new Model\Product\Filter\Option($optionItem);
            }
        }
    }
}