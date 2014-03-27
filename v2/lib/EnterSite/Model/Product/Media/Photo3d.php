<?php

namespace EnterSite\Model\Product\Media;

use EnterSite\Model\ImportArrayConstructorTrait;

class Photo3d {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $productId;
    /** @var string */
    public $source;

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('source', $data)) $this->source = (string)$data['source'];
        if (array_key_exists('product_id', $data)) $this->productId = (string)$data['product_id'];
    }
}