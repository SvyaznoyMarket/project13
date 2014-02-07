<?php

namespace EnterSite\Routing\ProductCatalog;

use Enter\Routing\Route;

class GetChildCategory extends Route {
    /**
     * @param string $categoryPath
     */
    public function __construct($categoryPath) {
        $this->action = ['ProductCatalog\\ChildCategory', 'execute'];
        $this->parameters = [
            'categoryPath' => $categoryPath,
        ];
    }
}