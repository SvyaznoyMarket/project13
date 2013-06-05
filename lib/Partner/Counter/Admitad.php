<?php

namespace Partner\Counter;

class Admitad {
    const NAME = 'admitad';

    /**
     * @param \Model\Order\Entity     $order
     * @param \Model\Product\Entity[] $productsById
     * @return string[]
     */
    public static function getLinks(\Model\Order\Entity $order, array &$productsById = []) {
        $links = [];

        try {
            foreach ($order->getProduct() as $orderProduct) {
                /** @var $product \Model\Product\Entity */
                $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;
                if (!$product) continue;


                $categories = $product->getCategory();
                /** @var $category \Model\Product\Category\Entity */
                $category = reset($categories);
                if (!$category) continue;

                $admitadCategoryType = self::getCategoryType($categories);
                if (!$admitadCategoryType) continue;

                $admitadUserId = \App::request()->cookies->get('admitad_uid');
                /*
                if (!$admitadUserId) {
                    \App::logger()->error(sprintf('В куках отсутсвует admitad_uid'));
                    continue;
                }
                */

                $link = strtr('http://ad.admitad.com/register/cdaf092422/script_type/img/payment_type/sale/product/{admitad.category.type}/cart/{order.sum}/order_id/{order.number}/uid/{admitad.user.id}/tracking/{category.token}/', [
                    '{admitad.category.type}' => $admitadCategoryType,
                    '{order.sum}'             => (int)$orderProduct->getPrice() * $orderProduct->getQuantity(),
                    '{order.number}'          => $order->getNumber() . '_' . uniqid(),
                    '{admitad.user.id}'       => $admitadUserId,
                    '{category.token}'        => $category ? $category->getToken() : null,
                ]);

                $links[] = $link;
            }
        } catch (\Exception $e) {
            \App::logger()->error($e, ['partner', 'admitad']);
        }

        return $links;
    }

    /**
     * @param \Model\Product\Category\Entity[] $categories
     * @return int|null
     */
    public static function getCategoryType(array $categories) {
        foreach ($categories as $category) {
            switch ($category->getId()) {
                case 80:  // Мебель
                    return 7;
                case 320: // Детские товары
                    return 9;
                case 443: // Товары для дома
                    return 7;
                case 647: // Спорт и отдых
                    return 12;
                case 21:  // Красота и здоровье
                    return 13;
                case 224:  // Сделай сам
                    return 15;
                case 225:  // Аксессуары для авто
                    return 15;
                case 185:  // Подарки и хобби
                    return 11;
                case 2545: // Парфюмерия и косметика
                    return 10;
                case 1438: // Зоотовары
                    return 3;
                case 788: // Электроника
                    return 8;
                case 1: // Бытовая техника
                    return 5;
                case 923: // Украшения и часы
                    return 6;
                case 311: // Сад и огород
                    return 15;
            }
        }

        return null;
    }
}