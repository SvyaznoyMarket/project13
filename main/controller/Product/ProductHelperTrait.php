<?php


namespace controller\Product;


trait ProductHelperTrait {

    /** Убираем из массива продуктов товары с разными размерами
     * @param \Model\Product\Entity[] $collection
     * @return \Model\Product\Entity[]|[]
     */
    public static function filterByModelId($collection) {
        $modelIds = [];
        return array_filter($collection, function ($product) use (&$modelIds) {
            // Оставим элемент в этих случаях
            if (!($product instanceof \Model\Product\Entity) || is_null($product->getModelId())) return true;
            if (!in_array($product->getModelId(), $modelIds)) {
                $modelIds[] = $product->getModelId();
            } else return false;
            return true;
        });
    }

}