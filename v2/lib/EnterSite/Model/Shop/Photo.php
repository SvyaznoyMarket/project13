<?php

namespace EnterSite\Model\Shop;

use EnterSite\Model\ImportArrayConstructorTrait;

class Photo {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $source;

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('source', $data)) $this->source = (string)$data['source'];
    }
}