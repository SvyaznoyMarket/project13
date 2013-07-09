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
        return $this->render('product/page-line-new', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'product_card';
    }

    public function slotInnerJavascript() {
        /** @var $product \Model\Product\Entity */
        $product = $this->getParam('mainProduct') instanceof \Model\Product\Entity ? $this->getParam('mainProduct') : null;
        if ($product) {
            $categories = $product->getCategory();
            $category = array_pop($categories);
        }

        return ''
            . "\n\n"
            . (bool)$product ? $this->render('_remarketingGoogle', ['tag_params' => ['prodid' => $product->getId(), 'pagetype' => 'product', 'pname' => $product->getName(), 'pcat' => ($category) ? $category->getToken() : '', 'pvalue' => $product->getPrice()]]) : ''
            . "\n\n"
            . $this->render('_innerJavascript');
    }

}