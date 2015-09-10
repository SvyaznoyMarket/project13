<?php
/**
 * Created by PhpStorm.
 * User: rmn
 * Date: 02.02.15
 * Time: 15:29
 */

namespace View\Partners;


class Hubrus {

    /** Возвращает div с данными по продукту
     * @param \Model\Product\Entity|null $product
     * @return string
     */
    public static function addProductData($product) {
        $productData = self::getProductInfo($product);
        if ($productData) return '<div class="hubrusProductData" data-value="'.\App::helper()->json(self::getProductInfo($product)).'"></div>';
    }

    /** Возвращает div с данными о продуктах
     * @param string $propertyName
     * @param \Model\Product\Entity[]|\Model\Cart\Product\Entity[|null $products
     * @return string
     */
    public static function addHubrusData($propertyName, $products) {

        if (empty($products)) return '';

        $json = \App::helper()->json(array_values(array_map(function($product) {
            if ($product instanceof \Model\Product\Entity) {
                return self::getProductInfo($product);
            } else if ($product instanceof \Model\Cart\Product\Entity) {
                return self::getProductInfoFromCartProduct($product);
            }
        }, $products )));

        return sprintf('<div class="hubrusData" data-property="%s" data-value="%s"></div>', $propertyName, $json);
    }

    /** Возвращает информацию по продукту
     * @param $cartProduct
     * @return array
     */
    private static function getProductInfoFromCartProduct(\Model\Cart\Product\Entity $cartProduct) {
        $info = [];
        if ($cartProduct->id !== null) $info['id'] = $cartProduct->id;
        if ($cartProduct->rootCategory && $cartProduct->rootCategory->id !== null) $info['category'] = $cartProduct->rootCategory->id;
        if ($cartProduct->price !== null) $info['price'] = $cartProduct->price;
        return $info;
    }

    /** Возвращает информацию по продукту
     * @param $product
     * @return array
     */
    private static function getProductInfo($product) {
        if ($product instanceof \Model\Product\Entity) {
            return [
                'id'        => $product->getId(),
                'category'  => $product->getRootCategory() ? $product->getRootCategory()->getId() : null,
                'price'     => $product->getPrice()
            ];
        }
    }

}