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

        $region = \App::user()->getRegion();

        $filterData = (array)$request->get('f', []);

        // фильтры
        try {
            $filters = \RepositoryManager::productFilter()->getCollectionByCategory($category, $region);
        } catch (\Exception $e) {
            \App::exception()->add($e);
            \App::logger()->error($e);

            $filters = [];
        }

        $productFilter = null;
        if ((bool)$filters) {
            $productFilter = new \Model\Product\Filter($filters);
            $productFilter->setCategory($category);
            $productFilter->setValues($filterData);
        }

        if (!$productFilter) {
            return '';
        }


        $page = new \Terminal\View\ProductCategory\FilterPage();
        $page->setParam('category', $category);
        $page->setParam('productFilter', $productFilter);

        return new \Http\Response($page->show());
    }
}
