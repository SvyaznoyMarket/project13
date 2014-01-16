<?php

namespace EnterSite\Model;

use EnterSite\Model\ImportConstructorTrait;
use EnterSite\Model\ImportInterface;

class Product implements ImportInterface {
    use ImportConstructorTrait;

    /** @var string */
    public $id;
    /** @var string */
    public $name;
    /** @var string */
    public $token;

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
        if (array_key_exists('name', $data)) $this->name = (string)$data['name'];
        if (array_key_exists('token', $data)) $this->token = (string)$data['token'];
    }
}