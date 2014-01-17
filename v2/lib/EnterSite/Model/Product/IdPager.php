<?php

namespace EnterSite\Model\Product;

use EnterSite\Model\ImportArrayConstructorTrait;

class IdPager {
    use \EnterSite\Model\ImportArrayConstructorTrait;

    /** @var array */
    public $id;
    /** @var int */
    public $count;

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('list', $data) && is_array($data['list'])) $this->id = $data['list'];
        if (array_key_exists('count', $data)) $this->count = (int)$data['count'];
    }
}