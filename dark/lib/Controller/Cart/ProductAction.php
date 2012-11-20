<?php

namespace Controller\Cart;

class ProductAction {
    public function add($productId, $quantity = 1) {
        \App::logger()->debug('Exec ' . __METHOD__);
    }
}