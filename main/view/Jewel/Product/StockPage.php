<?php

namespace View\Jewel\Product;

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
            $breadcrumbs = [];

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
        return $this->render('jewel/product/page-stock', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'product_stock';
    }

    public function slotInnerJavascript() {
        /** @var $product \Model\Product\Entity */
        $product = $this->getParam('product') instanceof \Model\Product\Entity ? $this->getParam('product') : null;
        $categories = $product->getCategory();
        $category = array_pop($categories);

        return ''
            . ($product ? $this->tryrender('jewel/product/partner-counter/_etargeting', array('product' => $product)) : '')
            . "\n\n"
            . (bool)$product ? $this->render('_remarketingGoogle', ['tag_params' => ['prodid' => $product->getId(), 'pagetype' => 'product', 'pname' => $product->getName(), 'pcat' => ($category) ? $category->getToken() : '', 'pvalue' => $product->getPrice()]]) : ''
            . "\n\n"
            . $this->render('_innerJavascript');
    }
}
