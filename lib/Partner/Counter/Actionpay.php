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
                \App::logger()->error(sprintf('В куках отсутсвует actionpay'), ['partner', ['actionpay']]);
            }

            /** @var \Model\Order\Entity $order */
            $order = reset($orders);
            if (!$order) {
                throw new \Exception('Заказ не передан');
            }

            $sum = 0;
            $rate = 0;
            foreach ($orders as $order) {
                $sum += $order->getSum();

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
                        continue;
                    }

                    $categoryRate = 0;
                    switch ($category->getId()) {
                        case 80:  // Мебель
                            $categoryRate = 0.065;
                            break;
                        case 224:  // Сделай сам
                            $categoryRate = 0.039;
                            break;
                        case 225:  // Аксессуары для авто
                            $categoryRate = 0.039;
                            break;
                        case 1438: // Зоотовары
                            $categoryRate = 0.052;
                            break;
                        case 320: // Детские товары
                            $categoryRate = 0.065;
                            break;
                        case 443: // Товары для дома
                            $categoryRate = 0.065;
                            break;
                        case 788: // Электроника
                            $categoryRate = 0.0195;
                            break;
                        case 185:  // Подарки и хобби
                            $categoryRate = 0.039;
                            break;
                        case 1: // Бытовая техника
                            $categoryRate = 0.026;
                            break;
                        case 21:  // Красота и здоровье
                            $categoryRate = 0.065;
                            break;
                        case 923: // Украшения и часы
                            $categoryRate = 0.078;
                            break;
                        case 2545: // Парфюмерия и косметика
                            $categoryRate = 0.039;
                            break;
                        case 647: // Спорт и отдых
                            $categoryRate = 0.065;
                            break;
                    }

                    if ((0 == $rate) || ($categoryRate < $rate)) {
                        $rate = $categoryRate;
                    }
                }
            }

            $link = strtr('http://n.actionpay.ru/ok/3781.png?actionpay={actionpayId}&apid={order.id}&price={sum}', [
                '{actionpayId}' => $actionpayId,
                '{order.id}'    => $order->getNumber(),
                '{sum}'         => $sum * $rate,
            ]);
        } catch (\Exception $e) {
            \App::logger()->error($e, ['partner', 'actionpay']);
        }

        return $link;
    }
}