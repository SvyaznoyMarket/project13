<?php

namespace View\Slice;

class ShowPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        /** @var $slice \Model\Slice\Entity */
        $slice = $this->getParam('slice') instanceof \Model\Slice\Entity ? $this->getParam('slice') : null;
        if (!$slice) {
            return;
        }

        $this->setTitle($slice->getTitle());
        $this->addMeta('description', $slice->getMetaDescription());
        $this->addMeta('keywords', $slice->getMetaKeywords());
    }

    public function slotContent() {
        return $this->render('slice/page-show', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }

    public function slotUserbar() {
        return $this->render('_userbar');
    }

    public function slotUserbarContent() {
        return $this->render('slice/_userbarContent', [
            'category'  => $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null,
            'slice'     => $this->getParam('slice')    instanceof \Model\Slice\Entity            ? $this->getParam('slice')    : null,
            'fixedBtn'  => [
                'link'       => '#',
                'name'       => 'Категории',
                'title'      => '',
                'class'      => '',
                'showCorner' => true,
            ],
        ]);
    }

    public function slotUserbarContentData() {
        return [
            //'target' => '#productCatalog-filter-form',
            'target' => '.bCatalogList',
        ];
    }

    public function slotMailRu() {
        /** @var \Iterator\EntityPager $productPages */
        $productPager = $this->getParam('productPager');
        $productIds = [];
        if (is_object($productPager) && $productPager instanceof \Iterator) {
            foreach ($productPager as $product) {
                if (is_object($product) && $product instanceof \Model\Product\Entity) {
                    $productIds[] = $product->getId();
                }
            }
        }

        return $this->render('_mailRu', [
            'pageType' => 'slice',
            'productIds' => $productIds,
            'price' => '',
        ]);
    }
}
