<?php

namespace Controller\ProductCategory;

use \Model\Product\Category\Entity as Category;

class IndexAction {
    public function execute($categoryPath, \Http\Request $request) {
        $categoryToken = explode('/', trim($categoryPath, '/'));
        $categoryToken = end($categoryToken);

        $category = \RepositoryManager::getProductCategory()->getEntityByToken($categoryToken);
        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара с токеном "%s" не найдена.', $categoryToken));
        }

        // http://en.wikipedia.org/wiki/Tree_%28data_structure%29
        if ($category->isRoot()) {
            return $this->executeRootNode($category, $request);
        } else if ($category->isBranch()) {
            return $this->executeBranchNode($category, $request);
        }

        return $this->executeLeafNode($category, $request);
    }

    private function executeRootNode(Category $category, \Http\Request $request) {
        $page = new \View\ProductCategory\RootPage();
        $page->setParam('category', $category);

        return new \Http\Response($page->show());
    }

    private function executeBranchNode(Category $category, \Http\Request $request) {
        $page = new \View\ProductCategory\BranchPage();
        $page->setParam('category', $category);

        return new \Http\Response($page->show());
    }

    private function executeLeafNode(Category $category, \Http\Request $request) {
        $page = new \View\ProductCategory\LeafPage();
        $page->setParam('category', $category);

        return new \Http\Response($page->show());
    }
}