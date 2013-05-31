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
                    $return['product']['warrantyQuantity'] = $cartWarranty ? $cartWarranty->getQuantity() : null;
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
            'cart' => [
                'productQuantity'  => $cart->getProductsQuantity(),
                'productSum'       => $cart->getTotalProductPrice(),
                'serviceQuantity'  => $cart->getServicesQuantity(),
                'serviceSum'       => $cart->getTotalServicePrice(),
                'warrantyQuantity' => $cart->getWarrantiesQuantity(),
                'warrantySum'      => $cart->getTotalWarrantyPrice(),
                'sum'              => $cart->getSum(),
            ],
        ];

        return $return;
    }

    /**
     * @param \Model\Product\Entity $product
     * @param int $position
     * @param int $page
     * @return array
     */
    public static function getProductSearchEvent($product, $position = 1, $page = 1) {
        $position = (($page - 1) * \App::config()->product['itemsPerPage']) + $position;
        $return = [
            'article'   =>  $product->getArticle(),
            'name'      =>  $product->getName(),
            'position'  =>  $position,
            'page'      =>  $page,
        ];
        return $return;
    }

    /**
     * @param \Model\Product\Entity $product
     * @param int $position
     * @param string $type
     * @return array
     */
    public static function getProductEvent($product, $position = 1, $type = '') {
        $return = [
            'place'     =>  'product',
            'article'   =>  $product->getArticle(),
            'name'      =>  $product->getName(),
            'position'  =>  $position,
            'type'      =>  $type,
        ];
        return $return;
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @return array
     */
    public static function getCategoryEvent($category) {
        if ($category->isRoot()) {
            $type = 'category';
        } else $type = 'listing';
        $return = [
            'type'              =>  $type,
            'level'             =>  $category->getLevel(),
            'parent_category'   =>  $category->getParent()?$category->getParent()->getName():$category->getName(),
            'category'          =>  $category->getName(),
            'id'                =>  $category->getId(),
        ];
        return $return;
    }

    /**
     * @param \Model\Order\Entity[] $orders
     * @return array
     */
    public static function getOrderCompleteEvent(array $orders) {
        $cart = \App::user()->getCart();

        try {
            foreach ($orders as $order) {
                $productSum = 0;
                foreach ($order->getProduct() as $orderProduct) {
                    $productSum += $orderProduct->getPrice();
                }

                $serviceQuantity = 0;
                $serviceSum = 0;
                foreach ($order->getService() as $orderService) {
                    $serviceSum += $orderService->getPrice();
                    $serviceQuantity++;
                }

                $warrantyQuantity = 0;
                $warrantySum = 0;
                foreach ($order->getProduct() as $orderProduct) {
                    $warrantyQuantity += $orderProduct->getWarrantyQuantity();
                    $warrantySum += $orderProduct->getWarrantyPrice();
                }

                $orderData = [
                    'number'           => $order->getNumber(),
                    'productQuantity'  => count($order->getProduct()),
                    'productSum'       => $productSum,
                    'serviceQuantity'  => $serviceQuantity,
                    'serviceSum'       => $serviceSum,
                    'warrantyQuantity' => $warrantyQuantity,
                    'warrantySum'      => $warrantySum,
                ];

                $return[] = $orderData;
            }
        } catch(\Exception $e) {
            $return = [];
        }

        return $return;
    }
}