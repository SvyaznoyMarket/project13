<?php

namespace Terminal\Controller\ProductCategory;

class FilterAction {
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

        $page = new \Terminal\View\ProductCategory\FilterPage();
        $page->setParam('category', $category);

        return new \Http\Response($page->show());
    }
}
