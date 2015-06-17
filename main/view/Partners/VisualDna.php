<?php
/**
 * Created by PhpStorm.
 * User: julian
 * Date: 12/17/13
 * Time: 10:07 AM
 */

namespace View\Partners;


class VisualDna {
// Данный класс и партнёр не используется
    private $partnerLink = '//e.visualdna.com/conversion?api_key=enter.ru';


    /**
     * add VisualDNA pixel, SITE-2773
     *
     * @param $orders
     * @param $paymentMethod
     */
    public function routeOrderComplete(array $orders, array $productsById, \Model\PaymentMethod\Entity $paymentMethod) {
        /* @var     $orders         \Model\Order\Entity[] */
        /* @var     $productsById   \Model\Product\Entity[] */

        $return = '';

        try{
            $productInfo = [];
            $uid = $this->getUserId();

            foreach ($orders as $i => $order) {
                $productInfo[$i] = [];

                foreach ($order->getProduct() as $j => $orderProduct) {
                    $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;
                    if (!$product) continue;

                    $productInfo[$i][$j] = [
                        'id'        => $product->getId(),
                        'price'     => $product->getPrice(),
                        'category'  => $this->getProductCategoryId($product),
                    ];
                }
            }

            $src = $this->partnerLink . sprintf(
                '&id=transaction&value=%d&currency=RUB&partner_user_id=%d&payment_type=%d',
                (int) $order->getSum(),
                (int) $uid,
                (int) $paymentMethod->getId()
                //(string) $paymentMethod->getName()
            );
            $return .= $this->makeImg($src);
            //$return .= '<img src="//e.visualdna.com/conversion?api_key=enter.ru&id=transaction&value='. $order->getSum() .'&currency=RUB&partner_user_id=' . $uid .'&payment_type=' . $paymentMethod->getName() .'" width="1" height="1" alt="" />';

            foreach($productInfo[$i] as $j => $img) {
                $src = $this->partnerLink . sprintf(
                    '&id=purchased&product_id=%d&product_category=%d&value=%d&currency=RUB',
                    $img['id'],
                    $img['category'],
                    $img['price']
                );
                $return .= $this->makeImg($src);
                //$return .= '<img src="//e.visualdna.com/conversion?api_key=enter.ru&id=purchased&product_id=' . $img['id'] . '&product_category=' . $img['category'] . '&value=' . $img['price'] . '&currency=RUB" width="1" height="1" alt="" />';
            }

        } catch (\Exception $e) {
            \App::logger()->error($e, [__CLASS__]);
        }

        return $return;
    }


    /**
     * @param $src
     * @return string
     */
    private function makeImg($src) {
        return '<img src="' . (string) $src . '" width="1" height="1" alt="" />';
    }


    /**
     * @return int|null
     */
    private function getUserId() {
        $uid = $uEntity = null;

        $user = \App::user();
        if ($user) {
            $uEntity = $user->getEntity();
            if ($uEntity) {
                $uid = $uEntity->getId();
            }
        }

        return $uid;
    }


    /**
     * @param \Model\Product\Entity $product
     * @return int
     */
    private function getProductCategoryId(\Model\Product\Entity $product) {
        $productCategory = null;

        if ( $product->getRootCategory() ) {
            $productCategory = $product->getRootCategory();
        }else{
            $productCategory = $product->getCategory();
            $productCategory = reset($productCategory);
        }

        if ($productCategory) {
            /** @var $productCategory   \Model\Product\Category\Entity */
            return $productCategory->getId();
        }

        return 0;
    }
} 