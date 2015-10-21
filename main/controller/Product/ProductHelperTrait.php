<?php


namespace controller\Product;


trait ProductHelperTrait {

    /** Убираем из массива продуктов товары с разными размерами
     * @param \Model\Product\Entity[] $collection
     * @return \Model\Product\Entity[]|[]
     */
    public static function filterByModelId($collection) {
        $modelUis = [];
        return array_filter($collection, function ($product) use (&$modelUis) {
            // Оставим элемент в этих случаях
            if (!($product instanceof \Model\Product\Entity) || !$product->model || !$product->model->ui) {
                return true;
            }

            if (!in_array($product->model->ui, $modelUis, true)) {
                $modelUis[] = $product->model->ui;
                return true;
            } else {
                return false;
            }
        });
    }
}