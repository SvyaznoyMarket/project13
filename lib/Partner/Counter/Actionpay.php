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

                    $categoriesArr = $product->getCategory();

                    $mainCategory = $product->getRootCategory();
                    if (!$mainCategory) {
                        \App::logger()->warn(sprintf('В заказе @%s не найдена категория для товара #%s', $order->getNumber(), $orderProduct->getId()));

                        if ($categoriesArr) $mainCategory = reset($categoriesArr);

                        if (!$mainCategory) $mainCategory = $product->getParentCategory();
                    }

                    $secondLevelCategory = null;
                    if (is_array($categoriesArr) && !empty($categoriesArr)) {
                        foreach ($categoriesArr as $category) {
                            if (!$category instanceof \Model\Product\Category\Entity) continue;
                            if (2 !== $category->getLevel()) continue;

                            $secondLevelCategory = $category;
                        }
                    }

                    $categoryRate = 0.005; //на неопределенные товары по умолчанию ставим минимальный процент, для web-мастеров =0,5%

                    // Пытаемся получить rate для категории 2-го уровня
                    $isRateSet = false;
                    if ($secondLevelCategory) {
                        switch ($secondLevelCategory->getId()) {
                            case 225:  // Аксессуары для авто
                                $categoryRate = 0.0585;
                                $isRateSet = true;
                                break;
                            case 2989:  // Красота и здоровье
                                $categoryRate = 0.039;
                                $isRateSet = true;
                                break;
                            case 1024: //Электроника => Аксессуары
                                $categoryRate = 0.065;
                                break;
                            case 868: // Электроника => Портативная электроника
                                $categoryRate = 0.052;
                                break;
                        }
                    }

                    if ($mainCategory && !$isRateSet) {
                        switch ($mainCategory->getId()) {
                            case 80:  // Мебель
                                $categoryRate = 0.136;
                                break;
                            case 224:  // Сделай сам
                                $categoryRate = 0.0585;
                                break;
                            case 1438: // Зоотовары
                                $categoryRate = 0.0585;
                                break;
                            case 320: // Детские товары
                                $categoryRate = 0.091;
                                break;
                            case 443: // Товары для дома
                                $categoryRate = 0.136;
                                break;
                            case 788: // Электроника
                                $categoryRate = 0.0257;
                                break;
                            case 185:  // Подарки и хобби
                                $categoryRate = 0.065;
                                break;
                            case 1: // Бытовая техника
                                $categoryRate = 0.0541;
                                break;
                            case 923: // Украшения и часы
                                $categoryRate = 0.13;
                                break;
                            case 2545: // Парфюмерия и косметика
                                $categoryRate = 0.078;
                                break;
                            case 647: // Спорт и отдых
                                $categoryRate = 0.099;
                                break;
                            case 4506: // Товары Tchibo
                                $categoryRate = 0.1144;
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

            $link = sprintf('actionpay=%s&apid=%s', $actionpayId, $order->getNumber());
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