<?php

namespace Controller\ProductCategory;

class IndexAction {
    public function execute($categoryPath, \Http\Request $request) {
        $categoryToken = explode('/', trim($categoryPath, '/'));
        $categoryToken = end($categoryToken);

        $repository = \RepositoryManager::getProductCategory();

        $category = $repository->getEntityByToken($categoryToken);
        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара с токеном "%s" не найдена.', $categoryToken));
        }

        // обязательно загрузить предков и детей
        $repository->loadEntityBranch($category);

        // http://en.wikipedia.org/wiki/Tree_%28data_structure%29
        if ($category->isRoot()) {
            return $this->executeRootNode($category, $request);
        } else if ($category->isBranch()) {
            return $this->executeBranchNode($category, $request);
        }

        return $this->executeLeafNode($category, $request);
    }

    private function executeRootNode(\Model\Product\Category\Entity $category, \Http\Request $request) {
        if (\App::config()->debug) \App::debug()->add('subact', 'rootNode');

        $page = new \View\ProductCategory\RootPage();
        $page->setParam('category', $category);

        return new \Http\Response($page->show());
    }

    private function executeBranchNode(\Model\Product\Category\Entity $category, \Http\Request $request) {
        if (\App::config()->debug) \App::debug()->add('subact', 'branchNode');

        $page = new \View\ProductCategory\BranchPage();
        $page->setParam('category', $category);

        return new \Http\Response($page->show());
    }

    private function executeLeafNode(\Model\Product\Category\Entity $category, \Http\Request $request) {
        if (\App::config()->debug) \App::debug()->add('subact', 'leafNode');

        $page = new \View\ProductCategory\LeafPage();
        $page->setParam('category', $category);

        return new \Http\Response($page->show());
    }
}