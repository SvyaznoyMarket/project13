<?php

namespace Partner\Counter;

use \Model\Order\Entity as Order;
use \Model\Product\Category\Entity as Category;

class Actionpay {

    const NAME = 'actionpay';

    /**
     * @link https://docs.google.com/document/d/1e-dbe51-ez78aQoSwmsmlnUHf6I6uQIhAZaw4GNDKEc
     * @param Order $order
     * @param \Model\Product\Entity[] $productsById
     * @return null|string
     */
    public static function getOrderCompleteLink(Order $order, array &$productsById = []) {

        $link = null;

        try {

            $actionpayId = self::getActionpayId();

            $partnerCommission = 0;

            foreach ($order->getProduct() as $orderProduct) {
                /** @var $product \Model\Product\Entity */
                $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;

                if (!$product) {
                    \App::logger()->warn(sprintf('В заказе @%s не найден товар #%s', $order->getNumber(), $orderProduct->getId()));
                    continue;
                }

                /** @var Category[] $categoriesArr */
                $categoriesArr = $product->getCategory();

                // default category rate
                $categoryRate = 0.033;

                // пройдем по категориям, начиная с самой глубокой
                foreach (array_reverse($categoriesArr) as $category) {
                    $rate = null;
                    /** @var Category $category */
                    switch ($category->ui) {
                        case Category::UI_MEBEL:  // Мебель
                            $rate = 0.136;
                            break;
                        case Category::UI_SDELAY_SAM:  // Сделай сам
                            $rate = 0.0858;
                            break;
                        case Category::UI_DETSKIE_TOVARY: // Детские товары
                            $rate = 0.0387;
                            break;
                        case Category::UI_TOVARY_DLYA_DOMA: // Товары для дома
                            $rate = 0.0652;
                            break;
                        case Category::UI_ELECTRONIKA: // Электроника
                            $rate = 0.022;
                            break;
                        case Category::UI_PODARKI_I_HOBBY:  // Подарки и хобби
                            $rate = 0.065;
                            break;
                        case Category::UI_BYTOVAYA_TEHNIKA: // Бытовая техника
                            $rate = 0.0415;
                            break;
                        case Category::UI_UKRASHENIYA_I_CHASY: // Украшения и часы
                            $rate = 0.13;
                            break;
                        case Category::UI_PARFUMERIA_I_COSMETIKA: // Парфюмерия и косметика
                            $rate = 0.0429;
                            break;
                        case Category::UI_SPORT_I_OTDYH: // Спорт и отдых
                            $rate = 0.099;
                            break;
                        case Category::UI_ZOOTOVARY: // Зоотовары
                            $rate = 0.0349;
                            break;
                        case Category::UI_TCHIBO: // Товары Tchibo
                            $rate = 0.1144;
                            break;
                        case Category::UI_KRASOTA_I_ZDOROVIE: // Красота и здоровье
                            $rate = 0.039;
                            break;
                        case Category::UI_AKSESSUARY: // Аксессуары
                            $rate = 0.055;
                            break;
                        case Category::UI_IGRY_I_KONSOLI: // Игры и консоли
                            $rate = 0.065;
                            break;
                    }

                    if ($rate !== null) {
                        $categoryRate = $rate;
                        break;
                    }
                }

                $partnerCommission += $orderProduct->getPrice() * $categoryRate * $orderProduct->getQuantity();

            }

            $link = sprintf('actionpay=%s&apid=%s&price=%F&totalPrice=%F', $actionpayId, $order->getNumber(), $partnerCommission, $order->getSum());
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

            $actionpayId = self::getActionpayId();

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

    /** Возвращает значение из куки "actionpay"
     * @return null|string
     */
    public static function getActionpayId() {

        $actionpayId = \App::request()->cookies->get('actionpay');

        if (!$actionpayId) {
            \App::logger()->error(['action' => __METHOD__, 'message' => 'В куках отсутсвует actionpay'], ['partner', 'actionpay']);
        }

        return $actionpayId;
    }

}