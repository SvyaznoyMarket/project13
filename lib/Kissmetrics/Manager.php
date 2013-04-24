<?php

namespace Kissmetrics;

class Manager {
    /**
     * @param \Model\Product\BasicEntity $product
     * @param \Model\Product\Service\Entity $service
     * @param \Model\Product\Warranty\Entity $warranty
     * @return array
     */
    public static function getCartEvent(\Model\Product\BasicEntity $product = null, \Model\Product\Service\Entity $service = null, \Model\Product\Warranty\Entity $warranty = null) {
        $return = [];

        $cart = \App::user()->getCart();

        try {
            if ($product && ($cartProduct = $cart->getProductById($product->getId()))) {
                $categoryData = [];
                foreach ($product->getCategory() as $category) {
                    $categoryData[] = [
                        'id'   => $category->getId(),
                        'name' => $category->getName(),
                    ];
                }

                $return = [
                    'product' => [
                        'name'     => $product->getName(),
                        'category' => $categoryData,
                        'sum'      => $product->getPrice(),
                        'quantity' => $cartProduct->getQuantity(),
                    ],
                ];
            }
            if ($service && ($cartService = $cart->getServiceById($service->getId()))) {
                $result['service'] = [
                    'name'     => $service->getName(),
                    'sum'      => $service->getPrice(),
                    'quantity' => $cartService->getQuantity(),
                ];
                if (isset($result['product'])) {
                    $result['product']['serviceQuantity'] = $product ? $cart->getServicesQuantityByProduct($product->getId()) : 0;
                }
            }
            if ($warranty && ($cartWarranty = $cart->getWarrantyById($warranty->getId()))) {
                $result['warranty'] = [
                    'name'     => $warranty->getName(),
                    'sum'      => $warranty->getPrice(),
                    'quantity' => $cartWarranty->getQuantity(),
                ];
                if (isset($result['product'])) {
                    $result['product']['warrantyQuantity'] = $cartWarranty->getQuantity();
                }
            }
        } catch (\Exception $e) {
            \App::logger()->error($e, ['kissmetriks']);
        }

        return $return;
    }
}