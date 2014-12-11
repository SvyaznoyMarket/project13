<?php

namespace View\Partial\ProductCategory\RootPage;

class Links {
    /**
     * @return array
     */
    public function execute(array $links, \Model\Product\Category\Entity $category) {
        return [
            'links' => $links,
            'category' => ['name' => $category->getName()],
        ];
    }
}