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
                $categoryRate = 0.005;

                // пройдем по категориям, начиная с самой глубокой
                foreach (array_reverse($categoriesArr) as $category) {
                    $rate = null;
                    /** @var Category $category */
                    switch ($category->ui) {
                        case 'f7a2f781-c776-4342-81e8-ab2ebe24c51a':  // Мебель
                            $rate = 0.136;
                            break;
                        case '0e80c81b-31c9-4519-bd10-e6a556fe000c':  // Сделай сам
                            $rate = 0.0858;
                            break;
                        case 'feccd951-d555-42c2-b417-a161a78faf03': // Детские товары
                            $rate = 0.0387;
                            break;
                        case 'b8569e65-e31e-47a1-af20-5b06aff9f189': // Товары для дома
                            $rate = 0.0652;
                            break;
                        case 'd91b814f-0470-4fd5-a2d0-a0449e63ab6f': // Электроника
                            $rate = 0.022;
                            break;
                        case 'c9c2dc8d-1ee5-4355-a0c1-898f219eb892':  // Подарки и хобби
                            $rate = 0.065;
                            break;
                        case '616e6afd-fd4d-4ff4-9fe1-8f78236d9be6': // Бытовая техника
                            $rate = 0.0415;
                            break;
                        case '022fa1e3-c51f-4a48-87fc-de2c917176d6': // Украшения и часы
                            $rate = 0.13;
                            break;
                        case '19b9f12c-d489-4540-9a17-23dba0641166': // Парфюмерия и косметика
                            $rate = 0.0429;
                            break;
                        case '846eccd2-e9f0-4ce4-b7a2-bb28a835fd7a': // Спорт и отдых
                            $rate = 0.099;
                            break;
                        case 'b933de12-5037-46db-95a4-370779bb4ee2': // Зоотовары
                            $rate = 0.0349;
                            break;
                        case 'caf18e17-550a-4d3e-8285-b1c9cc99b5f4': // Товары Tchibo
                            $rate = 0.1144;
                            break;
                        case '5f3aa3be-1ac2-4dff-a473-c603e6e51e41': // Красота и здоровье
                            $rate = 0.039;
                            break;
                        case '5e78849d-01e8-4509-8bfe-85f8e148b37d': // Аксессуары
                            $rate = 0.055;
                            break;
                        case 'ed807fca-962b-4b75-9813-d5efbb8ef586': // Игры и консоли
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

            $link = sprintf('actionpay=%s&apid=%s&price=%F&purchase=%F', $actionpayId, $order->getNumber(), $partnerCommission, $order->getSum());
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