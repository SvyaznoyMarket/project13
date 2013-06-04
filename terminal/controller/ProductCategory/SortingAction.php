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

        $sortData = (array)$request->get('sort');
        if ((bool)$sortData) {
            foreach ($sortData as $sortItem) {
                if (!is_array($sortItem)) {
                    \App::logger()->error(sprintf('Неправильная сортировка %s', $sortItem));
                    continue;
                }
                $sortName = (string)key($sortItem);
                $sortDirection = (string)current($sortItem);
                try {
                    $productSorting->setActiveSort($sortName, $sortDirection);
                } catch (\Exception $e) {
                    \App::logger()->error($e);
                }
            }
        }

        $page = new \Terminal\View\ProductCategory\SortingPage();
        $page->setParam('category', $category);
        $page->setParam('productSorting', $productSorting);

        return new \Http\Response($page->show());
    }
}
