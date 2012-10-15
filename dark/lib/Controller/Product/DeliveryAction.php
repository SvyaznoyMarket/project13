<?php

namespace Controller\Product;

class DeliveryAction {
    public function execute($productId, \Http\Request $request) {
        return new \Http\JsonResponse(array());
    }
}