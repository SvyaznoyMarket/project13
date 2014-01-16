<?php

namespace EnterSite\Model;

use EnterSite\Model\ImportArrayConstructorTrait;

class Product {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $id;
    /** @var string */
    public $name;
    /** @var string */
    public $token;
    /** @var string */
    public $link;

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
        if (array_key_exists('name', $data)) $this->name = (string)$data['name'];
        if (array_key_exists('token', $data)) $this->token = (string)$data['token'];
        if (array_key_exists('link', $data)) $this->link = rtrim((string)$data['link'], '/');
    }
}