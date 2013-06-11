<?php

namespace Terminal\Controller\ProductCategory;

class SortingAction {
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

        list($sortName, $sortDirection) = array_pad(explode('-', (string)$request->get('sort')), 2, '');
        if ($sortName && $sortDirection) try {
            $productSorting->setActiveSort($sortName, $sortDirection);
        } catch (\Exception $e) {
            \App::logger()->error($e);
        }

        $page = new \Terminal\View\ProductCategory\SortingPage();
        $page->setParam('category', $category);
        $page->setParam('productSorting', $productSorting);

        return new \Http\Response($page->show());
    }
}
