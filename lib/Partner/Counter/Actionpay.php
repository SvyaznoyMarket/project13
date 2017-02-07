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
        try {
            $reward = 0;

            foreach ($order->getProduct() as $orderProduct) {
                /** @var $product \Model\Product\Entity */
                $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;

                if (!$product) {
                    \App::logger()->warn(sprintf('В заказе @%s не найден товар #%s', $order->getNumberErp(), $orderProduct->getId()));
                    continue;
                }

                $reward = bcadd(
                    $reward,
                    bcmul(
                        bcmul($orderProduct->getPrice(), $orderProduct->getQuantity(), 2),
                        static::getPartnerProductCategoryRate($product),
                        2
                    ),
                    2
                );
            }

            return 'actionpay=' . self::getActionpayId() . '&apid=' . $order->getNumberErp() . '&price=' . $reward . '&totalPrice=' . bcadd($order->getSum(), 0, 2);
        } catch (\Exception $e) {
            \App::logger()->error($e, ['partner', 'actionpay ' . __METHOD__]);
        }

        return null;
    }

    private static function getPartnerProductCategoryRate(\Model\Product\Entity $product)
    {
        // ПРИ ИЗМЕНЕНИИ ДАННОГО АЛГОРИТМА НЕОБХОДИМО ТАКЖЕ ИЗМЕНИТЬ ПОДОБНЫЙ АЛГОРИТМ В ПРОЕКТЕ CORE В
        // \CronController::getPartnerProductCategoryRate

        if ($product->getPriceOld()) {
            return 0.03;
        }

        foreach (array_reverse($product->getCategory()) as $category) {
            /** @var \Model\Product\Category\Entity $category */
            switch ($category->ui) {
                case 'f7a2f781-c776-4342-81e8-ab2ebe24c51a': // мебель
                case '022fa1e3-c51f-4a48-87fc-de2c917176d6': // украшения и часы
                    return 0.12;
                case '6270ed26-3582-4749-8e0d-2e8373f600b0': // одежда > аксессуары
                case 'a72e6335-d62c-4a46-85a6-306cd1c8af14': // бытовая техника > аксессуары
                case '1f087575-d6c2-45f4-8c1b-dadabca45141': // спорт и отдых > велосипеды > аксессуары
                case 'ed1ac096-66b8-4b55-9941-34ade3dc6725': // товары для дома > аксессуары для ванной
                case '5e78849d-01e8-4509-8bfe-85f8e148b37d': // электронника > аксессуары
                case 'f30feba1-915e-40e5-9344-35b535085a76': // детские товары > аксессуары для автокресел
                case 'eba838c7-77f0-4e75-a631-b8280caddfc2': // зоотовары > аксессуары для аквариумов
                    return 0.10;
                case '846eccd2-e9f0-4ce4-b7a2-bb28a835fd7a': // спорт и отдых
                    return 0.09;
                case 'df56d956-3b07-4fca-ad47-d116a0f5104e': // одежда
                case 'feccd951-d555-42c2-b417-a161a78faf03': // детские товары
                case '19b9f12c-d489-4540-9a17-23dba0641166': // парфюмерия и косметика
                case '0e80c81b-31c9-4519-bd10-e6a556fe000c': // сделай сам
                case 'e86ccf17-e161-4d5e-8158-2a8ee458b8e7': // сад и огород
                    return 0.08;
                case 'c9c2dc8d-1ee5-4355-a0c1-898f219eb892': // подарки и хобби
                    return 0.07;
                case 'b8569e65-e31e-47a1-af20-5b06aff9f189': // товары для дома
                case 'f0d53c46-d4fc-413f-b5b3-a2b57b93a717': // авто
                case 'b933de12-5037-46db-95a4-370779bb4ee2': // зоотовары
                    return 0.06;
                case 'd91b814f-0470-4fd5-a2d0-a0449e63ab6f': // электронника
                case '616e6afd-fd4d-4ff4-9fe1-8f78236d9be6': // бытовая техника
                    return 0.03;
            }
        }

        return 0;
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