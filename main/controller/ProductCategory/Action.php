<?php

namespace Controller\ProductCategory;

class Action {
    private static $globalCookieName = 'global';
    protected $pageTitle;

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

        return new \Http\JsonResponse([
            'success' => true,
            'count'   => $count,
        ]);
    }

    /**
     * @param \Http\Request $request
     * @param string        $categoryPath
     * @param string|null   $brandToken
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function category(\Http\Request $request, $categoryPath, $brandToken = null) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        $categoryToken = explode('/', $categoryPath);
        $categoryToken = end($categoryToken);

        // подготовка 1-го пакета запросов

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
        \RepositoryManager::region()->prepareShownInMenuCollection(function($data) use (&$regionsToSelect) {
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

        /** @var $category \Model\Product\Category\Entity */
        $category = null;

        $shopScriptException = null;
        $shopScriptSeo = [];
        if (\App::config()->shopScript['enabled']) {
            try {
                $shopScript = \App::shopScriptClient();
                $shopScript->addQuery(
                    'category/get-seo',
                    [
                        'slug' => $categoryToken,
                        'geo_id' => \App::user()->getRegion()->getId(),
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

                // если shopscript вернул редирект
                if (!empty($shopScriptSeo['redirect']['link'])) {
                    $redirect = $shopScriptSeo['redirect']['link'];
                    if(!preg_match('/^http/', $redirect)) {
                        $redirect = (preg_match('/^http/', \App::config()->mainHost) ? '' : 'http://') .
                            \App::config()->mainHost .
                            (preg_match('/^\//', $redirect) ? '' : '/') .
                            $redirect;
                    }
                    return new \Http\RedirectResponse($redirect);
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

        } else {
            \RepositoryManager::productCategory()->prepareEntityByToken($categoryToken, $region, function($data) use (&$category) {
                $data = reset($data);
                if ((bool)$data) {
                    $category = new \Model\Product\Category\Entity($data);
                }
            });
        }

        // запрашиваем бренд по токену
        /** @var $brand \Model\Brand\Entity */
        $brand = null;
        if ($brandToken) {
            \RepositoryManager::brand()->prepareEntityByToken($brandToken, $region, function($data) use (&$brand) {
                $data = reset($data);
                if ((bool)$data) {
                    $brand = new \Model\Brand\Entity($data);
                }
            });
        }

        // выполнение 2-го пакета запросов
        $client->execute(\App::config()->coreV2['retryTimeout']['short']);

        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара @%s не найдена', $categoryToken));
        }

        // SITE-2634
        if (!empty($shopScriptSeo['link'])) {
            $category->setLink($shopScriptSeo['link']);
        }

        // подготовка 3-го пакета запросов

        // запрашиваем дерево категорий
        \RepositoryManager::productCategory()->prepareEntityBranch($category, $region);

        // получаем фильтры из http-запроса
        $filterUrl = $this->getFilterFromUrl($request);

        // заполняем параметр filters для запроса к ядру
        $filterParams = [];
        if (!empty($filterUrl)) {
            foreach ($filterUrl as $name => $values) {
                if (isset($values['from']) || isset($values['to'])) {
                    $filterParams[] = [
                        $name,
                        2,
                        isset($values['from']) ? $values['from'] : null,
                        isset($values['to']) ? $values['to'] : null
                    ];
                } else {
                    $filterParams[] = [$name, 1, $values];
                }
            }
        }

        // запрашиваем фильтры
        /** @var $filters \Model\Product\Filter\Entity[] */
        $filters = [];
        \RepositoryManager::productFilter()->prepareCollectionByCategory($category, $region, $filterParams, function($data) use (&$filters) {
            foreach ($data as $item) {
                $filters[] = new \Model\Product\Filter\Entity($item);
            }
        });

        // выполнение 3-го пакета запросов
        $client->execute();

        // получаем catalog json для категории (например, тип раскладки)
        $catalogJson = \RepositoryManager::productCategory()->getCatalogJson($category);

        $promoContent = '';
        // если в catalogJson'e указан category_layout_type == 'promo', то подгружаем промо-контент
        if (!empty($catalogJson['category_layout_type']) &&
            $catalogJson['category_layout_type'] == 'promo' &&
            !empty($catalogJson['promo_token'])
        ) {
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

        if ($categoryClass) {
            $controller = null;
            if (('jewel' == $categoryClass) && \App::config()->productCategory['jewelController']) {
                $controller = new \Controller\Jewel\ProductCategory\Action();

                return $controller->categoryDirect($filters, $category, $brand, $request, $regionsToSelect, $catalogJson, $promoContent, $shopScriptSeo);
            }

            \App::logger()->error(sprintf('Контроллер для категории @%s класса %s не найден или не активирован', $category->getToken(), $categoryClass));
        }


        $shop = null;
        try {
            if (!self::isGlobal() && \App::request()->get('shop') && \App::config()->shop['enabled']) {
                $shop = \RepositoryManager::shop()->getEntityById( \App::request()->get('shop') );
                if (\App::user()->getRegion() && $shop && $shop->getRegion()) {
                    if ((int)\App::user()->getRegion()->getId() != (int)$shop->getRegion()->getId()) {
                        /*$route = \App::router()->generate('region.change', ['regionId' => $shop->getRegion()->getId()]);
                        $response = new \Http\RedirectResponse($route);
                        $response->headers->set('referer', \App::request()->getUri());*/
                        $controller = new \Controller\Region\Action();
                        \App::logger()->info(sprintf('Смена региона #%s на #%s', \App::user()->getRegion()->getId(), $shop->getRegion()->getId()));
                        $response = $controller->change($shop->getRegion()->getId(), \App::request(), \App::request()->getUri());
                        return $response;
                    }
                }
            }
        } catch (\Exception $e) {
            \App::logger()->error(sprintf('Не удалось отфильтровать товары по магазину #%s', \App::request()->get('shop')));
        }

        // TODO SITE-2403 Вернуть фильтр instore
        if ($category->getIsFurniture() && 14974 === $user->getRegion()->getId()) {
            $labelFilter = null;
            $labelFilterKey = null;
            foreach ($filters as $key => $filter) {
                if ('label' === $filter->getId()) {
                    $labelFilter = $filter;
                    $labelFilterKey = $key;
                }
            }

            // если нету блока фильтров "WOW-товары", то создаем
            if (null === $labelFilter) {
                $labelFilter = new \Model\Product\Filter\Entity();
                $labelFilter->setId('label');
                $labelFilter->setTypeId(\Model\Product\Filter\Entity::TYPE_LIST);
                $labelFilter->setName('WOW-товары');
                $labelFilter->getIsInList(true);
            }

            // создаем фильтр "Товар за три дня"
            $option = new \Model\Product\Filter\Option\Entity();
            $option->setId(1);
            $option->setToken('instore');
            $option->setName('Товар за три дня');
            $labelFilter->unshiftOption($option);

            // добавляем фильтр в массив фильтров
            if (null !== $labelFilterKey) {
                $filters[$labelFilterKey] = $labelFilter;
            } else {
                array_unshift($filters, $labelFilter);
            }
        }

        // фильтры
        $productFilter = $this->getFilter($filters, $category, $brand, $request, $shop);

        // получаем из json данные о горячих ссылках и content
        try {
            $seoCatalogJson = \Model\Product\Category\Repository::getSeoJson($category, null, $shopScriptSeo);
            // получаем горячие ссылки
            $hotlinks = \RepositoryManager::productCategory()->getHotlinksBySeoCatalogJson($seoCatalogJson);

            // в json-файле в свойстве content содержится массив
            if (empty($brand)) {
                $seoContent = empty($seoCatalogJson['content']) ? '' : implode('<br />', $seoCatalogJson['content']);
            } else {
                $seoBrandJson = \Model\Product\Category\Repository::getSeoJson($category, $brand);
                $seoContent = empty($seoBrandJson['content']) ? '' : implode('<br />', $seoBrandJson['content']);
            }
        } catch (\Exception $e) {
            $hotlinks = [];
            $seoContent = '';
        }

        $pageNum = (int)$request->get('page', 1);
        // на страницах пагинации сео-контент не показываем
        if ($pageNum > 1) {
            $seoContent = '';
        }

        $excludeTokens = empty($catalogJson['promo_exclude_token']) ? [] : $catalogJson['promo_exclude_token'];

        if (
            // промо-контент не показываем на страницах пагинации, брэнда, фильтров
            $pageNum > 1 || !empty($brand) || (bool)((array)$request->get(\View\Product\FilterForm::$name, [])) ||
            // ..или если категория в списке исключений
            ($excludeTokens && in_array($category->getToken(), $excludeTokens) )
        ) {
            $promoContent = '';
        }

        $setPageParameters = function(\View\Layout $page) use (
            &$category,
            &$regionsToSelect,
            &$productFilter,
            &$brand,
            &$hotlinks,
            &$seoContent,
            &$catalogJson,
            &$promoContent,
            &$shopScriptSeo,
            &$shop
        ) {
            $page->setParam('category', $category);
            $page->setParam('regionsToSelect', $regionsToSelect);
            $page->setParam('productFilter', $productFilter);
            $page->setParam('brand', $brand);
            $page->setParam('hotlinks', $hotlinks);
            $page->setParam('seoContent', $seoContent);
            $page->setParam('catalogJson', $catalogJson);
            $page->setParam('promoContent', $promoContent);
            $page->setParam('shopScriptSeo', $shopScriptSeo);
            $page->setGlobalParam('shop', $shop);
        };

        // полнотекстовый поиск через сфинкс
        $textSearched = false;
        if (\App::config()->sphinx['showListingSearchBar']) {
            $filterValues = $productFilter->getValues();
            if(!empty($filterValues['text'])) {
                $textSearched = true;
            }
        }

        // Формируем заголовок страницы (пока используется только в ajax)
        $this->setPageTitle($category, $brand);

        // если категория содержится во внешнем узле дерева
        if ($category->isLeaf() || $textSearched) {
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
        }

        $page = new \View\ProductCategory\LeafPage();
        $setPageParameters($page);

        return $this->leafCategory($category, $productFilter, $page, $request);
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
        $productVideosByProduct = [];
        $productCount = 0;

        foreach ($repository->getIteratorsByFilter($filterData, $productSorting->dump(), null, $limit) as $productPager) {
            $productPager->setPage(1);
            $productPager->setMaxPerPage($limit);
            $productPagersByCategory[$child->getId()] = $productPager;
            $productCount += $productPager->count();

            foreach ($productPager as $product) {
                /** @var $product \Model\Product\Entity */
                $productVideosByProduct[$product->getId()] = [];
            }

            $child = next($childrenById);
            if (!$child) {
                break;
            }
        }

        $productVideosByProduct =  \RepositoryManager::productVideo()->getVideosByProduct( $productVideosByProduct );

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

        if (\App::config()->debug) \App::debug()->add('sub.act', 'ProductCategory\\Action.leafCategory', 138);

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
                $sort,
                ($pageNum - 1) * $limit - (1 === $pageNum ? 0 : 1),
                1 === $pageNum ? ($limit - 1) : $limit
            );
            $page->setGlobalParam('allCount', $pagerAll->count());
        }

        $filters = $productFilter->dump();
        // TODO Костыль для таска: SITE-2403 Вернуть фильтр instore
        if (self::inStore()) {
            foreach ($filters as $filterKey => $filter) {
                if ('label' === $filter[0]) {
                    foreach ($filter[2] as $labelFilterKey => $labelFilter) {
                        if (1 === $labelFilter) {
                            unset($filters[$filterKey][2][$labelFilterKey]);
                        }
                    }
                }
            }
        }

        $productPager = $repository->getIteratorByFilter(
            $filters,
            $sort,
            ($pageNum - 1) * $limit - (1 === $pageNum ? 0 : 1),
            1 === $pageNum ? ($limit - 1) : $limit
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
        if ($request->isXmlHttpRequest() && 'true' == $request->get('ajax')) {
            $data = [
                'list'           => (new \View\Product\ListAction())->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productPager,
                    $productVideosByProduct,
                    !empty($catalogJson['bannerPlaceholder']) ? $catalogJson['bannerPlaceholder'] : []
                ),
                'selectedFilter' => (new \View\ProductCategory\SelectedFilterAction())->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productFilter,
                    \App::router()->generate('product.category', ['categoryPath' => $category->getPath()])
                ),
                'pagination'     => (new \View\PaginationAction())->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productPager
                ),
                'sorting'        => (new \View\Product\SortingAction())->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productSorting
                ),
                'page'          => [
                    'title'     => $this->getPageTitle()
                ],
            ];

            // если установлена настройка что бы показывать фасеты, то в ответ добавляем "disabledFilter"
            if (true === \App::config()->sphinx['showFacets']) {
                $data['disabledFilter'] = (new \View\ProductCategory\DisabledFilterAction())->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productFilter
                );
            }

            return new \Http\JsonResponse($data);
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
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Brand\Entity            $brand
     * @param \Model\Product\Filter          $productFilter
     * @param \View\Layout                   $page
     * @param \Http\Request                  $request
     * @return \Http\Response
     */
    public function brand(\Model\Product\Category\Entity $category, \Model\Brand\Entity $brand, \Model\Product\Filter $productFilter, \View\Layout $page, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->debug) \App::debug()->add('sub.act', 'ProductCategory\\Action.brand', 138);

        $page->setParam('brand', $brand);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Http\Request $request
     * @return array
     */
    public function getFilterFromUrl(\Http\Request $request) {
        // добывание фильтров из http-запроса
        $requestData = ('POST'== $request->getMethod()) ? $request->request : $request->query;

        $values = [];
        foreach ($requestData as $k => $v) {
            if (0 !== strpos($k, \View\Product\FilterForm::$name)) continue;
            $parts = array_pad(explode('-', $k), 3, null);

            if (!isset($values[$parts[1]])) {
                $values[$parts[1]] = [];
            }
            if (('from' == $parts[2]) || ('to' == $parts[2])) {
                $values[$parts[1]][$parts[2]] = $v;
            } else {
                $values[$parts[1]][] = $v;
            }
        }

        // filter values
        if ($request->get('scrollTo')) {
            // TODO: SITE-2218 сделать однотипные фильтры для ювелирки и неювелирки
            $values = (array)$request->get(\View\Product\FilterForm::$name, []);
        }

        return $values;
    }

    /**
     * @param \Model\Product\Filter\Entity[] $filters
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Brand\Entity|null $brand
     * @param \Http\Request $request
     * @param \Model\Shop\Entity|null $shop
     * @return \Model\Product\Filter
     */
    public function getFilter(array $filters, \Model\Product\Category\Entity $category = null, \Model\Brand\Entity &$brand = null, \Http\Request $request, $shop = null) {
        // флаг глобального списка в параметрах запроса
        $isGlobal = self::isGlobal();
        //
        $inStore = self::inStore();

        // регион для фильтров
        $region = $isGlobal ? null : \App::user()->getRegion();

        // добывание фильтров из http-запроса
        $values = $this->getFilterFromUrl($request);

        if ($isGlobal) {
            $values['global'] = 1;
        }
        if ($inStore) {
            $values['instore'] = 1;
            $values['label'][] = 1; // TODO SITE-2403 Вернуть фильтр instore
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

            // полнотекстовый поиск через сфинкс
            if (\App::config()->sphinx['showListingSearchBar']) {
                $sphinxFilter = isset($values['text']) ? $values['text'] : null;

                if ($sphinxFilter) {
                    $clientV2 = \App::coreClientV2();
                    $result = null;
                    $clientV2->addQuery('search/normalize', [], ['request' => $sphinxFilter], function ($data) use (&$result) {
                        $result = $data;
                    });
                    $clientV2->execute();

                    if(is_array($result)) {
                        $values['text'] = implode(' ', $result);
                    } else {
                        unset($values['text']);
                    }
                }

                $sphinxFilterData = [
                    'filter_id'     => 'text',
                    'type_id'       => \Model\Product\Filter\Entity::TYPE_STRING,
                ];
                $sphinxFilter = new \Model\Product\Filter\Entity($sphinxFilterData);
                array_push($filters, $sphinxFilter);
            }

            // проверяем есть ли в запросе фильтры, которых нет в текущей категории (фильтры родительских категорий)
            /** @var $exists Ид фильтров текущей категории */
            $exists = array_map(function($filter) { /** @var $filter \Model\Product\Filter\Entity */ return $filter->getId(); }, $filters);
            /** @var $diff Ид фильтров родительских категорий */
            $diff = array_diff(array_keys($values), $exists);
            if ((bool)$diff && $category) {
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
     * @return mixed
     */
    protected function getPageTitle() {
        return $this->pageTitle;
    }


    /**
     * @param $category         \Model\Product\Category\Entity|null
     * @param $brand            \Model\Brand\Entity|null
     * @param bool|string       $defaultTitle
     * @return bool
     */
    protected function setPageTitle($category, $brand, $defaultTitle = false)
    {
        if ( $category ) {
            /**@var $category \Model\Product\Category\Entity **/
            $this->pageTitle = $category->getName();
            if ( $brand ) {
                /**@var $brand \Model\Brand\Entity **/
                $this->pageTitle .= ' ' . $brand->getName();
            }
            return true;
        }

        if ( $defaultTitle ) {
            return $this->pageTitle = $defaultTitle;
        }
        return false;
    }
}