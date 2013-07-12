<?php

namespace Controller\Tag;

class Action {
    private static $globalCookieName = 'global';

    public function index($tagToken, \Http\Request $request, $categoryToken = null) {
        \App::logger()->debug('Exec ' . __METHOD__);
        $client = \App::coreClientV2();
        /** @var $region \Model\Region\Entity|null */
        $region = self::isGlobal() ? null : \App::user()->getRegion();

        $tag = \RepositoryManager::tag()->getEntityByToken($tagToken);
        if (!$tag) {
            throw new \Exception\NotFoundException(sprintf('Тег @%s не найден', $tagToken));
        }

        $pageNum = (int)$request->get('page', 1);
        if ($pageNum < 1) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s"', $pageNum));
        }

        if (!(bool)$tag->getCategory()) {
            throw new \Exception\NotFoundException(sprintf('Тег "%s" не связан ни с одной категорией', $tag->getToken()));
        }

        // категории
        /** @var $tagCategoriesById \Model\Tag\Category\Entity[] */
        $tagCategoriesById = [];
        $tagCategories = $tag->getCategory();
        foreach ($tagCategories as $tagCategory) {
            $tagCategoriesById[$tagCategory->getId()] = $tagCategory;
        }
        /** @var $categoriesByToken \Model\Product\Category\Entity[] */
        $categoriesByToken = [];
        $categories = \RepositoryManager::productCategory()->getCollectionById(array_keys($tagCategoriesById));
        foreach ($categories as $category) {
            /** @var $category \Model\Product\Category\Entity */
            $tagCategory = $tagCategoriesById[$category->getId()];
            $category->setProductCount($tagCategory->getProductCount());
            $categoriesByToken[$category->getToken()] = $category;
        }

        if ($categoryToken) {
            if (!isset($categoriesByToken[$categoryToken])) {
                throw new \Exception\NotFoundException(sprintf('Категория @%s не найдена', $categoryToken));
            }
            $category = $categoriesByToken[$categoryToken];
        } else {
            $category = reset($categoriesByToken);
        }

        // сортировка
        $productSorting = new \Model\Product\Sorting();
        list($sortingName, $sortingDirection) = array_pad(explode('-', $request->get('sort')), 2, null);
        $productSorting->setActive($sortingName, $sortingDirection);

        // вид товаров
        $productView = $request->get('view', $category->getHasLine() ? 'line' : $category->getProductView());
        // фильтры
        $filter = new \Model\Product\Filter\Entity();
        $filter->setId('tag');
        $productFilter = new \Model\Product\Filter(array($filter));
        $productFilter->setCategory($category);
        $productFilter->setValues(array('tag' => array($tag->getId())));

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

        // получаем из json данные о горячих ссылках и content
        $seoTagJson = \Model\Tag\Repository::getSeoJson($tag);
        $hotlinks = empty($seoTagJson['hotlinks']) ? [] : $seoTagJson['hotlinks'];
        // в json-файле в свойстве content содержится массив
        $seoContent = empty($seoTagJson['content']) ? '' : implode('<br>', $seoTagJson['content']);

        // на страницах пагинации сео-контент не показываем
        if ($pageNum > 1) $seoContent = '';

        $setPageParameters = function(\View\Layout $page) use (
            &$tag,
            &$productPager,
            &$productFilter,
            &$productSorting,
            &$productView,
            &$category,
            &$categoriesByToken,
            &$hotlinks,
            &$seoContent,
            &$sidebarCategoriesTree,
            &$categoriesByToken
        ) {
            $page->setParam('tag', $tag);
            $page->setParam('productPager', $productPager);
            $page->setParam('productFilter', $productFilter);
            $page->setParam('productSorting', $productSorting);
            $page->setParam('productView', $productView);
            $page->setParam('category', $category);
            $page->setParam('categories', array_values($categoriesByToken));
            $page->setParam('hotlinks', $hotlinks);
            $page->setParam('seoContent', $seoContent);
            $page->setParam('sidebarHotlinks', true);
            $page->setParam('sidebarCategoriesTree', $sidebarCategoriesTree);
            $page->setParam('categoriesByToken', $categoriesByToken);
        };

