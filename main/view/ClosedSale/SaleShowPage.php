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

        $categories = $this->getParam('categories', []);
        $filteredByCategory = \App::request()->query->has('categoryId') && count($categories) === 1;

        $breadcrumbs = [
            [
                'name' => 'Secret Sale',
                'url'  => $this->url('sale.all'),
            ],
            [
                'name' => $this->sale->name,
                'url'  => $filteredByCategory ? $this->url('sale.one', ['uid' => $this->sale->uid]) : null,
                'span' => $filteredByCategory ? false : true
            ]

        ];

        if ($filteredByCategory) {
            $breadcrumbs[] = [
                'name'  => $categories[0]->getName(),
                'url'  => null,
                'span' => true
            ];
        }

        $breadcrumbs[] = [];
        $this->setParam('breadcrumbs', $breadcrumbs);

        $this->setParam('availableProductCount', count($availableProducts));
        $this->setTitle($this->sale->name);
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }

    public function slotContent()
    {
        return $this->render('closed-sale/sale-one', $this->params);
    }

}