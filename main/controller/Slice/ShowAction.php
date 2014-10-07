<?php

namespace Controller\Slice;

class ShowAction {
    private static $globalCookieName = 'global';
    protected $pageTitle;

    /**
     * @param \Http\Request $request
     * @param string        $sliceToken
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function execute(\Http\Request $request, $sliceToken) {
        return $this->category($request, $sliceToken, null);
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
     * @param \Http\Request $request
     * @param string        $sliceToken
     * @param string|null   $categoryToken
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function category(\Http\Request $request, $sliceToken, $categoryToken) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $region = \App::user()->getRegion();

        $helper = new \Helper\TemplateHelper();

        /** @var $slice \Model\Slice\Entity|null */
        $slice = null;
        try {
            \RepositoryManager::slice()->prepareEntityByToken($sliceToken, function($data) use (&$slice, $sliceToken) {
                if (is_array($data) && $data) {
                    $data['token'] = $sliceToken;
                    $slice = new \Model\Slice\Entity($data);
                }
            });
            \App::scmsSeoClient()->execute();

            if (!$slice) {
                throw new \Exception('Не получен get-slice?url=' . $sliceToken);
            }
        } catch (\Exception $e) {
            \App::exception()->remove($e);
            \App::logger()->error($e);
        }

        if (!$slice) {
            throw new \Exception\NotFoundException(sprintf('Срез @%s не найден', $sliceToken));
        }

        // добывание фильтров из среза
        $requestData = [];
        parse_str($slice->getFilterQuery(), $requestData);

        // region
        if (!empty($requestData['region'])) {
            $region = \RepositoryManager::region()->getEntityById((int)$requestData['region']);
        }

        // фильтры среза
        $filterData = $this->getSliceFilters($slice);

        // если в слайсе задан category_id, то отображаем листинг данной категории
        if ($slice->getCategoryId()) {
            $productCategoryRepository = \RepositoryManager::productCategory();
            $productCategoryRepository->setEntityClass('\Model\Product\Category\Entity');

            $categoryId = $slice->getCategoryId();
            $category = $categoryId ? $productCategoryRepository->getEntityById($categoryId) : null;

            if ($category) {
                // запрашиваем дерево категорий
                $productCategoryRepository->prepareEntityBranch($category, $region);

                $page = new \View\Slice\ShowPage();
                $page->setParam('category', $category);
                $page->setParam('slice', $slice);
                $page->setParam('seoContent', $slice->getContent());

                return $this->leafCategory($category, $page, $request, $filterData, $region, $slice);
            }
        }


        $client = \App::coreClientV2();

        // подготовка 1-го пакета запросов

