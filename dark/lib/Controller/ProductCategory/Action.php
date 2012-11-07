<?php

namespace Controller\ProductCategory;

class Action {
    private static $globalCookieName = 'global';

    /**
     * @param string        $categoryPath
     * @param \Http\Request $request
     * @return \Http\RedirectResponse
     */
    public function setGlobal($categoryPath, \Http\Request $request) {
        $response = new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('product.category', array($categoryPath => $categoryPath)));

        if ($request->query->has('global')) {
            if ($request->query->get('global')) {
                $cookie = new \Http\Cookie(self::$globalCookieName, 1, strtotime('+7 days' ));
                $response->headers->setCookie($cookie);
            } else {
                $response->headers->clearCookie(self::$globalCookieName);
            }
        }

        return $response;
    }

    /**
     * @param string        $categoryPath
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
     * @param string        $categoryPath
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function count($categoryPath, \Http\Request $request) {
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

        // фильтры
        $productFilter = $this->getFilter($category, $request);

        $count = \RepositoryManager::getProduct()->countByFilter($productFilter->dump());

        return new \Http\JsonResponse(array(
            'success' => true,
            'data'    => $count,
        ));
    }

    /**
     * @param string        $categoryPath
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

        $isGlobal = $this->isGlobal();

        if ($category->isLeaf()) {
            $parent = $category->getParentId() ? $repository->getEntityById($category->getParentId()) : null;
            if (!$parent) {
                throw new \RuntimeException(sprintf('Category #%s has no parent', $category->getId()));
            }
            $category->setParent($parent);
            $repository->loadEntityBranch($category->getParent(), $isGlobal ? null : \App::user()->getRegion());

            // устанавливаем предков категории на основе предков родителя
            foreach ($category->getParent()->getAncestor() as $ancestor) {
                $category->addAncestor($ancestor);
            }
            $category->addAncestor($parent);

            // у дочек родителя категория содержит всю инфу
            // TODO: переделать
            foreach ($category->getParent()->getChild() as $child) {
                if ($child->getId() == $category->getId()) {
                    $category->setProductCount($child->getProductCount());
                    $category->setGlobalProductCount($child->getGlobalProductCount());
                    break;
                }
            }
        } else {
            $repository->loadEntityBranch($category, $isGlobal ? null : \App::user()->getRegion());
        }

        // если категория содержится во внешнем узле дерева
        if ($category->isLeaf()) {
            return $this->leafCategory($category, $request);
        }
        // иначе, если в запросе есть фильтрация
        else if ($request->get(\View\Product\FilterForm::$name)) {
            return $this->branchCategory($category, $request);
        }
        // иначе, если категория самого верхнего уровня
        else if ($category->isRoot()) {
            return $this->rootCategory($category, $request);
        }

        return $this->branchCategory($category, $request);
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Http\Request                  $request
     * @return \Http\Response
     * @throws \Exception
     */
    private function rootCategory(\Model\Product\Category\Entity $category, \Http\Request $request) {
        if (\App::config()->debug) \App::debug()->add('sub.act', 'rootCategory', 138);

        if (!$category->getHasChild()) {
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
     * @param \Http\Request                  $request
     * @return \Http\Response
     */
    private function branchCategory(\Model\Product\Category\Entity $category, \Http\Request $request) {
        if (\App::config()->debug) \App::debug()->add('sub.act', 'branchCategory', 138);

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
     * @param \Http\Request                  $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    private function leafCategory(\Model\Product\Category\Entity $category, \Http\Request $request) {
        if (\App::config()->debug) \App::debug()->add('sub.act', 'leafCategory', 138);

        $pageNum = (int)$request->get('page', 1);
        if ($pageNum < 1) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $pageNum));
        }

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
        // проверяем флаг глобального списка в параметрах запроса
        $isGlobal = $this->isGlobal();

        // регион для фильтров
        $region = $isGlobal ? null : \App::user()->getRegion();

        // filter values
        $values = $request->get(\View\Product\FilterForm::$name, array());
        if ($isGlobal) {
            $values['global'] = 1;
        }

        $filters = \RepositoryManager::getProductFilter()->getCollectionByCategory($category, $region);
        // проверяем есть ли в запросе фильтры
        if ((bool)$values) {
            // проверяем есть ли в запросе фильтры, которых нет в текущей категории (фильтры родительских категорий)
            /** @var $exists Ид фильтров текущей категории */
            $exists = array_map(function($filter) { /** @var $filter \Model\Product\Filter\Entity */ return $filter->getId(); }, $filters);
            /** @var $diff Ид фильтров родительских категорий */
            $diff = array_diff(array_keys($values), $exists);
            if ((bool)$diff) {
                foreach ($category->getAncestor() as $ancestor) {
                    foreach (\RepositoryManager::getProductFilter()->getCollectionByCategory($ancestor, $region) as $filter) {
                        if (false === $i = array_search($filter->getId(), $diff)) continue;

                        // скрываем фильтр в списке
                        $filter->setIsInList(false);
                        $filters[] = $filter;
                        unset($diff[$i]);
                        if (!(bool)$diff) break;
                    }
                    if (!(bool)$diff) break;
                }
            }
        }

        $productFilter = new \Model\Product\Filter($filters, $isGlobal);
        $productFilter->setCategory($category);
        $productFilter->setValues($values);

        return $productFilter;
    }

    public function isGlobal() {
        return \App::user()->getRegion()->getHasTransportCompany()
            && (bool)(\App::request()->cookies->get(self::$globalCookieName, false));
    }
}