<?php

namespace Terminal\Controller\ProductCategory;

class ModelAction {
    /**
     * @param $categoryId
     * @param \Http\Request $request
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function execute($categoryId, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $category = \RepositoryManager::productCategory()->getEntityById($categoryId);
        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория #% не найдена', $category->getId()));
        }

        $productSorting = new \Model\Product\TerminalSorting();

        $page = new \Terminal\View\ProductCategory\ModelPage();
        $page->setParam('category', $category);
        $page->setParam('productSorting', $productSorting);

        return new \Http\Response($page->show());
    }
}
