<?php

namespace Session;

class Cart {
    public function hasProduct($productId) {
        return false;
    }

    /**
     * @param int $productId
     * @return array
     */
    public function getServicesByProduct($productId) {
        return array();
    }

    /**
     * @param int $productId
     * @return int
     */
    public function getQuantityByProduct($productId) {
        return 0;
    }

}