        if(empty($seoTagJson['acts_as_category'])) {
            $sidebarCategoriesTree = null;
            $page = new \View\Tag\IndexPage();
            $setPageParameters($page);
            return new \Http\Response($page->show());
        } else {
            // получаем ветки для всех найденных категорий, чтобы построить сайдбар
            // сбрасываем количество товаров, чтобы затем установить количество протэгированных товаров
            foreach ($categories as $category) {
                \RepositoryManager::productCategory()->prepareEntityBranch($category, $region);
                $client->execute(\App::config()->coreV2['retryTimeout']['tiny']);
            }

            // строим дерево категорий для сайдбара
            $sidebarCategoriesTree = [];
            $categoryProductCountsByToken = [];
            foreach ($categories as $category) {
                $ancestorList = [$category->getRoot(), $category->getParent(), $category];
                foreach ($ancestorList as $key => $ancestor) {
                    $ancestorToken = $ancestor->getToken();
                    if(!in_array($ancestorToken, $categoriesByToken)) {
                        $categoriesByToken[$ancestorToken] = $ancestor;
                    }

                    if($ancestor->isRoot() && !in_array($ancestorToken, array_keys($sidebarCategoriesTree))) {
                        $sidebarCategoriesTree[$ancestorToken] = [];
                    } elseif(!$ancestor->isRoot()) {
                        $parentToken = $ancestorList[$key - 1]->getToken();
                        $this->addToken($sidebarCategoriesTree, $parentToken, $ancestorToken);
                    }
                }

                $tagCategory = $tagCategoriesById[$category->getId()];
                $category->setProductCount($tagCategory->getProductCount());
                $categoryProductCountsByToken[$category->getToken()] = $tagCategory->getProductCount();
                $categoryProductCountsByToken[$category->getParent()->getToken()] = 
                    isset($categoryProductCountsByToken[$category->getParent()->getToken()]) ? 
                        $categoryProductCountsByToken[$category->getParent()->getToken()] + $tagCategory->getProductCount() : $tagCategory->getProductCount();
                $categoryProductCountsByToken[$category->getRoot()->getToken()] = 
                    isset($categoryProductCountsByToken[$category->getRoot()->getToken()]) ? 
                        $categoryProductCountsByToken[$category->getRoot()->getToken()] + $tagCategory->getProductCount() : $tagCategory->getProductCount();
            }

            if(empty($categoryToken) && !empty($sidebarCategoriesTree)) {
                $rootTokens = array_keys($sidebarCategoriesTree);
                $firstRootToken = $rootTokens[0];
                $category = $categoriesByToken[$firstRootToken];
            }


            // если категория содержится во внешнем узле дерева
            if ($category->isLeaf()) {
                $page = new \View\Tag\LeafPage();
                $page->setParam('categoryProductCountsByToken', $categoryProductCountsByToken);
                $setPageParameters($page);

                return $this->leafCategory($category, $productFilter, $page, $request);
            }
            // иначе, если категория самого верхнего уровня
            else if ($category->isRoot()) {
                $page = new \View\Tag\RootPage();
                $page->setParam('categoryProductCountsByToken', $categoryProductCountsByToken);
                $setPageParameters($page);

                return $this->rootCategory($category, $productFilter, $page, $request);
            }

            $page = new \View\Tag\BranchPage();
            $page->setParam('categoryProductCountsByToken', $categoryProductCountsByToken);
            $setPageParameters($page);

            return $this->branchCategory($category, $productFilter, $page, $request);
        }
    }

    /**
     * @param string        $categoryPath
     * @param \Http\Request $request
     * @return \Http\RedirectResponse
     */
    public function setGlobal($categoryPath, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $response = new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('product.category', ['categoryPath' => $categoryPath]));

        if ($request->query->has('global')) {
            if ($request->query->get('global')) {
                $cookie = new \Http\Cookie(self::$globalCookieName, 1, strtotime('+7 days' ));
                $response->headers->clearCookie(\App::config()->shop['cookieName']);
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

        $category = null;
        $repository->prepareEntityByToken($categoryToken, $region, function($data) use (&$category) {
            $category = new \Model\Product\Category\Entity(reset($data));
        });
        \App::coreClientV2()->execute();

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
        $productFilter = $this->getFilter($filters, $category, null, $request);
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

        return (new \Controller\Product\SliderAction())->execute($productPager, $productView, $request);
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

        $shop = null;
        try {
            if (!self::isGlobal() && \App::request()->get('shop') && \App::config()->shop['enabled']) {
                $shop = \RepositoryManager::shop()->getEntityById( \App::request()->get('shop') );
            }
        } catch (\Exception $e) {
            \App::logger()->error(sprintf('Не удалось отфильтровать товары по магазину #%s', \App::request()->get('shop')));
        }

        $productFilter = $this->getFilter($filters, $category, null, $request, $shop);

        $count = \RepositoryManager::product()->countByFilter($productFilter->dump());

        return new \Http\JsonResponse(array(
            'success' => true,
            'data'    => $count,
        ));
    }


    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Product\Filter          $productFilter
     * @param \View\Layout                   $page
     * @param \Http\Request                  $request
     * @return \Http\Response
     * @throws \Exception
     */
    protected function rootCategory(\Model\Product\Category\Entity $category, \Model\Product\Filter $productFilter, \View\Layout $page, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->debug) \App::debug()->add('sub.act', 'ProductCategory\\Action.rootCategory', 138);

        if (!$category->getHasChild()) {
            throw new \Exception(sprintf('У категории "%s" отстутсвуют дочерние узлы', $category->getId()));
        }

        $page->setParam('sidebarHotlinks', true);

        $catalogJson = $page->getParam('catalogJson');
        $catalogJsonBulk = [];
        if(empty($catalogJson['category_layout_type']) || (!empty($catalogJson['category_layout_type']) && $catalogJson['category_layout_type'] == 'icons')) {
            $catalogJsonBulk = \RepositoryManager::productCategory()->getCatalogJsonBulk();
        }
        $page->setParam('catalogJsonBulk', $catalogJsonBulk);

        $page->setParam('myThingsData', [
            'EventType' => 'MyThings.Event.Visit',
            'Action'    => '1011',
            'Category'  => $category->getName(),
        ]);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Product\Filter          $productFilter
     * @param \View\Layout                   $page
     * @param \Http\Request                  $request
     * @return \Http\Response
     */
    protected function branchCategory(\Model\Product\Category\Entity $category, \Model\Product\Filter $productFilter, \View\Layout $page, \Http\Request $request) {
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
            $productCount += $productPager->count();

            $child = next($childrenById);
            if (!$child) {
                break;
            }
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
                foreach ($data as $id => $items) {
                    if (!is_array($items)) continue;
                    foreach ($items as $item) {
                        if (!$item) continue;
                        $productVideosByProduct[$id][] = new \Model\Product\Video\Entity((array)$item);
                    }
                }
            });
            \App::dataStoreClient()->execute(\App::config()->dataStore['retryTimeout']['tiny'], \App::config()->dataStore['retryCount']);
        }

        $page->setParam('productPagersByCategory', $productPagersByCategory);
        $page->setParam('productVideosByProduct', $productVideosByProduct);
        $page->setParam('sidebarHotlinks', true);

        $catalogJson = $page->getParam('catalogJson');
        $catalogJsonBulk = [];
        if(!empty($catalogJson['category_layout_type']) && $catalogJson['category_layout_type'] == 'icons') {
            $catalogJsonBulk = \RepositoryManager::productCategory()->getCatalogJsonBulk();
        }
        $page->setParam('catalogJsonBulk', $catalogJsonBulk);

        $myThingsData = [
            'EventType' => 'MyThings.Event.Visit',
            'Action'    => '1011',
        ];
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
    protected function leafCategory(\Model\Product\Category\Entity $category, \Model\Product\Filter $productFilter, \View\Layout $page, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->debug) \App::debug()->add('sub.act', 'Tag\\Action.leafCategory', 138);

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

        if (\App::request()->get('shop') && \App::config()->shop['enabled']) {
            $filtersWithoutShop = [];
            foreach ($productFilter->dump() as $filter) {
                if ($filter[0] != 'shop') {
                    $filtersWithoutShop[] = $filter;
                }
            }
            $pagerAll = $repository->getIteratorByFilter(
                $filtersWithoutShop,
                $productSorting->dump(),
                ($pageNum - 1) * $limit,
                $limit
            );
            $page->setGlobalParam('allCount', $pagerAll->count());
        }

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

        // video
        $productVideosByProduct = [];
        foreach ($productPager as $product) {
            /** @var $product \Model\Product\Entity */
            $productVideosByProduct[$product->getId()] = [];
        }
        if ((bool)$productVideosByProduct) {
            \RepositoryManager::productVideo()->prepareCollectionByProductIds(array_keys($productVideosByProduct), function($data) use (&$productVideosByProduct) {
                foreach ($data as $id => $items) {
                    if (!is_array($items)) continue;
                    foreach ($items as $item) {
                        $productVideosByProduct[$id][] = new \Model\Product\Video\Entity((array)$item);
                    }
                }
            });
            \App::dataStoreClient()->execute(\App::config()->dataStore['retryTimeout']['tiny'], \App::config()->dataStore['retryCount']);
        }

        // ajax
        if ($request->isXmlHttpRequest()) {
            return new \Http\Response(\App::templating()->render('product/_list', array(
                'page'                   => new \View\Layout(),
                'pager'                  => $productPager,
                'view'                   => $productView,
                'productVideosByProduct' => $productVideosByProduct,
                'isAjax'                 => true,
            )));
        }

        $page->setParam('productPager', $productPager);
        $page->setParam('productSorting', $productSorting);
        $page->setParam('productView', $productView);
        $page->setParam('productVideosByProduct', $productVideosByProduct);
        $page->setParam('sidebarHotlinks', true);

        $page->setParam('myThingsData', [
            'EventType'   => 'MyThings.Event.Visit',
            'Action'      => '1011',
            'Category'    => isset($category->getAncestor()[0]) ? $category->getAncestor()[0]->getName() : null,
            'SubCategory' => $category->getName()
        ]);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Model\Product\Filter\Entity[] $filters
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Brand\Entity|null       $brand
     * @param \Http\Request $request
     * @return \Model\Product\Filter
     */
    protected function getFilter(array $filters, \Model\Product\Category\Entity $category, \Model\Brand\Entity $brand = null, \Http\Request $request, $shop = null) {
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
        if ($brand) {
            $values['brand'] = [
                $brand->getId(),
            ];
        }

        //если есть фильтр по магазину
        if ($shop) {
            /** @var \Model\Shop\Entity $shop */
            $values['shop'] = $shop->getId();
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
                        /** @var $ancestorFilters \Model\Product\Filter\Entity[] */
                        $ancestorFilters = [];
                        \RepositoryManager::productFilter()->prepareCollectionByCategory($ancestor, $region, function($data) use (&$ancestorFilters) {
                            foreach ($data as $item) {
                                $ancestorFilters[] = new \Model\Product\Filter\Entity($item);
                            }
                        });
                        \App::coreClientV2()->execute();
                    } catch (\Exception $e) {
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

        $productFilter = new \Model\Product\Filter($filters, $isGlobal, $inStore, $shop);
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


    /**
     * добавить дочерний токен к родительскому токену в дереве категорий для сайдбара
     */
    private function addToken(&$array, $token, $value) {
        if(in_array($token, array_keys($array)) && empty($array[$token][$value])) {
            $array[$token][$value] = [];
        } else {
            foreach ($array as $key => $subArray) {
                $this->addToken($array[$key], $token, $value);
            }
        }
    }


}