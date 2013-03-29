<?php

namespace View\Product;

class UpsellPage extends \View\DefaultLayout {
    protected $layout = 'layout-oneColumn';

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

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        $this->setTitle('');
    }
    public function slotContent() {
        return $this->render('product/page-upsell', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'product_card';
    }

    public function slotFooter() {
        try {
            $response = \App::contentClient()->query('footer_compact');
        } catch (\Exception $e) {
            \App::exception()->add($e);
            \App::logger()->error($e);

            $response = array('content' => '');
        }

        return $this->render('order/_footer', $this->params) . "\n\n" . $response['content'];
    }

    public function slotInnerJavascript() {
        /** @var $product \Model\Product\Entity */
        $product = $this->getParam('product') instanceof \Model\Product\Entity ? $this->getParam('product') : null;
        if ($product) {
            $categories = $product->getCategory();
            $category = array_pop($categories);
        }

        return ''
            . ($product ? $this->render('product/partner-counter/_odinkod', array('product' => $product)) : '')
            . "\n\n"
            . (bool)$product ? $this->render('_remarketingGoogle', ['tag_params' => ['prodid' => $product->getId(), 'pagetype' => 'product', 'pname' => $product->getName(), 'pcat' => ($category) ? $category->getToken() : '', 'pvalue' => $product->getPrice()]]) : ''
            . "\n\n"
            . $this->render('_innerJavascript');
    }

}