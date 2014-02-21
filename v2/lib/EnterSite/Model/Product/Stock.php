<?php

namespace EnterSite\Model\Product;

use EnterSite\Model\ImportArrayConstructorTrait;

class Stock {
    use ImportArrayConstructorTrait;

    /** @var string|null */
    public $storeId;
    /** @var string|null */
    public $shopId;
    /** @var int */
    public $quantity = 0;
    /** @var int */
    public $showroomQuantity = 0;

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('store_id', $data)) $this->storeId = $data['store_id'] ? (string)$data['store_id'] : null;
        if (array_key_exists('shop_id', $data)) $this->shopId = $data['shop_id'] ? (string)$data['shop_id'] : null;
        if (array_key_exists('quantity_showroom', $data)) $this->showroomQuantity = (int)$data['quantity_showroom'];
        if (array_key_exists('quantity', $data)) $this->quantity = (int)$data['quantity'];
    }
}