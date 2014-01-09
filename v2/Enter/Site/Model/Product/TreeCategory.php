<?php

namespace Enter\Site\Model\Product;

use Enter\Site\Model\ImportConstructorTrait;

class TreeCategory extends Category {

    /** @var TreeCategory[] */
    public $child;
    /** @var int */
    public $productCount;
    /** @var int */
    public $productGlobalCount;

    /**
     * @param array $data
     */
    public function import(array $data) {
        parent::import($data);

        if (array_key_exists('product_count', $data)) $this->productCount = (int)$data['product_count'];
        if (array_key_exists('product_count_global', $data)) $this->productGlobalCount = (int)$data['product_count_global'];
        if (isset($data['children']) && is_array($data['children'])) {
            foreach ($data['children'] as $childItem) {
                if (!isset($childItem['id'])) continue;
                $this->child[] = new TreeCategory($childItem);
            }
        }
    }
}