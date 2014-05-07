<?php

namespace EnterSite\Routing\ProductCatalog;

use Enter\Routing\Route;

class GetRootCategory extends Route {
    /**
     * @param string $categoryPath
     */
    public function __construct($categoryPath) {
        $this->action = ['ProductCatalog\\RootCategory', 'execute'];
        $this->parameters = [
            'categoryPath' => $categoryPath,
        ];
    }
}