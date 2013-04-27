<?php

namespace Kissmetrics;

class Manager {
    /**
     * @param \Model\Product\Entity          $product
     * @param \Model\Product\Service\Entity  $service
     * @param \Model\Product\Warranty\Entity $warranty
     * @return array
     */
    public static function getCartEvent(\Model\Product\Entity $product = null, \Model\Product\Service\Entity $service = null, \Model\Product\Warranty\Entity $warranty = null) {
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
                        'name'            => $product->getName(),
                        'article'         => $product->getArticle(),
                        'category'        => $categoryData,
                        'price'           => $product->getPrice(),
                        'quantity'        => $cartProduct->getQuantity(),
                        'serviceQuantity' => $cart->getServicesQuantityByProduct($product->getId()),
                    ],
                ];
            }
            if ($service) {
                $cartService = null;
                if ($product && $cartProduct = $cart->getProductById($product->getId())) {
                    $cartService = $cartProduct->getServiceById($service->getId());
                } else {
                    $cartService = $cart->getServiceById($service->getId());
                }

                $return['service'] = [
                    'name'     => $service->getName(),
                    'price'    => $service->getPrice(),
                    'quantity' => $cartService ? $cartService->getQuantity() : 0,
                ];
                if (isset($return['product'])) {
                    $return['product']['serviceQuantity'] = $product ? $cart->getServicesQuantityByProduct($product->getId()) : 0;
                }
            }
            if ($warranty) {
                $cartWarranty = null;
                if ($product && $cartProduct = $cart->getProductById($product->getId())) {
                    $cartWarranty = $cartProduct->getWarrantyById($warranty->getId());
                } else {
                    $cartWarranty = $cart->getWarrantyById($warranty->getId());
                }

                $return['warranty'] = [
                    'name'     => $warranty->getName(),
                    'price'    => $warranty->getPrice(),
                    'quantity' => $cartWarranty ? $cartWarranty->getQuantity() : null,
                ];
                if (isset($return['product'])) {
                    $return['product']['warrantyQuantity'] = $cartWarranty->getQuantity();
                }
            }
        } catch (\Exception $e) {
            \App::logger()->error($e, ['kissmetriks']);
        }

        return $return;
    }

    public static function getOrderNewEvent() {
        $cart = \App::user()->getCart();

        $return = [
            'productQuantity'  => $cart->getProductsQuantity(),
            'productSum'       => $cart->getTotalProductPrice(),
            'serviceQuantity'  => $cart->getServicesQuantity(),
            'serviceSum'       => $cart->getTotalServicePrice(),
            'warrantyQuantity' => $cart->getWarrantiesQuantity(),
            'warrantySum'      => $cart->getTotalWarrantyPrice(),
            'sum'              => $cart->getSum(),
        ];

        return $return;
    }
}