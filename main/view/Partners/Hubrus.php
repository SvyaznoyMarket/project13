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
     * @param \Model\Product\Entity[]|null $products
     * @return string
     */
    public static function addHubrusData($propertyName, $products) {

        if (empty($products)) return '';

        $json = \App::helper()->json(array_values(array_map(function($product) {
            return $product instanceof \Model\Product\Entity ? self::getProductInfo($product) : self::getProductInfoFromArray($product);
        }, $products )));

        return sprintf('<div class="hubrusData" data-property="%s" data-value="%s"></div>', $propertyName, $json);
    }

    /** Возвращает информацию по продукту
     * @param $arr
     * @return array
     */
    public static function getProductInfoFromArray($arr) {
        $info = [];
        if (isset($arr['id'])) $info['id'] = $arr['id'];
        if (isset($arr['rootCategory']['id'])) $info['category'] = $arr['rootCategory']['id'];
        if (isset($arr['price'])) $info['price'] = $arr['price'];
        return $info;
    }

    /** Возвращает информацию по продукту
     * @param $product
     * @return array
     */
    public static function getProductInfo($product) {
        if ($product instanceof \Model\Product\Entity) {
            return [
                'id'        => $product->getId(),
                'category'  => $product->getRootCategory() ? $product->getRootCategory()->getId() : null,
                'price'     => $product->getPrice()
            ];
        }
    }

}