<?php

namespace View\ProductCategory;

class LeafPage extends Layout {
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

    public function slotMyThings($data) {

        $category = $this->getParam('category');

        $data = ['Action' => '1011'];
        $catDataKeys = ['Category', 'SubCategory1', 'SubCategory2'];

        if ($category instanceof \Model\Product\Category\Entity) {

            $category->addAncestor($category);

            foreach ($category->getAncestor() as $i => $cat) {
                if ($i > 2) break;
                $data[$catDataKeys[$i]] = $cat->getName();
            }

        }

        return parent::slotMyThings($data);
    }


}
