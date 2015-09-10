<?php

namespace View\Jewel\ProductCategory;

class LeafPage extends Layout {
    public function slotContent() {
        $this->params['request'] = \App::request();

        return $this->render('jewel/product-category/page-leaf', $this->params);
    }

    public function slotUserbarContent() {
        return $this->render('jewel/product-category/_userbarContent', [
            'category'  => $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null,
        ]);
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
}
