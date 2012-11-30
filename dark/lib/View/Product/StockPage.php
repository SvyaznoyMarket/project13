<?php

namespace View\Product;

class StockPage extends \View\DefaultLayout {
    /** @var string */
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        /** @var $product \Model\Product\Entity */
        $product = $this->getParam('product') instanceof \Model\Product\Entity ? $this->getParam('product') : null;
        if (!$product) {
            return;
        }

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = array();

            foreach ($product->getCategory() as $category) {
                $breadcrumbs[] = array(
                    'name' => $category->getName(),
                    'url'  => $category->getLink(),
                );
            }
            $breadcrumbs[] = array(
                'name' => $product->getName(),
                'url'  => $product->getLink(),
            );
            $breadcrumbs[] = array(
                'name' => 'Где купить ' . $product->getName(),
                'url'  => null,
            );

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        // seo: page meta
        $this->setTitle(sprintf(
            'Где купить %s в магазинах Enter - интернет-магазин Enter.ru',
            $product->getName()
        ));
        $this->addMeta('keywords', sprintf('%s где купить %s', $product->getName(), \App::user()->getRegion()->getName()));
    }

    public function slotContent() {
        return $this->render('product/page-stock', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'product_stock';
    }
}
