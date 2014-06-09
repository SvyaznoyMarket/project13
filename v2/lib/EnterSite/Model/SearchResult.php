<?php

namespace EnterSite\Model;

use EnterSite\Model\ImportArrayConstructorTrait;

class SearchResult {
    use ImportArrayConstructorTrait;

    /** @var array */
    public $productIds = [];
    /** @var int */
    public $productCount;
    /** @var bool */
    public $isForcedMean;
    /** @var string */
    public $forcedMean;

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('forced_mean', $data)) $this->isForcedMean = (bool)$data['forced_mean'];
        if (array_key_exists('did_you_mean', $data)) $this->forcedMean = $data['did_you_mean'] ? (string)$data['did_you_mean'] : null;

        $productData = isset($data['1']) ? (array)$data['1'] : [];

        if (array_key_exists('data', $productData)) $this->productIds = (array)$productData['data'];
        if (array_key_exists('count', $productData)) $this->productCount = (int)$productData['count'];
    }
}