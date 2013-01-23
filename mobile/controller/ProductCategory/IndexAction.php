<?php

namespace Mobile\Controller\ProductCategory;

class IndexAction {
    public function execute() {
        $categories = \RepositoryManager::productCategory()->getRootCollection();

        $page = new \Mobile\View\ProductCategory\IndexPage();
        $page->setParam('categories', $categories);

        return new \Http\Response($page->show());
    }
}