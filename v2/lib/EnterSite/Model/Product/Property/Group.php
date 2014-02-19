<?php

namespace EnterSite\Model\Product\Property;

use EnterSite\Model\ImportArrayConstructorTrait;
use EnterSite\Model;

class Group {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $id;
    /** @var string */
    public $name;

    /**
     * @param array $data
     */
    public function import(array $data = []) {
        if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
        if (array_key_exists('name', $data)) $this->name = (string)$data['name'];
    }
}