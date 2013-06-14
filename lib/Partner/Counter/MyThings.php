<?php

namespace Partner\Counter;

class MyThings {
    const NAME = 'mythings';

    /**
    * @param \Model\Order\Entity[] $orders
    * @param \Model\Product\Entity[] $productsById
    * @return null|array
    */
    public static function getCompleteOrder($orders, $productsById) {
        $orderSum = 0;
        foreach ($orders as $order) {
            foreach ($order->getProduct() as $product) {
                $categoryFee = 0;
                if (isset($productsById[$product->getId()]) && $productsById[$product->getId()]->getMainCategory()) {
                    if (isset(\App::config()->myThings['feeByCategory'][$productsById[$product->getId()]->getMainCategory()->getId()])) {
                        $categoryFee = \App::config()->myThings['feeByCategory'][$productsById[$product->getId()]->getMainCategory()->getId()];
                    }
                }
                $orderSum += round($product->getPrice() * $categoryFee * $product->getQuantity(), 2);
            }
            return array(
                'EventType' => 'MyThings.Event.Conversion',
                'Action' => '9902',
                'TransactionReference' => $order->getNumber(),
                'TransactionAmount' => str_replace(',', '.', $order->getSum()), // Полная сумма заказа (дроби через точку)
                'Commission' => $orderSum,
                'Products' => array_map(function($orderProduct){
                    /** @var $orderProduct \Model\Order\Product\Entity  */
                    return array('id' => $orderProduct->getId(), 'price' => $orderProduct->getPrice(), 'qty' => $orderProduct->getQuantity());
                }, $order->getProduct()),
            );
        }

    }

    public static function isTracking() {
        return (bool)(\App::request()->cookies->get( \App::config()->myThings['cookieName'] , false));
    }

}