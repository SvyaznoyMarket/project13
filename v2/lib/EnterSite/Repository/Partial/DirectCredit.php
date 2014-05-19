<?php

namespace EnterSite\Repository\Partial;

use EnterSite\ConfigTrait;
use EnterSite\ViewHelperTrait;
use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;

class DirectCredit {
    use ConfigTrait, ViewHelperTrait;

    public function getObject(
        Model\Product $product,
        Model\Cart\Product $productCart = null
    ) {
        $directCredit = new Partial\DirectCredit();

        /** @var Model\Product\Category|null $rootCategory */
        $rootCategory = ($product->category && !empty($product->category->ascendants[0])) ? $product->category->ascendants[0] : null;

        $directCredit->widgetId = 'id-creditPayment-' . $product->id;
        $directCredit->dataValue = $this->getViewHelper()->json([
            'partnerId' => $this->getConfig()->directCredit->partnerId,
            'product' => [
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => $product->price,
                'quantity' => $productCart ? $productCart->quantity : 1,
                'type'     => $rootCategory ? (new Repository\DirectCredit())->getTypeByCategoryToken($rootCategory->token) : null,
            ],
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