<?php

namespace EnterSite\Model\Partial\Cart;

use EnterSite\Model\ImportArrayConstructorTrait;

class ProductButton {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $id;
    /** @var string */
    public $text;
    /** @var string */
    public $url;
    /** @var string */
    public $class;

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
        if (array_key_exists('text', $data)) $this->text = (string)$data['text'];
        if (array_key_exists('url', $data)) $this->url = (string)$data['url'];
        if (array_key_exists('class', $data)) $this->class = (string)$data['class'];
    }
}