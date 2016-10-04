<?php

namespace Model\Product;

use Model\Product\Model\Property;
use Model\Product\Entity as Product;

class Model {
    /** @var string */
    public $ui = '';
    /** @var Property\Entity|null */
    public $property;
    /** @var Product[]  */
    public $items = [];

    public function __construct($data = []) {
        if (isset($data['uid'])) $this->ui = (string)$data['uid'];
        if (!empty($data['property']) && !empty($data['items'])) $this->property = new Property\Entity($data);
        if (array_key_exists('items', $data) && is_array($data['items'])) {
            $this->items = array_map(function ($item) {
                $product = new Product($item['product']);
                $product->importFromScms($item['product']);
                return $product;
            }, $data['items']);
        }
    }

    /**
     * @return Product|null
     */
    public function getMainProduct()
    {
        return isset($this->items[0]) ? $this->items[0] : null;
    }
}