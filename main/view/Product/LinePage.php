<?php

namespace View\Product;

class LinePage extends \View\DefaultLayout {
    protected $layout = 'layout-oneColumn';

    public function prepare() {
        $product = $this->getParam('mainProduct');
        $line = $this->getParam('line');
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

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        $this->setTitle('Серия ' . $line->getName());
    }
    public function slotContent() {
        return $this->render('product/page-line', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'product_card';
    }

}