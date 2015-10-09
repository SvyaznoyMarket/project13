<?php

namespace View\ClosedSale;


use \Model\ClosedSale\ClosedSaleEntity;
use \Model\Product\Entity as Product;
use \View\DefaultLayout;

class SaleShowPage extends DefaultLayout
{
    /**
     * @var ClosedSaleEntity
     */
    private $sale;

    public function prepare()
    {
        parent::prepare();

        $this->sale = $this->getParam('currentSale');

        $availableProducts = array_filter(
            $this->sale->products,
            function (Product $product) {
                return $product->getIsBuyable();
            }
        );

        $this->setParam('availableProductCount', count($availableProducts));
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }

    public function slotContent()
    {
        return $this->render('closed-sale/sale-one', $this->params);
    }

}