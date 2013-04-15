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
        \App::logger()->debug('Exec ' . __METHOD__);

        $response = new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('product.category', [$categoryPath => $categoryPath]));

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
     * @return \Http\RedirectResponse
     */
    public function setInstore($categoryPath, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $response = new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('product.category', [
            'categoryPath' => $categoryPath,
            'instore'      => 1,
        ]));

        return $response;
    }

    /**
     * @param string        $categoryPath
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function slider($categoryPath, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $categoryToken = explode('/', $categoryPath);
        $categoryToken = end($categoryToken);

        $region = self::isGlobal() ? null : \App::user()->getRegion();

        $repository = \RepositoryManager::productCategory();
        $category = $repository->getEntityByToken($categoryToken);
        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара @%s не найдена.', $categoryToken));
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
        try {
            $filters = \RepositoryManager::productFilter()->getCollectionByCategory($category, $region);
        } catch (\Exception $e) {
            \App::exception()->add($e);
            \App::logger()->error($e);

            $filters = [];
        }
        $productFilter = $this->getFilter($filters, $category, $request);
        // листалка
        $limit = \App::config()->product['itemsInCategorySlider'];
        $repository = \RepositoryManager::product();
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
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $categoryToken = explode('/', $categoryPath);
        $categoryToken = end($categoryToken);

        $region = self::isGlobal() ? null : \App::user()->getRegion();

        $repository = \RepositoryManager::productCategory();
        $category = $repository->getEntityByToken($categoryToken);
        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара @%s не найдена.', $categoryToken));
        }

        // фильтры
        try {
            $filters = \RepositoryManager::productFilter()->getCollectionByCategory($category, $region);
        } catch (\Exception $e) {
            \App::exception()->add($e);
            \App::logger()->error($e);

            $filters = [];
        }
        $productFilter = $this->getFilter($filters, $category, $request);

        $count = \RepositoryManager::product()->countByFilter($productFilter->dump());

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
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        $categoryToken = explode('/', $categoryPath);
        $categoryToken = end($categoryToken);

        // подготовка 1-го пакета запросов

        // запрашиваем пользователя, если он авторизован
        /*if ($user->getToken()) {
            \RepositoryManager::user()->prepareEntityByToken($user->getToken(), function($data) {
                if ((bool)$data) {
                    \App::user()->setEntity(new \Model\User\Entity($data));
                }
            }, function (\Exception $e) {
                \App::exception()->remove($e);
                $token = \App::user()->removeToken();
                throw new \Exception\AccessDeniedException(sprintf('Время действия токена %s истекло', $token));
            });
        }*/

        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
            if ($user->getRegionId()) {
                \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                    $data = reset($data);
                    if ((bool)$data) {
                        \App::user()->setRegion(new \Model\Region\Entity($data));
                    }
                });
            }
        }

        // запрашиваем список регионов для выбора
        $regionsToSelect = [];
        \RepositoryManager::region()->prepareShowInMenuCollection(function($data) use (&$regionsToSelect) {
            foreach ($data as $item) {
                $regionsToSelect[] = new \Model\Region\Entity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute(\App::config()->coreV2['retryTimeout']['tiny']);

        /** @var $region \Model\Region\Entity|null */
        $region = self::isGlobal() ? null : \App::user()->getRegion();

        // подготовка 2-го пакета запросов

        // TODO: запрашиваем меню

        // запрашиваем категорию по токену
        /** @var $category \Model\Product\Category\Entity */
        $category = null;
        \RepositoryManager::productCategory()->prepareEntityByToken($categoryToken, $region, function($data) use (&$category) {
            $data = reset($data);
            if ((bool)$data) {
                $category = new \Model\Product\Category\Entity($data);
            }
        });

        // выполнение 2-го пакета запросов
        $client->execute(\App::config()->coreV2['retryTimeout']['tiny']);

        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара @%s не найдена.', $categoryToken));
        }

        // подготовка 3-го пакета запросов

        // запрашиваем дерево категорий
        \RepositoryManager::productCategory()->prepareEntityBranch($category, $region);

        // запрашиваем фильтры
        /** @var $filters \Model\Product\Filter\Entity[] */
        $filters = [];
        \RepositoryManager::productFilter()->prepareCollectionByCategory($category, $region, function($data) use (&$filters) {
            foreach ($data as $item) {
                $filters[] = new \Model\Product\Filter\Entity($item);
            }
        });

        // выполнение 3-го пакета запросов
        $client->execute(\App::config()->coreV2['retryTimeout']['tiny']);

        // фильтры
        $productFilter = $this->getFilter($filters, $category, $request);

        $setPageParameters = function(\View\Layout $page) use (
            &$category,
            &$regionsToSelect,
            &$productFilter
        ) {
            $page->setParam('category', $category);
            $page->setParam('regionsToSelect', $regionsToSelect);
            $page->setParam('productFilter', $productFilter);
        };

        // если категория содержится во внешнем узле дерева
        if ($category->isLeaf()) {
            $page = new \View\ProductCategory\LeafPage();
            $setPageParameters($page);

            return $this->leafCategory($category, $productFilter, $page, $request);
        }
        // иначе, если в запросе есть фильтрация
        else if ($request->get(\View\Product\FilterForm::$name)) {
            $page = new \View\ProductCategory\BranchPage();
            $setPageParameters($page);

            return $this->branchCategory($category, $productFilter, $page, $request);
        }
        // иначе, если категория самого верхнего уровня
        else if ($category->isRoot()) {
            $page = new \View\ProductCategory\RootPage();
            $setPageParameters($page);

            return $this->rootCategory($category, $productFilter, $page, $request);
        }

        $page = new \View\ProductCategory\BranchPage();
        $setPageParameters($page);

        return $this->branchCategory($category, $productFilter, $page, $request);
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Product\Filter          $productFilter
     * @param \View\Layout                   $page
     * @param \Http\Request                  $request
     * @return \Http\Response
     * @throws \Exception
     */
    private function rootCategory(\Model\Product\Category\Entity $category, \Model\Product\Filter $productFilter, \View\Layout $page, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->debug) \App::debug()->add('sub.act', 'ProductCategory\\Action.rootCategory', 138);

        if (!$category->getHasChild()) {
            throw new \Exception(sprintf('У категории "%s" отстутсвуют дочерние узлы', $category->getId()));
        }

        $page->setParam('myThingsData', array(
            'EventType' => 'MyThings.Event.Visit',
            'Action' => '1011',
	        'Category' => $category->getName(),
        ));

        return new \Http\Response($page->show());
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Product\Filter          $productFilter
     * @param \View\Layout                   $page
     * @param \Http\Request                  $request
     * @return \Http\Response
     */
    private function branchCategory(\Model\Product\Category\Entity $category, \Model\Product\Filter $productFilter, \View\Layout $page, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->debug) \App::debug()->add('sub.act', 'ProductCategory\\Action.branchCategory', 138);

        // сортировка
        $productSorting = new \Model\Product\Sorting();
        // дочерние категории сгруппированные по идентификаторам
        $childrenById = [];
        foreach ($category->getChild() as $child) {
            $childrenById[$child->getId()] = $child;
        }
        // листалки сгруппированные по идентификаторам категорий
        $limit = \App::config()->product['itemsInCategorySlider'] * 2;
        $repository = \RepositoryManager::product();
        $repository->setEntityClass('\\Model\\Product\\CompactEntity');
        // массив фильтров для каждой дочерней категории
        $filterData = array_map(function(\Model\Product\Category\Entity $category) use ($productFilter) {
            $productFilter = clone $productFilter;
            $productFilter->setCategory($category);

            return $productFilter->dump();
        }, $childrenById);
        /** @var $child \Model\Product\Category\Entity */
        $child = reset($childrenById);
        $productPagersByCategory = [];
        $productCount = 0;
        foreach ($repository->getIteratorsByFilter($filterData, $productSorting->dump(), null, $limit) as $productPager) {
            $productPager->setPage(1);
            $productPager->setMaxPerPage($limit);
            $productPagersByCategory[$child->getId()] = $productPager;
            $child = next($childrenById);
            $productCount += $productPager->count();
        }



        // video
        $productVideosByProduct = [];
        foreach ($productPagersByCategory as $productPager) {
            foreach ($productPager as $product) {
                /** @var $product \Model\Product\Entity */
                $productVideosByProduct[$product->getId()] = [];
            }
        }
        if ((bool)$productVideosByProduct) {
            \RepositoryManager::productVideo()->prepareCollectionByProductIds(array_keys($productVideosByProduct), function($data) use (&$productVideosByProduct) {
                foreach ($data as $id => $item) {
                    if (!$item) continue;
                    $productVideosByProduct[$id][] = new \Model\Product\Video\Entity($item);
                }
            });
            \App::dataStoreClient()->execute(\App::config()->dataStore['retryTimeout']['tiny'], \App::config()->dataStore['retryCount']);
        }

        $page->setParam('productPagersByCategory', $productPagersByCategory);
        $page->setParam('productVideosByProduct', $productVideosByProduct);

        $myThingsData = array(
            'EventType' => 'MyThings.Event.Visit',
            'Action' => '1011',
        );
        if ($category->isRoot()) {
            $myThingsData['Category'] = $category->getName();
        } else {
            $myThingsData['Category'] = isset($category->getAncestor()[0]) ? $category->getAncestor()[0]->getName() : null;
            $myThingsData['SubCategory'] = $category->getName();
        }
        $page->setParam('myThingsData', $myThingsData);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Product\Filter          $productFilter
     * @param \View\Layout                   $page
     * @param \Http\Request                  $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    private function leafCategory(\Model\Product\Category\Entity $category, \Model\Product\Filter $productFilter, \View\Layout $page, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->debug) \App::debug()->add('sub.act', 'ProductCategory\\Action.leafCategory', 138);

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
        // листалка
        $limit = \App::config()->product['itemsPerPage'];
        $repository = \RepositoryManager::product();
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
        if (self::isGlobal()) {
            $category->setGlobalProductCount($productPager->count());
        } else {
            $category->setProductCount($productPager->count());
        }

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

        // video
        $productVideosByProduct = [];
        foreach ($productPager as $product) {
            /** @var $product \Model\Product\Entity */
            $productVideosByProduct[$product->getId()] = [];
        }
        if ((bool)$productVideosByProduct) {
            \RepositoryManager::productVideo()->prepareCollectionByProductIds(array_keys($productVideosByProduct), function($data) use (&$productVideosByProduct) {
                foreach ($data as $id => $item) {
                    if (!$item) continue;
                    $productVideosByProduct[$id][] = new \Model\Product\Video\Entity($item);
                }
            });
            \App::dataStoreClient()->execute(\App::config()->dataStore['retryTimeout']['tiny'], \App::config()->dataStore['retryCount']);
        }

        $page->setParam('productPager', $productPager);
        $page->setParam('productSorting', $productSorting);
        $page->setParam('productView', $productView);
        $page->setParam('productVideosByProduct', $productVideosByProduct);

        $page->setParam('myThingsData', array(
            'EventType' => 'MyThings.Event.Visit',
            'Action' => '1011',
            'Category' => isset($category->getAncestor()[0]) ? $category->getAncestor()[0]->getName() : null,
            'SubCategory' => $category->getName()
        ));

        return new \Http\Response($page->show());
    }

    /**
     * @param \Model\Product\Filter\Entity[] $filters
     * @param \Model\Product\Category\Entity $category
     * @param \Http\Request                  $request
     * @return \Model\Product\Filter
     */
    private function getFilter(array $filters, \Model\Product\Category\Entity $category, \Http\Request $request) {
        // флаг глобального списка в параметрах запроса
        $isGlobal = self::isGlobal();
        //
        $inStore = self::inStore();

        // регион для фильтров
        $region = $isGlobal ? null : \App::user()->getRegion();

        // filter values
        $values = (array)$request->get(\View\Product\FilterForm::$name, []);
        if ($isGlobal) {
            $values['global'] = 1;
        }
        if ($inStore) {
            $values['instore'] = 1;
        }

        // проверяем есть ли в запросе фильтры
        if ((bool)$values) {
            // проверяем есть ли в запросе фильтры, которых нет в текущей категории (фильтры родительских категорий)
            /** @var $exists Ид фильтров текущей категории */
            $exists = array_map(function($filter) { /** @var $filter \Model\Product\Filter\Entity */ return $filter->getId(); }, $filters);
            /** @var $diff Ид фильтров родительских категорий */
            $diff = array_diff(array_keys($values), $exists);
            if ((bool)$diff) {
                foreach ($category->getAncestor() as $ancestor) {
                    try {
                        $ancestorFilters = \RepositoryManager::productFilter()->getCollectionByCategory($ancestor, $region);
                    } catch (\Exception $e) {
                        \App::exception()->add($e);
                        \App::logger()->error($e);

                        $ancestorFilters = [];
                    }
                    foreach ($ancestorFilters as $filter) {
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

        $productFilter = new \Model\Product\Filter($filters, $isGlobal, $inStore);
        $productFilter->setCategory($category);
        $productFilter->setValues($values);

        return $productFilter;
    }

    /**
     * @return bool
     */
    public static function isGlobal() {
        return \App::user()->getRegion()->getHasTransportCompany()
            && (bool)(\App::request()->cookies->get(self::$globalCookieName, false));
    }

    /**
     * @return bool
     */
    public static function inStore() {
        return (bool)\App::request()->get('instore');
    }
}