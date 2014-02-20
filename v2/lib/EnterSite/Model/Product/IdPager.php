<?php

namespace EnterSite\Model\Product;

use EnterSite\Model\ImportArrayConstructorTrait;

class IdPager {
    use ImportArrayConstructorTrait;

    /** @var array */
    public $ids;
    /** @var int */
    public $count;

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('list', $data) && is_array($data['list'])) $this->ids = $data['list'];
        if (array_key_exists('count', $data)) $this->count = (int)$data['count'];
    }
}