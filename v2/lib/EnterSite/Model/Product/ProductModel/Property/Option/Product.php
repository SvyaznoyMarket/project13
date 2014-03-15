<?php

namespace EnterSite\Model\Product\ProductModel\Property\Option;

use EnterSite\Model\ImportArrayConstructorTrait;

class Product {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $id;
    /** @var string */
    public $name;
    /** @var string */
    public $link;
    /** @var string */
    public $token;
    /** @var string */
    public $image;

    /**
     * @param array $data
     */
    public function import(array $data = []) {
        if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
        if (array_key_exists('name', $data)) $this->name = (string)$data['name'];
        if (array_key_exists('link', $data)) $this->link = rtrim((string)$data['link'], '/');
        if (array_key_exists('token', $data)) $this->token = (string)$data['token'];
        if (array_key_exists('media_image', $data)) $this->image = (string)$data['media_image'];
    }
}