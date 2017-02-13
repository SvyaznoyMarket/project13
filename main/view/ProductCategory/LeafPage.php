<?php

namespace View\ProductCategory;

class LeafPage extends Layout {
    public function prepare() {
        parent::prepare();
        $this->setParam('breadcrumbs', call_user_func(function() {
            /** @var \Model\Product\Category\Entity|null $category */
            $category = $this->getParam('category');
            if (!$category) {
                return [];
            }

            /** @var \Model\Product\Category\Entity[] $categories */
            $categories = $category->getAncestor();

            /** @var \Model\Brand\Entity|null $brand */
            $brand = $this->getParam('brand');
            if ($brand) {
                $iCategory = clone $category;
                $iCategory->name = preg_replace('/' . $brand->name . '$/', '', $iCategory->name);
                $categories[] = $iCategory; // SITE-6369
            }

            $breadcrumbs = [];
            $count = count($categories);
            $i = 0;
            foreach ($categories as $ancestor) {
                $i++;

                $breadcrumbs[] = [
                    'url'  => $ancestor->getLink(),
                    'name' => $ancestor->getName(),
                    'last' => $i == $count,
                ];
            }

            return $breadcrumbs;
        }));
    }

    public function slotContent() {
        $this->params['request'] = \App::request();

        return $this->render('product-category/page-leaf', $this->params);
    }

    public function slotUserbarContent() {
        $category = $this->getParam('category');
        $productFilter = $this->getParam('productFilter');

        return $this->render('product-category/_userbarContent', [
            'category'  => $category instanceof \Model\Product\Category\Entity ? $category : null,
            'productFilter'  => $productFilter instanceof \Model\Product\Filter ? $productFilter : null,
        ]);
    }

    public function slotUserbarContentData() {
        $category = $this->getParam('category');
        $productFilter = $this->getParam('productFilter');
        return [
            'target' => $category instanceof \Model\Product\Category\Entity && ($category->isV2() || $category->isV3()) ? '.js-listing' : '#productCatalog-filter-form',
            'filterTarget' => '#productCatalog-filter-form',
        ];
    }

    public function slotConfig() {
        $category = $this->getParam('category');
        return $this->tryRender('_config', ['config' => [
            'location' => ['listing'],
            'category' => $category instanceof \Model\Product\Category\Entity ? [
                'name' => $category->getName(),
                'ancestors' => array_map(function(\Model\Product\Category\Entity $category) {
                    return [
                        'name' => $category->getName(),
                    ];
                }, $category->getAncestor()),
            ] : [],
        ]]);
    }

    public function slotAdmitadJS() {
        if (!\App::config()->partners['admitad']['enabled']) {
            return '';
        }
        
        $category = $this->getParam('category');
        return '<div id="admitadJS" class="jsanalytics" data-vars="' . $this->json([
            'type' => 'category',
            'category' => $category instanceof \Model\Product\Category\Entity ? [
                'id' => $category->getId(),
            ] : [],
        ]) . '"></div>';
    }


    public function slotRelLink() {
        return
            parent::slotRelLink() . "\n" .
            $this->getPrevNextRelLinks();
    }

    /**
     * @return string
     */
    protected function getSort() {
        return \App::helper()->getCurrentSort();
    }
}
