<?php

namespace Controller\ProductCategory;

class Action {
    /**
     * @param $categoryPath
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function slider($categoryPath, \Http\Request $request) {
        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $categoryToken = explode('/', $categoryPath);
        $categoryToken = end($categoryToken);

        $repository = \RepositoryManager::getProductCategory();

        $category = $repository->getEntityByToken($categoryToken);
        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара с токеном "%s" не найдена.', $categoryToken));
        }

        return new \Http\Response();
    }

    /**
     * @param $categoryPath
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function category($categoryPath, \Http\Request $request) {
        $categoryToken = explode('/', $categoryPath);
        $categoryToken = end($categoryToken);

        $repository = \RepositoryManager::getProductCategory();

        $category = $repository->getEntityByToken($categoryToken);
        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара с токеном "%s" не найдена.', $categoryToken));
        }

        // обязательно загружаем предков и детей
        $repository->loadEntityBranch($category);

        // http://en.wikipedia.org/wiki/Tree_%28data_structure%29
        if ($category->isRoot()) {
            return $this->executeRootNode($category, $request);
        } else if ($category->isBranch()) {
            return $this->executeBranchNode($category, $request);
        }

        return $this->executeLeafNode($category, $request);
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception
     */
    private function executeRootNode(\Model\Product\Category\Entity $category, \Http\Request $request) {
        if (\App::config()->debug) \App::debug()->add('subact', 'rootNode');

        if (!(bool)$category->getChild()) {
            throw new \Exception(sprintf('У категории "%s" отстутсвуют дочерние узлы', $category->getId()));
        }

        // фильтры
        $productFilter = $this->getFilter($category);

        $page = new \View\ProductCategory\RootPage();
        $page->setParam('category', $category);
        $page->setParam('productFilter', $productFilter);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Http\Request $request
     * @return \Http\Response
     */
    private function executeBranchNode(\Model\Product\Category\Entity $category, \Http\Request $request) {
        if (\App::config()->debug) \App::debug()->add('subact', 'branchNode');

        $repository = \RepositoryManager::getProduct();

        // фильтры
        $productFilter = $this->getFilter($category);
        // дочерние категории сгруппированные по идентификаторам
        $childrenById = array();
        foreach ($category->getChild() as $child) {
            $childrenById[$child->getId()] = $child;
        }
        // листалки сгруппированные по идентификаторам категорий
        $limit = \App::config()->product['itemsInCategorySlider'] * 2;
        $repository = \RepositoryManager::getProduct();
        $repository->setEntityClass('\\Model\\Product\\CompactEntity');
        // массив фильтров для каждой дочерней категории
        $filterData = array_map(function(\Model\Product\Category\Entity $category) use ($productFilter) {
            $productFilter = clone $productFilter;
            $productFilter->setCategory($category);

            return $productFilter->dump();
        }, $childrenById);
        /** @var $child \Model\Product\Category\Entity */
        $child = reset($childrenById);
        $productPagersByCategory = array();
        foreach ($repository->getIteratorsByFilter($filterData, array(), null, $limit) as $productPager) {
            $productPager->setPage(1);
            $productPager->setMaxPerPage($limit);
            $productPagersByCategory[$child->getId()] = $productPager;
            $child = next($childrenById);
        }

        $page = new \View\ProductCategory\BranchPage();
        $page->setParam('category', $category);
        $page->setParam('productFilter', $productFilter);
        $page->setParam('productPagersByCategory', $productPagersByCategory);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    private function executeLeafNode(\Model\Product\Category\Entity $category, \Http\Request $request) {
        if (\App::config()->debug) \App::debug()->add('subact', 'leafNode');

        $pageNum = (int)$request->get('page', 1);
        if ($pageNum < 1) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $pageNum));
        }

        // вид товаров
        $productView = $request->get('view', $category->getProductView());
        // фильтры
        $productFilter = $this->getFilter($category);
        // листалка
        $limit = \App::config()->product['itemsPerPage'];
        $repository = \RepositoryManager::getProduct();
        $repository->setEntityClass(
            \Model\Product\Category\Entity::PRODUCT_VIEW_EXPANDED == $productView
                ? '\\Model\\Product\\ExpandedEntity'
                : '\\Model\\Product\\CompactEntity'
        );
        $productPager = $repository->getIteratorByFilter(
            $productFilter->dump(),
            array(),
            ($pageNum - 1) * $limit,
            $limit
        );
        $productPager->setPage($pageNum);
        $productPager->setMaxPerPage($limit);
        // проверка на максимально допустимый номер страницы
        if ($productPager->getPage() > $productPager->getLastPage()) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $productPager->getPage()));
        }

        // ajax
        if ($request->isXmlHttpRequest()) {
            return new \Http\Response(\App::templating()->render('product/_list', array(
                'page'   => new \View\DefaultLayout(),
                'pager'  => $productPager,
                'view'   => $productView,
                'isAjax' => true,
            )));
        }

        $page = new \View\ProductCategory\LeafPage();
        $page->setParam('category', $category);
        $page->setParam('productFilter', $productFilter);
        $page->setParam('productPager', $productPager);
        $page->setParam('productView', $productView);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @return \Model\Product\Filter
     */
    private function getFilter(\Model\Product\Category\Entity $category) {
        $filters = \RepositoryManager::getProductFilter()->getCollectionByCategory($category);
        $productFilter = new \Model\Product\Filter($filters);
        $productFilter->setCategory($category);

        return $productFilter;
    }
}