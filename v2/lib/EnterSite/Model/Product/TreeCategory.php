<?php

namespace EnterSite\Model\Product;

use EnterSite\Model\ImportArrayConstructorTrait;
use EnterSite\Model\Product\Category;

class TreeCategory extends Category {

    /** @var TreeCategory[] */
    public $children = [];
    /** @var int */
    public $productCount;
    /** @var int */
    public $productGlobalCount;
    /** @var array TreeCategory[] */
    public $ascendants = [];

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
                $this->children[] = new TreeCategory($childItem);
            }
        }
    }
}