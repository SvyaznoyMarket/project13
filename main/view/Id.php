<?php

namespace View;

class Id {
    /**
     * Генерирует ид кнопки "купить" для товара
     *
     * @param $productId
     * @return string
     */
    public static function cartButtonForProduct($productId) {
        return sprintf('id-cartButton-product-%s', $productId);
    }

    /**
     * Генерирует ид чекбокса для услуги F1 у товара
     *
     * @param $productId
     * @param $serviceId
     * @return string
     */
    public static function cartButtonForProductService($productId, $serviceId) {
        return sprintf('id-cartButton-product-%s-service-%s', $productId, $serviceId);
    }

    /**
     * Генерирует ид кнопки "купить" для услуги F1
     *
     * @param $serviceId
     * @return string
     */
    public static function cartButtonForService($serviceId) {
        return sprintf('id-cartButton-service-%s', $serviceId);
    }

    /**
     * Генерирует ид фильтра в каталоге товаров
     *
     * @param $filterId
     * @return string
     */
    public static function productCategoryFilter($filterId) {
        return sprintf('id-productCategory-filter-%s', $filterId);
    }
}
