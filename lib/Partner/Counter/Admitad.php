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
            foreach ($order->getProduct() as $cartProduct) {
                /** @var $product \Model\Product\Entity */
                $product = isset($productsById[$cartProduct->getId()]) ? $productsById[$cartProduct->getId()] : null;
                if (!$product) continue;


                $categories = $product->getCategory();
                /** @var $category \Model\Product\Category\Entity */
                $category = reset($categories);
                if (!$category) continue;

                $admitadCategoryType = self::getCategoryType($category);
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
                    '{order.sum}'             => (int)$cartProduct->getPrice(),
                    '{order.number}'          => $order->getNumber() . '_' . uniqid(),
                    '{admitad.user.id}'       => $admitadUserId,
                    '{category.token}'        => $category ? $category->getToken() : null,
                ]);

                $links[] = $link;
            }
        } catch (\Exception $e) {
            \App::logger()->error($e);
        }

        return $links;
    }

    public static function getCategoryType(\Model\Product\Category\Entity $category) {
        switch ($category->getId()) {
            case 80:  // Мебель
            case 320: // Детские товары
            case 443: // Товары для дома
            case 647: // Спорт и отдых
            case 21:  // Красота и здоровье
                return 1;
            case 224:  // Сделай сам
            case 225:  // Аксессуары для авто
            case 185:  // Подарки и хобби
            case 2545: // Парфюмерия и косметика
                return 2;
            case 1438: // Зоотовары
                return 3;
            case 788: // Электроника
                return 4;
            case 1: // Бытовая техника
                return 5;
            case 923: // Украшения и часы
                return 6;
            default:
                return null;
        }
    }
}