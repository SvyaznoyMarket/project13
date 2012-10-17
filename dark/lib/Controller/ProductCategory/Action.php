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

        $pageNum = (int)$request->get('page', 1);
        if ($pageNum < 1) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $pageNum));
        }

        // сортировка
        $productSorting = new \Model\Product\Sorting();

        // вид товаров
        $productView = $category->getHasLine() ? 'line' : 'compact';
        // фильтры
        $productFilter = $this->getFilter($category, $request);
        // листалка
        $limit = \App::config()->product['itemsInCategorySlider'];
        $repository = \RepositoryManager::getProduct();
        $repository->setEntityClass('\\Model\\Product\\CompactEntity');
        $productPager = $repository->getIteratorByFilter(
            $productFilter->dump(),
            $productSorting->dump(),
            ($pageNum - 1) * $limit,
            $limit
        );
        $productPager->setPage($pageNum);
        $productPager->setMaxPerPage($limit);
        // проверка на максимально допустимый номер страницы
        if (($productPager->getPage() - $productPager->getLastPage()) > 0) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $productPager->getPage()));
        }

        return new \Http\Response(\App::templating()->render('product/_list', array(
            'page'   => new \View\Layout(),
            'pager'  => $productPager,
            'view'   => $productView,
            'isAjax' => true,
        )));
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

        // обязательно загружаем предков и детей, чтобы установить тип узла категории (root, branch или leaf)
        $repository->loadEntityBranch($category);

        // http://en.wikipedia.org/wiki/Tree_%28data_structure%29
        if ($category->isRoot()) {
            return $this->rootCategory($category, $request);
        } else if ($category->isBranch()) {
            return $this->branchCategory($category, $request);
        }

        return $this->leafCategory($category, $request);
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception
     */
    private function rootCategory(\Model\Product\Category\Entity $category, \Http\Request $request) {
        if (\App::config()->debug) \App::debug()->add('sub.act', 'rootCategory');

        if (!(bool)$category->getChild()) {
            throw new \Exception(sprintf('У категории "%s" отстутсвуют дочерние узлы', $category->getId()));
        }

        // фильтры
        $productFilter = $this->getFilter($category, $request);

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
    private function branchCategory(\Model\Product\Category\Entity $category, \Http\Request $request) {
        if (\App::config()->debug) \App::debug()->add('sub.act', 'branchCategory');

        // сортировка
        $productSorting = new \Model\Product\Sorting();
        // фильтры
        $productFilter = $this->getFilter($category, $request);
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
        foreach ($repository->getIteratorsByFilter($filterData, $productSorting->dump(), null, $limit) as $productPager) {
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
    private function leafCategory(\Model\Product\Category\Entity $category, \Http\Request $request) {
        if (\App::config()->debug) \App::debug()->add('sub.act', 'leafCategory');

        $pageNum = (int)$request->get('page', 1);
        if ($pageNum < 1) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $pageNum));
        }

        // к сожалению, нужна также загрузка дочерних узлов родителя (для левого меню категорий - product-category/_branch)
        \RepositoryManager::getProductCategory()->loadEntityBranch($category->getParent());

        // сортировка
        $productSorting = new \Model\Product\Sorting();
        list($sortingName, $sortingDirection) = array_pad(explode('-', $request->get('sort')), 2, null);
        $productSorting->setActive($sortingName, $sortingDirection);

        // вид товаров
        $productView = $request->get('view', $category->getHasLine() ? 'line' : $category->getProductView());
        // фильтры
        $productFilter = $this->getFilter($category, $request);
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
            $productSorting->dump(),
            ($pageNum - 1) * $limit,
            $limit
        );
        $productPager->setPage($pageNum);
        $productPager->setMaxPerPage($limit);
        // проверка на максимально допустимый номер страницы
        if (($productPager->getPage() - $productPager->getLastPage()) > 0) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $productPager->getPage()));
        }

        // ajax
        if ($request->isXmlHttpRequest()) {
            return new \Http\Response(\App::templating()->render('product/_list', array(
                'page'   => new \View\Layout(),
                'pager'  => $productPager,
                'view'   => $productView,
                'isAjax' => true,
            )));
        }

        $page = new \View\ProductCategory\LeafPage();
        $page->setParam('category', $category);
        $page->setParam('productFilter', $productFilter);
        $page->setParam('productPager', $productPager);
        $page->setParam('productSorting', $productSorting);
        $page->setParam('productView', $productView);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @return \Model\Product\Filter
     */
    private function getFilter(\Model\Product\Category\Entity $category, \Http\Request $request) {
        $filters = \RepositoryManager::getProductFilter()->getCollectionByCategory($category);
        $productFilter = new \Model\Product\Filter($filters);
        $productFilter->setCategory($category);
        $productFilter->setValues($request->get(\View\Product\FilterForm::$name, array()));

        return $productFilter;
    }
}