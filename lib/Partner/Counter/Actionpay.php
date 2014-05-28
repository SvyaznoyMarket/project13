<?php

namespace Partner\Counter;

class Actionpay {
    const NAME = 'actionpay';

    /**
     * @param \Model\Order\Entity[] $orders
     * @param \Model\Product\Entity[] $productsById
     * @return null|string
     */
    public static function getOrderCompleteLink(array $orders, array &$productsById = []) {
        $link = null;

        try {
            $actionpayId = \App::request()->cookies->get('actionpay');
            if (!$actionpayId) {
                \App::logger()->error(['action' => __METHOD__, 'message' => 'В куках отсутсвует actionpay'], ['partner', 'actionpay']);
            }

            /** @var \Model\Order\Entity $order */
            $order = reset($orders);
            if (!$order) {
                throw new \Exception('Заказ не передан');
            }

            $orderSum = 0;
            foreach ($orders as $order) {
                foreach ($order->getProduct() as $orderProduct) {
                    /** @var $product \Model\Product\Entity */
                    $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;
                    if (!$product) {
                        \App::logger()->warn(sprintf('В заказе @%s не найден товар #%s', $order->getNumber(), $orderProduct->getId()));
                        continue;
                    }

                    $category = $product->getMainCategory();
                    if (!$category) {
                        \App::logger()->warn(sprintf('В заказе @%s не найдена категория для товара #%s', $order->getNumber(), $orderProduct->getId()));

                        $categoriesArr = $product->getCategory();
                        if ($categoriesArr) $category = reset($categoriesArr);

                        if (!$category) $category = $product->getParentCategory();
                    }

                    $categoryRate = 0.005; //на неопределенные товары по умолчанию ставим минимальный процент, для web-мастеров =0,5%

                    if ($category) {
                        switch ($category->getId()) {
                            case 80:  // Мебель
                                $categoryRate = 0.1236;
                                break;
                            case 224:  // Сделай сам
                                $categoryRate = 0.0532;
                                break;
                            case 225:  // Аксессуары для авто
                                $categoryRate = 0.0532;
                                break;
                            case 1438: // Зоотовары
                                $categoryRate = 0.0532;
                                break;
                            case 320: // Детские товары
                                $categoryRate = 0.0827;
                                break;
                            case 443: // Товары для дома
                                $categoryRate = 0.1236;
                                break;
                            case 788: // Электроника
                                $categoryRate = 0.0234;
                                break;
                            case 1024: //Электроника => Аксессуары
                                $categoryRate = 0.0591;
                                break;
                            case 185:  // Подарки и хобби
                                $categoryRate = 0.0591;
                                break;
                            case 1: // Бытовая техника
                                $categoryRate = 0.0492;
                                break;
                            case 21:  // Красота и здоровье
                                $categoryRate = 0.0355;
                                break;
                            case 923: // Украшения и часы
                                $categoryRate = 0.1182;
                                break;
                            case 2545: // Парфюмерия и косметика
                                $categoryRate = 0.0709;
                                break;
                            case 647: // Спорт и отдых
                                $categoryRate = 0.09;
                                break;
                            case 868: // Электроника => Портативная электроника
                                $categoryRate = 0.0473;
                                break;
                            case 4506: // Товары Tchibo
                                $categoryRate = 0.104;
                                break;
                            default:
                                if ( 'cpo' == \App::request()->cookies->get('utm_medium') ) {
                                    //для всех товаров по которым не удалось расчитать %
                                    $categoryRate = 0.0065; // CPO агрегатора = 0,65%
                                }
                        }
                    }

                    $orderSum += $orderProduct->getPrice() * $categoryRate * $orderProduct->getQuantity();

                    /*if ((0 == $rate) || ($categoryRate < $rate)) {
                        $rate = $categoryRate;
                    }*/
                }
            }

            $link = strtr('actionpay={actionpayId}&apid={order.id}&price={sum}', [
                '{actionpayId}' => $actionpayId,
                '{order.id}'    => $order->getNumber(),
                '{sum}'         => $orderSum,
            ]);
        } catch (\Exception $e) {
            \App::logger()->error($e, ['partner', 'actionpay ' . __METHOD__]);
        }

        return $link;
    }


    /**
     * @return null|string
     */
    public static function getSubscribeLink() {
        $link = null;

        try {
            $actionpayId = \App::request()->cookies->get('actionpay');
            if (!$actionpayId) {
                \App::logger()->error(['action' => __METHOD__, 'message' => 'В куках отсутсвует actionpay'], ['partner', 'actionpay']);
            }

            $appid = null;
            $user = \App::user();
            if ( $user ) {
                $appid = 0;
                $uEntity = $user->getEntity();
                if ( $uEntity ) {
                    $appid = $uEntity->getEmail();
                }
            }

            $link = strtr('actionpay={actionpayId}&apid={appid}', [
                '{actionpayId}' => $actionpayId,
                '{appid}'    => $appid,
            ]);
        } catch (\Exception $e) {
            \App::logger()->error($e, ['partner', 'actionpay ' . __METHOD__]);
        }

        return $link;
    }

}