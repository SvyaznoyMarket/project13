<?php

namespace EnterSite\Repository\Partial;

use EnterSite\ConfigTrait;
use EnterSite\ViewHelperTrait;
use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;

class DirectCredit {
    use ConfigTrait, ViewHelperTrait;

    /**
     * @param Model\Product[] $products
     * @param Model\Cart\Product[] $productCartsById
     * @return Partial\DirectCredit
     */
    public function getObject(
        $products = [],
        $productCartsById = []
    ) {
        $directCredit = new Partial\DirectCredit();

        $productData = [];
        foreach ($products as $product) {
            /** @var Model\Product\Category|null $rootCategory */
            $rootCategory = ($product->category && !empty($product->category->ascendants[0])) ? $product->category->ascendants[0] : null;
            /** @var Model\Cart\Product|null $cartProduct */
            $cartProduct = !empty($productCartsById[$product->id]) ? $productCartsById[$product->id] : null;

            $productData[] = [
                'id'    => $product->id,
                'name'  => $product->name,
                'price' => $product->price,
                'count' => $cartProduct ? $cartProduct->quantity : 1,
                'type'  => $rootCategory ? (new Repository\DirectCredit())->getTypeByCategoryToken($rootCategory->token) : null,
            ];
        }

        $directCredit->widgetId = 'id-creditPayment';
        $directCredit->dataValue = $this->getViewHelper()->json([
            'partnerId' => $this->getConfig()->directCredit->partnerId,
            'product'   => $productData,
        ]);

        return $directCredit;
    }

    /**
     * @return string
     */
    public static function getWidgetId() {
        return 'id-directCredit';
    }
}