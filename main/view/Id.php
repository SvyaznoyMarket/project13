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
     * Генерирует ид фильтра в каталоге товаров
     *
     * @param $filterId
     * @return string
     */
    public static function productCategoryFilter($filterId) {
        return sprintf('id-productCategory-filter-%s', $filterId);
    }
}