        // запрашиваем список регионов для выбора
        $regionsToSelect = [];
        \RepositoryManager::region()->prepareShownInMenuCollection(function($data) use (&$regionsToSelect) {
            foreach ($data as $item) {
                $regionsToSelect[] = new \Model\Region\Entity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute(\App::config()->coreV2['retryTimeout']['tiny']);

        /** @var $region \Model\Region\Entity|null */
        if (self::isGlobal()) {
            $region = null;
        }

        // подготовка 2-го пакета запросов

        // TODO: запрашиваем меню

        /** @var $category \Model\Product\Category\Entity */
        $category = null;

        $shopScriptException = null;
        $shopScriptSeo = [];
        if ($categoryToken && \App::config()->shopScript['enabled']) {
            try {
                $shopScript = \App::shopScriptClient();
                $shopScript->addQuery(
                    'category/get-seo',
                    [
                        'slug' => $categoryToken,
                        'geo_id' => $region ? $region->getId() : \App::user()->getRegion()->getId(),
                    ],
                    [],
                    function ($data) use (&$shopScriptSeo) {
                        if ($data && is_array($data)) $shopScriptSeo = reset($data);
                    },
                    function (\Exception $e) use (&$shopScriptException) {
                        $shopScriptException = $e;
                    }
                );
                $shopScript->execute();
                if ($shopScriptException instanceof \Exception) {
                    throw $shopScriptException;
                }

                if (empty($shopScriptSeo['ui'])) {
                    throw new \Exception\NotFoundException(sprintf('Не получен ui для категории товара @%s', $categoryToken));
                }

                // запрашиваем категорию по ui
                \RepositoryManager::productCategory()->prepareEntityByUi($shopScriptSeo['ui'], $region, function($data) use (&$category) {
                    $data = reset($data);
                    if ((bool)$data) {
                        $category = new \Model\Product\Category\Entity($data);
                    }
                });
            } catch (\Exception $e) { // если не плучилось добыть seo-данные или категорию по ui, пробуем старый добрый способ
                \RepositoryManager::productCategory()->prepareEntityByToken($categoryToken, $region, function($data) use (&$category) {
                    $data = reset($data);
                    if ((bool)$data) {
                        $category = new \Model\Product\Category\Entity($data);
                    }
                });
            }

        } elseif (!is_null($categoryToken)) {
            \RepositoryManager::productCategory()->prepareEntityByToken($categoryToken, $region, function($data) use (&$category) {
                $data = reset($data);
                if ((bool)$data) {
                    $category = new \Model\Product\Category\Entity($data);
                }
            });
        } else {
            $category = new \Model\Product\Category\Entity();
        }

        // выполнение 2-го пакета запросов
        $client->execute(\App::config()->coreV2['retryTimeout']['short']);

        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара @%s не найдена', $categoryToken));
        }

        // подготовка 3-го пакета запросов

        // запрашиваем дерево категорий
        //\RepositoryManager::productCategory()->prepareEntityBranch($category, $region);
        if (!$category->getId()) {
            $category->setLevel(1);
        }

        $params = [
            'root_id'         => $category->getHasChild() ? $category->getId() : ($category->getParentId() ? $category->getParentId() : 0),
            'max_level'       => $category->getId() ? $category->getLevel() + 1 : 1,
            'is_load_parents' => true,
            'filter' => ['filters' => $filterData],
        ];

        if ($region) {
            $params['region_id'] = $region->getId();
        }
        $client->addQuery('category/tree', $params, [], function($data) use (&$category, &$region, $sliceToken, $helper) {
            /**
             * Загрузка дочерних и родительских узлов категории
             *
             * @param \Model\Product\Category\Entity $category
             * @param array $data
             * @use \Model\Region\Entity $region
             */
            $loadBranch = function(\Model\Product\Category\Entity $category, array $data) use (&$region, $sliceToken, $helper) {
                // только при загрузке дерева ядро может отдать нам количество товаров в ней
                if ($region && isset($data['product_count'])) {
                    $category->setProductCount($data['product_count']);
                }
                if (\App::config()->product['globalListEnabled'] && isset($data['product_count_global'])) {
                    $category->setGlobalProductCount($data['product_count_global']);
                }

                // добавляем дочерние узлы
                if (isset($data['children']) && is_array($data['children'])) {
                    foreach ($data['children'] as $childData) {
                        $child = new \Model\Product\Category\Entity($childData);
                        // переделываем url для дочерних категорий
                        $url = explode('/', $child->getLink());
                        $url = $helper->url('slice.category', ['sliceToken' => $sliceToken, 'categoryToken' => end($url)]);
                        $child->setLink($url);

                        $category->addChild($child);
                    }
                }

                // если категория не выбрана, выводим рутовые категории
                if (!$category->getId()) {
                    $child = new \Model\Product\Category\Entity($data);
                    // переделываем url для категорий
                    $url = explode('/', $child->getLink());
                    $url = $helper->url('slice.category', ['sliceToken' => $sliceToken, 'categoryToken' => end($url)]);
                    $child->setLink($url);

                    $category->addChild($child);
                }
            };

            /**
             * Перебор дерева категорий на данном уровне
             *
             * @param $data
             * @use $iterateLevel
             * @use $loadBranch
             * @use $category     Текущая категория каталога
             */
            $iterateLevel = function($data) use(&$iterateLevel, &$loadBranch, $category) {
                $item = reset($data);
                if (!(bool)$item) return;

                $level = (int)$item['level'];
                if ($level < $category->getLevel()) {
                    // если текущий уровень меньше уровня категории, загружаем данные для предков и прямого родителя категории
                    $ancestor = new \Model\Product\Category\Entity($item);
                    if (1 == ($category->getLevel() - $level)) {
                        $loadBranch($ancestor, $item);
                        $category->setParent($ancestor);
                    }
                    $category->addAncestor($ancestor);
                } else if ($level == $category->getLevel()) {
                    // если текущий уровень равен уровню категории, пробуем найти данные для категории
                    foreach ($data as $item) {
                        // ура, наконец-то наткнулись на текущую категорию
                        if ($item['id'] == $category->getId() || !$category->getId()) {
                            $loadBranch($category, $item);
                            if ($item['id'] == $category->getId()) {
                                return;
                            }
                        }
                    }
                }

                $item = reset($data);
                if (isset($item['children'])) {
                    $iterateLevel($item['children']);
                }
            };

            $iterateLevel($data);
        });

        // запрашиваем фильтры
        /** @var $filters \Model\Product\Filter\Entity[] */
//        $filters = [];
//        \RepositoryManager::productFilter()->prepareCollectionByCategory($category, $region, function($data) use (&$filters) {
//            foreach ($data as $item) {
//                $filters[] = new \Model\Product\Filter\Entity($item);
//            }
//        });

        // выполнение 3-го пакета запросов
        $client->execute();

        // получаем catalog json для категории (например, тип раскладки)
        //$catalogJson = \RepositoryManager::productCategory()->getCatalogJson($category);

        $promoContent = '';
        if (!empty($catalogJson['promo_token'])) {
            \App::contentClient()->addQuery(
                trim((string)$catalogJson['promo_token']),
                [],
                function($data) use (&$promoContent) {
                    if (!empty($data['content'])) {
                        $promoContent = $data['content'];
                    }
                },
                function(\Exception $e) {
                    \App::logger()->error(sprintf('Не получено содержимое для промо-страницы %s', \App::request()->getRequestUri()));
                    \App::exception()->add($e);
                }
            );
            \App::contentClient()->execute();
        }

        // если в catalogJson'e указан category_class, то обрабатываем запрос соответствующим контроллером
        $categoryClass = !empty($catalogJson['category_class']) ? strtolower(trim((string)$catalogJson['category_class'])) : null;

        // поддержка GET-запросов со старыми фильтрами
        if (!$categoryClass && is_array($request->get(\View\Product\FilterForm::$name)) && (bool)$request->get(\View\Product\FilterForm::$name)) {
            return new \Http\RedirectResponse(\App::router()->generate('product.category', ['categoryPath' => $category->getPath()]));
        }

        $shop = null;
        try {
            if (!self::isGlobal() && \App::request()->get('shop') && \App::config()->shop['enabled']) {
                $shop = \RepositoryManager::shop()->getEntityById( \App::request()->get('shop') );
            }
        } catch (\Exception $e) {
            \App::logger()->error(sprintf('Не удалось отфильтровать товары по магазину #%s', \App::request()->get('shop')));
        }

        // фильтры
//        $productFilter = $this->getFilter($filters, $category, $request, $shop);

        // получаем из json данные о горячих ссылках и content
        try {
            $seoCatalogJson = \Model\Product\Category\Repository::getSeoJson($category, null, $shopScriptSeo);
            // получаем горячие ссылки
            $hotlinks = \RepositoryManager::productCategory()->getHotlinksBySeoCatalogJson($seoCatalogJson);

            // в json-файле в свойстве content содержится массив
            $seoContent = empty($seoCatalogJson['content']) ? '' : implode('<br />', $seoCatalogJson['content']);
        } catch (\Exception $e) {
            $hotlinks = [];
            $seoContent = '';
        }

        $pageNum = (int)$request->get('page', 1);
        // на страницах пагинации сео-контент не показываем
        if ($pageNum > 1) {
            $seoContent = '';
        }
        // промо-контент не показываем на страницах пагинации, брэнда, фильтров
        if ($pageNum > 1 || (bool)((array)$request->get(\View\Product\FilterForm::$name, []))) {
            $promoContent = '';
        }

        // переделываем url для breadcrumbs
        foreach ($category->getAncestor() as $ancestor) {
            $url = explode('/', $ancestor->getLink());
            $url = $helper->url('slice.category', ['sliceToken' => $sliceToken, 'categoryToken' => end($url)]);
            $ancestor->setLink($url);
        }

        $setPageParameters = function(\View\Layout $page) use (
            &$category,
            &$regionsToSelect,
//            &$productFilter,
            &$hotlinks,
            &$seoContent,
            &$catalogJson,
            &$promoContent,
            &$shopScriptSeo,
            &$shop,
            &$slice
        ) {
            $page->setParam('category', $category);
            $page->setParam('regionsToSelect', $regionsToSelect);
//            $page->setParam('productFilter', $productFilter);
            $page->setParam('hotlinks', $hotlinks);
            $page->setParam('seoContent', $seoContent);
            $page->setParam('catalogJson', $catalogJson);
            $page->setParam('promoContent', $promoContent);
            $page->setParam('shopScriptSeo', $shopScriptSeo);
            $page->setParam('slice', $slice);
            $page->setGlobalParam('shop', $shop);
        };

        // полнотекстовый поиск через сфинкс
        $textSearched = false;
        if (\App::config()->sphinx['showListingSearchBar']) {
//            $filterValues = $productFilter->getValues();
            if(!empty($filterValues['text'])) {
                $textSearched = true;
            }
        }

        // если категория содержится во внешнем узле дерева
        /*if ($category->isLeaf() || $textSearched) {
            $page = new \View\ProductCategory\LeafPage();
            $setPageParameters($page);

            return $this->leafCategory($category, $productFilter, $page, $request);
        }
        // иначе, если в запросе есть фильтрация
        else if ($request->get(\View\Product\FilterForm::$name)) {
            $page = new \View\ProductCategory\LeafPage();
            $page->setParam('forceSliders', true);
            $setPageParameters($page);

            return $this->leafCategory($category, $productFilter, $page, $request);
        }
        // иначе, если категория самого верхнего уровня
        else if ($category->isRoot()) {
            $page = new \View\ProductCategory\RootPage();
            $setPageParameters($page);

            return $this->rootCategory($category, $productFilter, $page, $request);
        }*/

        //$page = new \View\ProductCategory\LeafPage();
        $page = new \View\Slice\ShowPage();
        $setPageParameters($page);

        return $this->leafCategory($category, /*$productFilter,*/ $page, $request, $filterData, $region, $slice);
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \View\Layout $page
     * @param \Http\Request $request
     * @param $filterData
     * @param \Model\Region\Entity $region
     * @param \Model\Slice\Entity $slice
     * @throws \Exception\NotFoundException
     * @internal param \Model\Product\Filter $productFilter
     * @return \Http\Response
     */
    protected function leafCategory(\Model\Product\Category\Entity $category, /*\Model\Product\Filter $productFilter,*/ \View\Layout $page, \Http\Request $request, $filterData, \Model\Region\Entity $region = null, \Model\Slice\Entity $slice) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->debug) \App::debug()->add('sub.act', 'ProductCategory\\Action.leafCategory', 134);

        if (!$region) {
            $region = \App::user()->getRegion();
        }

        $pageNum = (int)$request->get('page', 1);
        if ($pageNum < 1) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $pageNum));
        }

        $catalogJson = $page->getParam('catalogJson');

        // сортировка
        $productSorting = new \Model\Product\Sorting();
        list($sortingName, $sortingDirection) = array_pad(explode('-', $request->get('sort')), 2, null);
        $productSorting->setActive($sortingName, $sortingDirection);

        // если сортировка по умолчанию и в json заданы настройки сортировок,
        // то применяем их
        if(!empty($catalogJson['sort']) && $productSorting->isDefault()) {
            $sort = $catalogJson['sort'];
        } else {
            $sort = $productSorting->dump();
        }

        // вид товаров
        $productView = $request->get('view', $category->getHasLine() ? 'line' : $category->getProductView());
        // листалка
        $limit = \App::config()->product['itemsPerPage'];
        $repository = \RepositoryManager::product();
        $repository->setEntityClass('\\Model\\Product\\Entity');

        if (\App::request()->get('shop') && \App::config()->shop['enabled']) {
            $productIds = [];
            $productCount = 0;
            $repository->prepareIteratorByFilter(
                $filterData,
                $sort,
                ($pageNum - 1) * $limit,
                $limit,
                $region,
                function($data) use (&$productIds, &$productCount) {
                    if (isset($data['list'][0])) $productIds = $data['list'];
                    if (isset($data['count'])) $productCount = (int)$data['count'];
                }
            );
            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

            $products = [];
            if ((bool)$productIds) {
                $repository->prepareCollectionById($productIds, $region, function($data) use (&$products) {
                    foreach ($data as $item) {
                        $products[] = new \Model\Product\Entity($item);
                    }
                });
            }
            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

            $scoreData = [];
            if ((bool)$products) {
                $productUIs = [];
                foreach ($products as $product) {
                    if (!$product instanceof \Model\Product\BasicEntity) continue;
                    $productUIs[] = $product->getUi();
                }

                \RepositoryManager::review()->prepareScoreCollectionByUi($productUIs, function($data) use (&$scoreData) {
                    if (isset($data['product_scores'][0])) {
                        $scoreData = $data;
                    }
                });
            }
            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

            \RepositoryManager::review()->addScores($products, $scoreData);

            $pagerAll = new \Iterator\EntityPager($products, $productCount);
            $page->setGlobalParam('allCount', $pagerAll->count());
        }

        if (!empty($pagerAll)) {
            $productPager = $pagerAll;
        } else {
            // добавляем фильтр по категории
            if ($category->getId()) {
                $filterData[] = ["category",1,[$category->getId()]];
            }

            $productPager = null;

            $productIds = [];
            $productCount = 0;
            $repository->prepareIteratorByFilter(
                $filterData,
                $sort,
                ($pageNum - 1) * $limit,
                $limit,
                $region,
                function($data) use (&$productIds, &$productCount) {
                    if (isset($data['list'][0])) $productIds = $data['list'];
                    if (isset($data['count'])) $productCount = (int)$data['count'];
                }
            );
            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

            $products = [];
            if ((bool)$productIds) {
                $repository->prepareCollectionById($productIds, $region, function($data) use (&$products) {
                    foreach ($data as $item) {
                        $products[] = new \Model\Product\Entity($item);
                    }
                });
            }
            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

            $scoreData = [];
            if ((bool)$products) {
                $productUIs = [];
                foreach ($products as $product) {
                    if (!$product instanceof \Model\Product\BasicEntity) continue;
                    $productUIs[] = $product->getUi();
                }

                \RepositoryManager::review()->prepareScoreCollectionByUi($productUIs, function($data) use (&$scoreData) {
                    if (isset($data['product_scores'][0])) {
                        $scoreData = $data;
                    }
                });
            }
            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

            \RepositoryManager::review()->addScores($products, $scoreData);

            $productPager = new \Iterator\EntityPager($products, $productCount);
        }

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
        if ($request->isXmlHttpRequest() && 'true' == $request->get('ajax')) {
            return new \Http\JsonResponse([
                'list'           => (new \View\Product\ListAction())->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productPager,
                    $productVideosByProduct,
                    !empty($catalogJson['bannerPlaceholder']) ? $catalogJson['bannerPlaceholder'] : [],
                    $slice->getProductBuyMethod(),
                    $slice->getShowProductState()
                ),
//                'selectedFilter' => (new \View\ProductCategory\SelectedFilterAction())->execute(
//                    \App::closureTemplating()->getParam('helper'),
//                    $productFilter,
//                    \App::router()->generate('product.category', ['categoryPath' => $category->getPath()])
//                ),
                'pagination'     => (new \View\PaginationAction())->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productPager
                ),
                'sorting'        => (new \View\Product\SortingAction())->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productSorting
                ),
                'page'          => [
                    'title'     => $slice->getName()
                ],
            ]);
        }

        $page->setParam('productPager', $productPager);
        $page->setParam('productSorting', $productSorting);
        $page->setParam('productView', $productView);
        $page->setParam('productVideosByProduct', $productVideosByProduct);
        $page->setParam('sidebarHotlinks', true);
        $page->setParam('hasCategoryChildren', in_array($request->get('route'), ['slice.show', 'slice.category']));

        return new \Http\Response($page->show());
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
     * Получение фильтров среза
     * @param \Model\Slice\Entity $slice
     * @return array
     */
    public static function getSliceFilters(\Model\Slice\Entity $slice) {
        $requestData = [];
        parse_str($slice->getFilterQuery(), $requestData);

        $values = [];
        foreach ($requestData as $k => $v) {
            if ('q' === $k) {
                $values['text'] = $v;
            } elseif (0 === strpos($k, \View\Product\FilterForm::$name)) {
                $parts = array_pad(explode('-', $k), 3, null);

                if (!isset($values[$parts[1]])) {
                    $values[$parts[1]] = [];
                }
                if (('from' == $parts[2]) || ('to' == $parts[2])) {
                    $values[$parts[1]][$parts[2]] = $v;
                } else {
                    $values[$parts[1]][] = $v;
                }
            } elseif (0 === strpos($k, 'tag-')) {
                // добавляем теги в фильтр
                if (isset($values['tag'])) {
                    $values['tag'][] = $v;
                } else {
                    $values['tag'] = [$v];
                }
            }
        }

        $filterData = [];
        foreach ($values as $k => $v) {
            if ('text' === $k) {
                $filterData[] = [$k, 3, $v];
            } elseif (isset($v['from']) || isset($v['to'])) {
                $filterData[] = [$k, 2, isset($v['from']) ? $v['from'] : null, isset($v['to']) ? $v['to'] : null];
            } else {
                $filterData[] = [$k, 1, $v];
            }
        }

        return $filterData;
    }
}