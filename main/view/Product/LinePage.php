<?php

namespace View\Product;

class LinePage extends \View\DefaultLayout {
    protected $layout = 'layout-oneColumn';

    public function prepare() {
        /** @var $product \Model\Product\Entity */
        $product = $this->getParam('mainProduct');
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

        $this->setTitle($product->getName());
    }

    public function slotContentHead() {
        /** @var $line \Model\Product\Line\Entity */
        $line = $this->getParam('line');

        /** @var $product \Model\Product\Entity */
        $product = $this->getParam('mainProduct');

        // заголовок контента страницы
        if (!$this->hasParam('title')) {
            $this->setParam('title', null);
        }
        // навигация
        if (!$this->hasParam('breadcrumbs')) {
            $this->setParam('breadcrumbs', []);
        }

        return $this->render('product/_contentHead', array_merge($this->params, [
            'titlePrefix' => 'Серия ' . $line->getName(),
            'title'       => $product->getName(),
            'product'     => $product,
        ]));
    }

    public function slotContent() {
        return $this->render('product/page-line', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'product_card';
    }


    public function slotUserbarContent() {
        $product = $this->getParam('mainProduct') instanceof \Model\Product\Entity ? $this->getParam('mainProduct') : null;
        if (!$product) {
            return;
        }

        return $this->render('product/_userbarContent', [
            'product'   => $product,
            'line'      => true
        ]);
    }

    public function slotUserbarContentData() {
        $product = $this->getParam('mainProduct') instanceof \Model\Product\Entity ? $this->getParam('mainProduct') : null;
        if (!$product) {
            return;
        }
        /** @var $product \Model\Product\Entity */

        return [
            'target' => '.js-showTopBar',
            'productId' => $product->getId(),
        ];
    }
}