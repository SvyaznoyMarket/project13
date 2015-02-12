<?php

namespace Controller\ProductCategory;

use Model\Product\Filter\Entity;
use View\Product\FilterForm;

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
        $regionConfig = [];
        if ($user->getRegionId()) {
            \App::dataStoreClient()->addQuery("region/{$user->getRegionId()}.json", [], function($data) use (&$regionConfig) {
                if((bool)$data) {
                    $regionConfig = $data;
                }
            });

            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
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

        $regionEntity = \App::user()->getRegion();
        if ($regionEntity instanceof \Model\Region\Entity) {
            if (array_key_exists('reserve_as_buy', $regionConfig)) {
                $regionEntity->setForceDefaultBuy(false == $regionConfig['reserve_as_buy']);
            }
            \App::user()->setRegion($regionEntity);
        }

        /** @var $region \Model\Region\Entity|null */
        $region = self::isGlobal() ? null : \App::user()->getRegion();

        // подготовка 2-го пакета запросов

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

        /** @var $category \Model\Product\Category\Entity|null */
        $category = null;
        $catalogJson = [];
        \RepositoryManager::productCategory()->prepareEntityByToken($categoryToken, $region, function($data) use (&$category, &$catalogJson) {
            if ($data) {
                $category = new \Model\Product\Category\Entity($data);
                $catalogJson = $category->catalogJson;
            }
        }, $brandToken);

        // выполнение 2-го пакета запросов
        $client->execute(\App::config()->coreV2['retryTimeout']['short']);

        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара @%s не найдена', $categoryToken));
        }

        // SITE-3381
        if (!$request->isXmlHttpRequest() && ($category->getLevel() > 1) && false === strpos($categoryPath, '/')) {
            throw new \Exception\NotFoundException(sprintf('Не передана родительская категория для категории @%s', $categoryToken));
        }

        // подготовка 3-го пакета запросов

        \RepositoryManager::productCategory()->prepareEntityHasChildren($category);

        $client->execute();

        // запрашиваем дерево категорий
        if ($category->isV2Root()) {
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

            // Необходимо запросить сестринские категории, т.к. они используется в гридстере (/main/template/product-category/__sibling-list.php) и в ювелирке (/main/template/jewel/product-category/_branch.php)
            \RepositoryManager::productCategory()->prepareEntityBranch($category->getHasChild() ? $category->getId() : $category->getParentId(), $category, $region, $filterParams);
        } else {
            // Необходимо запросить сестринские категории, т.к. они используется в гридстере (/main/template/product-category/__sibling-list.php) и в ювелирке (/main/template/jewel/product-category/_branch.php)
            \RepositoryManager::productCategory()->prepareEntityBranch($category->getHasChild() ? $category->getId() : $category->getParentId(), $category, $region);
        }

        // запрашиваем фильтры
        /** @var $filters \Model\Product\Filter\Entity[] */
        $filters = [];
        \RepositoryManager::productFilter()->prepareCollectionByCategory($category, $region, [], function($data) use (&$filters) {
            foreach ($data as $item) {
                $filters[] = new \Model\Product\Filter\Entity($item);
            }
        });

        $client->execute();

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

        if ($categoryClass && ('default' !== $categoryClass)) {
            if ('jewel' == $categoryClass) {
                if (\App::config()->debug) \App::debug()->add('sub.act', 'Jewel\\ProductCategory\\Action.categoryDirect', 134);

                return (new \Controller\Jewel\ProductCategory\Action())->categoryDirect($filters, $category, $brand, $request, $regionsToSelect, $catalogJson, $promoContent);
            } else if ('grid' == $categoryClass) {
                if (\App::config()->debug) \App::debug()->add('sub.act', 'ProductCategory\Grid\ChildAction.executeByEntity', 134);

                return (new \Controller\ProductCategory\Grid\ChildAction())->executeByEntity($request, $category, $catalogJson);
            }

            \App::logger()->error(sprintf('Контроллер для категории @%s класса %s не найден или не активирован', $category->getToken(), $categoryClass));
        }

        $relatedCategories = [];
        $categoryConfigById = [];
        if (!empty($catalogJson['related_categories']) && is_array($catalogJson['related_categories'])) {
            foreach ((array)$catalogJson['related_categories'] as $relatedCategoryItem) {
                if (is_scalar($relatedCategoryItem)) {
                    $categoryConfigById[(int)$relatedCategoryItem] = [
                        'id' => $relatedCategoryItem,
                    ];
                } else if (is_array($relatedCategoryItem) && !empty($relatedCategoryItem['id'])) {
                    $categoryConfigById[(int)$relatedCategoryItem['id']] = array_merge([
                        'id'    => null,
                        'image' => null,
                        'name'  => null,
                        'css'   => [],
                    ], $relatedCategoryItem);
                }
            }

            if ($categoryConfigById) {
                \RepositoryManager::productCategory()->prepareCollectionById(
                    array_keys($categoryConfigById),
                    $region,
                    function($data) use (&$relatedCategories) {
                        if (is_array($data)) {
                            foreach ($data as $item) {
                                if ($item) {
                                    $relatedCategories[] = new \Model\Product\Category\Entity($item);
                                }
                            }
                        }
                    }
                );

                \App::scmsClient()->execute();
            }
        }


        $shop = null;
        try {
            if (!self::isGlobal() && \App::request()->get('shop') && \App::config()->shop['enabled']) {
                $shop = \RepositoryManager::shop()->getEntityById( \App::request()->get('shop') );
            }
        } catch (\Exception $e) {
            \App::logger()->error(sprintf('Не удалось отфильтровать товары по магазину #%s', \App::request()->get('shop')));
        }

        // TODO SITE-2403 Вернуть фильтр instore
        if ($category->getIsFurniture()/* && 14974 === $user->getRegion()->getId()*/) {
            $this->createInStoreFilter($filters);
        }

        if ($category->isV2()) {
            $this->transformFiltersV2($filters);
        }

        $this->correctFiltersForJewel($filters, $category);

        // фильтры
        $productFilter = $this->getFilter($filters, $category, $brand, $request, $shop);

        $this->correctProductFilterAndCategoryForJewel($category, $productFilter);

        if (!$category->isV2()) {
            // SITE-4734
            foreach ($productFilter->getFilterCollection() as $filter) {
                if ('brand' === $filter->getId()) {
                    foreach ($filter->getOption() as $option) {
                        $option->setImageUrl('');
                    }

                    break;
                }
            }
        }

        // получаем из json данные о горячих ссылках и content
        $hotlinks = [];
        $seoContent = '';
        try {
            $seoContent = $category->getSeoContent();
            if ($category) {
                $hotlinks = $category->getSeoHotlinks();
            }
        } catch (\Exception $e) {
            \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['controller']);
        }

        // SITE-4439
        try {
            // если у категории нет дочерних узлов
            if ($category && (!$category->getHasChild() || in_array($category->getId(), [1096]))) {
//                $hotlinks = array_filter($hotlinks, function(\Model\Seo\Hotlink\Entity $item) { return (bool)$item->getGroupName(); }); // TODO: временная заглушка
                // опции брендов
                $brandOptions = [];
                foreach ($filters as $filter) {
                    if ('brand' == $filter->getId()) {
                        foreach ($filter->getOption() as $option) {
                            $brandOptions[] = $option;
                        }

                        break;
                    }
                }
                // сортировка брендов по наибольшему количеству товаров
                usort($brandOptions, function(\Model\Product\Filter\Option\Entity $a, \Model\Product\Filter\Option\Entity $b) { return $b->getQuantity() - $a->getQuantity(); });
                $brandOptions = array_slice($brandOptions, 0, 60);
                /** @var \Model\Brand\Entity[] $brands */
                $brands = [];
                if ((bool)$brandOptions) {
                    \RepositoryManager::brand()->prepareByIds(
                        array_map(function(\Model\Product\Filter\Option\Entity $option) { return $option->getId(); }, $brandOptions),
                        null,
                        function($data) use (&$brands) {
                            if (isset($data[0])) {
                                foreach ($data as $item) {
                                    if (empty($item['token'])) continue;

                                    $brands[] = new \Model\Brand\Entity($item);
                                }
                            }
                        },
                        function(\Exception $e) { \App::exception()->remove($e); }
                    );

                    \App::coreClientV2()->execute();

                    foreach ($brands as $iBrand) {
                        $hotlinks[] = new \Model\Seo\Hotlink\Entity([
                            'group' => '',
                            'name' => $iBrand->getName(),
                            'url' => \App::router()->generate('product.category.brand', [
                                'categoryPath' => $categoryPath,
                                'brandToken'   => $iBrand->getToken(),
                            ]),
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['hotlinks']);
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

        // promo slider
        $slideData = null;
        if (array_key_exists('promo_slider', $catalogJson)) {
            $show = isset($catalogJson['promo_slider']['show']) ? (bool)$catalogJson['promo_slider']['show'] : false;
            $promoCategoryToken = isset($catalogJson['promo_slider']['promo_token']) ? trim($catalogJson['promo_slider']['promo_token']) : null;

            if ($show && !empty($promoCategoryToken)) {
                try {
                    $promoRepository = \RepositoryManager::promo();
                    /** @var $promo \Model\Promo\Entity */
                    $promo = null;

                    $promoRepository->prepareEntityByToken($promoCategoryToken, function($data) use (&$promo, &$promoCategoryToken) {
                        if (is_array($data)) {
                            $data['token'] = $promoCategoryToken;
                            $promo = new \Model\Promo\Entity($data);
                        }
                    });
                    $client->execute();

                    if (!$promo) {
                        throw new \Exception\NotFoundException(sprintf('Промо-каталог @%s', $promoCategoryToken));
                    }

                    $products = [];
                    $productsIds = [];
                    // перевариваем данные изображений
                    // используя айдишники товаров из секции image.products, получим мини-карточки товаров
                    foreach ($promo->getImage() as $image) {
                        $productsIds = array_merge($productsIds, $image->getProducts());
                    }
                    $productsIds = array_unique($productsIds);
                    if (count($productsIds) > 0) {
                        \RepositoryManager::product()->prepareCollectionById($productsIds, $region, function ($data) use (&$products) {
                            foreach ($data as $item) {
                                if (!isset($item['id'])) continue;
                                $products[ $item['id'] ] = new \Model\Product\Entity($item);
                            }
                        });
                        $client->execute(\App::config()->coreV2['retryTimeout']['short']);
                    }

                    // перевариваем данные изображений для слайдера в $slideData
                    foreach ($promo->getImage() as $image) {
                        if (!$image instanceof \Model\Promo\Image\Entity) continue;

                        $itemProducts = [];
                        foreach($image->getProducts() as $productId) {
                            if (!isset($products[$productId])) continue;
                            $product = $products[$productId];
                            /** @var $product \Model\Product\Entity */
                            $itemProducts[] = [
                                'image'     => $product->getImageUrl(2), // 163х163 seize
                                'link'      => $product->getLink(),
                                'name'      => $product->getName(),
                                'price'     => $product->getPrice(),
                                'isBuyable' => ($product->getIsBuyable() || $product->isInShopOnly() || $product->isInShopStockOnly()),
                                'statusId'      => $product->getStatusId(),
                                'cartButton'    => (new \View\Cart\ProductButtonAction())->execute(new \Helper\TemplateHelper(), $product)
                            ];
                        }

                        $slideData[] = [
                            'imgUrl'  => \App::config()->dataStore['url'] . 'promo/' . $promo->getToken() . '/' . trim($image->getUrl(), '/'),
                            'title'   => $image->getName(),
                            'linkUrl' => $image->getLink()?($image->getLink().'?from='.$promo->getToken()):'',
                            'time'    => $image->getTime() ? $image->getTime() : 3000,
                            'products'=> $itemProducts,
                            // Пока не нужно, но в будущем, возможно понадобится делать $repositoryPromo->setEntityImageLink() как в /main/controller/Promo/IndexAction.php
                        ];
                    }
                } catch (\Exception $e) {
                    \App::exception()->remove($e);
                    \App::logger()->error($e);
                }
            }
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
            &$shop,
            &$relatedCategories,
            &$categoryConfigById,
            &$categoryPath,
            &$slideData
        ) {
            $page->setParam('category', $category);
            $page->setParam('regionsToSelect', $regionsToSelect);
            $page->setParam('productFilter', $productFilter);
            $page->setParam('brand', $brand);
            $page->setParam('hotlinks', $hotlinks);
            $page->setParam('seoContent', $seoContent);
            $page->setParam('catalogJson', $catalogJson);
            $page->setParam('promoContent', $promoContent);
            $page->setGlobalParam('shop', $shop);
            $page->setParam('searchHints', $this->getSearchHints($catalogJson));
            $page->setParam('relatedCategories', $relatedCategories);
            $page->setParam('categoryConfigById', $categoryConfigById);
            $page->setParam('viewParams', [
                'showSideBanner' => \Controller\ProductCategory\Action::checkAdFoxBground($catalogJson)
            ]);
            $page->setParam('categoryPath', $categoryPath);
            $page->setGlobalParam('slideData', $slideData);
            $page->setGlobalParam('isTchibo', ($category->getRoot() && 'Tchibo' === $category->getRoot()->getName()));
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

            return $this->rootCategory($category, $productFilter, $page, $request, $categoryConfigById);
        }

        $page = new \View\ProductCategory\LeafPage();
        $setPageParameters($page);

        return $this->leafCategory($category, $productFilter, $page, $request);
    }

    /**
     * @param \Model\Product\Filter\Entity[] $filters
     */
    private function createInStoreFilter(array &$filters) {
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
        if (\App::config()->region['defaultId'] === \App::user()->getRegion()->getId()) {
            // Для Москвы, SITE-2850
            //$option->setName('Товар за три дня');
            $option->setName('Товар со склада'); // SITE-3131
        } else {
            // Для регионов (привозит быстрее, но не за три дня)
            $option->setName('Товар со склада');
        }

        $labelFilter->unshiftOption($option);

        // добавляем фильтр в массив фильтров
        if (null !== $labelFilterKey) {
            $filters[$labelFilterKey] = $labelFilter;
        } else {
            array_unshift($filters, $labelFilter);
        }
    }

    /**
     * @param \Model\Product\Filter\Entity[] $filters
     */
    private function transformFiltersV2(array &$filters) {
        $newProperties = [];

        foreach ($filters as $key => $property) {
            if ($property->isLabel()) {
                $property->setName('Скидки');

                foreach ($property->getOption() as $option) {
                    if ('instore' === $option->getToken()) {
                        $labelProperty = new \Model\Product\Filter\Entity();
                        $labelProperty->setId($option->getToken());
                        $labelProperty->setName($option->getName());
                        $labelProperty->setTypeId(\Model\Product\Filter\Entity::TYPE_LIST);

                        $option->setName('да');
                        $labelProperty->addOption($option);

                        $newProperties[] = $labelProperty;

                        $property->deleteOption($option);

                        break;
                    }
                }
            } else if ($property->isShop()) {
                $property->setName('В магазине');
                foreach ($property->getOption() as $option) {
                    if (!$option->getQuantity()) {
                        $property->deleteOption($option);
                    }
                }

                if (!$property->getOption()) {
                    unset($filters[$key]);
                }
            } else if ($property->isBrand()) {
                $this->sortOptionsByQuantity($property);
            }
        }

        foreach ($newProperties as $property) {
            array_push($filters, $property);
        }
    }

    private function sortOptionsByQuantity(\Model\Product\Filter\Entity $property) {
        $options = $property->getOption();

        usort($options, function(\Model\Product\Filter\Option\Entity $a, \Model\Product\Filter\Option\Entity $b) {
            if ($a->getQuantity() == $b->getQuantity()) {
                return 0;
            }

            return ($a->getQuantity() > $b->getQuantity()) ? -1 : 1;
        });

        $property->setOption($options);
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Product\Filter $productFilter
     * @param \View\Layout $page
     * @param \Http\Request $request
     * @param array $categoryConfigById
     * @throws \Exception
     * @return \Http\Response
     */
    protected function rootCategory(\Model\Product\Category\Entity $category, \Model\Product\Filter $productFilter, \View\Layout $page, \Http\Request $request, array $categoryConfigById = []) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->debug) \App::debug()->add('sub.act', 'ProductCategory\\Action.rootCategory', 134);

        if (!$category->getHasChild()) {
            throw new \Exception(sprintf('У категории "%s" отстутсвуют дочерние узлы', $category->getId()));
        }

        if ($category->isV2Root() && $request->isXmlHttpRequest()) {
            $data = [
                'links' => $this->getRootCategoryLinks($category, $page),
                'category' => ['name' => $category->getName()],
            ];

            return new \Http\JsonResponse($data);
        }

        $page->setParam('links', $this->getRootCategoryLinks($category, $page, $categoryConfigById));
        return new \Http\Response($page->show());
    }

    private function getRootCategoryLinks(\Model\Product\Category\Entity $category, \View\Layout $page, array $categoryConfigById = []) {
        $category_class = !empty($catalogJson['category_class']) ? strtolower(trim((string)$catalogJson['category_class'])) : null;
        $relatedCategories = $page->getParam('relatedCategories');

        /** @var $categories \Model\Product\Category\Entity[] */
        $categories = $category->getChild();
        if (!empty($relatedCategories)) {
            $categories = array_merge($categories, $relatedCategories);
        }

        $links = [];
        foreach ($categories as $child) {
            $config = isset($categoryConfigById[$child->getId()]) ? $categoryConfigById[$child->getId()] : null;
            $productCount = $child->getProductCount() ? : $child->getGlobalProductCount();
            $totalText = '';

            if ( $productCount > 0 ) {
                $totalText = $productCount . ' ' . ($child->getHasLine()
                        ? $page->helper->numberChoice($productCount, array('серия', 'серии', 'серий'))
                        : $page->helper->numberChoice($productCount, array('товар', 'товара', 'товаров'))
                    );
            }

            $linkUrl = $child->getLink();
            $linkUrl .= \App::request()->getQueryString() ? (strpos('?', $linkUrl) === false ? '?' : '&') . \App::request()->getQueryString() : '';
            $linkUrl .= \App::request()->get('instore') ? (strpos('?', $linkUrl) === false ? '?' : '&') . 'instore=1' : '';

            $links[] = [
                'name'          => isset($config['name']) ? $config['name'] : $child->getName(),
                'url'           => $linkUrl,
                'image'         => (!empty($config['image'])) ? $config['image'] : $child->getImageUrl('furniture' === $category_class ? 3 : 0),
                'css'           => isset($config['css']) ? $config['css'] : null,
                'totalText'     => $totalText,
            ];
        }

        return $links;
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

        if (\App::config()->debug) \App::debug()->add('sub.act', 'ProductCategory\\Action.leafCategory', 134);

        $region = \App::user()->getRegion();

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
        $itemsPerPage = \App::config()->product['itemsPerPage'];
        $limit = $itemsPerPage;
        $offset = ($pageNum - 1) * $limit;

        // стиль листинга
        $listingStyle = isset($catalogJson['listing_style']) ? $catalogJson['listing_style'] : null;

        $hasBanner = 'jewel' !== $listingStyle ? true : false;
        if ($hasBanner) {
            // уменшаем кол-во товаров на первой странице для вывода баннера
            $offset = $offset - (1 === $pageNum ? 0 : 1);
            $limit = $limit - (1 === $pageNum ? 1 : 0);
        }

        $repository = \RepositoryManager::product();
        $repository->setEntityClass('\\Model\\Product\\Entity');

        if (\App::request()->get('shop') && \App::config()->shop['enabled']) {
            $productIds = [];
            $productCount = 0;
            $repository->prepareIteratorByFilter(
                $productFilter->dump(),
                $sort,
                $offset,
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

        $smartChoiceEnabled = isset($catalogJson['smartchoice']) ? $catalogJson['smartchoice'] : false;
        $smartChoiceData = [];

        if ($smartChoiceEnabled) {
            try {
                $smartChoiceFilters = $filters;
                if (!in_array('is_store', array_map(function($var){return $var[0];}, $smartChoiceFilters))) {
                    $smartChoiceFilters[] = ["is_store",1,1];
                }

                $smartChoiceData = \App::coreClientV2()->query('listing/smart-choice', ['region_id' => $region->getId(), 'client_id' => 'site', 'filter' => ['filters' => $smartChoiceFilters]]);

                // SITE-4715
                $smartChoiceData = array_filter($smartChoiceData, function($a) {
                    return isset($a['products']);
                });

                $smartChoiceProductsIds = array_map(function ($a) {
                    return $a['products'][0]['id'];
                }, $smartChoiceData);
                $repository->prepareCollectionById($smartChoiceProductsIds, $region, function ($data) use (&$smartChoiceProducts, &$smartChoiceData) {
                    try {
                        if (count($data) === 3) {
                            foreach ($data as $item) {
                                $smartChoiceProduct = new \Model\Product\Entity($item);
                                array_walk($smartChoiceData, function (&$item, $key, $smartChoiceProduct) {
                                    if ($item['products'][0]['id'] == $smartChoiceProduct->getId()) $item['product'] = $smartChoiceProduct;
                                }, $smartChoiceProduct);
                            }
                        } else {
                            throw new \Exception('[Smartchoice] Не получены товары из базы');
                        }
                    } catch (\Exception $e) {
                        $smartChoiceData = [];
                    }
                });
            } catch (\Exception $e) {
                $smartChoiceData = [];
            }
        }

        if (!empty($pagerAll)) {
            $productPager = $pagerAll;
        } else {
            $productPager = null;

            $productIds = [];
            $productCount = 0;
            $repository->prepareIteratorByFilter(
                $filters,
                $sort,
                $offset,
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
                    if (is_array($data)) {
                        foreach ($data as $item) {
                            $products[] = new \Model\Product\Entity($item);
                        }
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

            $repository->prepareProductsMedias($products);

            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

            \RepositoryManager::review()->addScores($products, $scoreData);

            $productPager = new \Iterator\EntityPager($products, $productCount);
        }

        // Если товаров слишком мало (меньше 3 строк в листинге), то не показываем SmartChoice
        if ($productPager->count() < 7) $smartChoiceData = [];

        if ($hasBanner) {
            $productPager->setCount($productPager->count() + 1);
        }
        $productPager->setPage($pageNum);
        $productPager->setMaxPerPage($itemsPerPage);
        if (self::isGlobal()) {
            $category->setGlobalProductCount($productPager->count());
        } else {
            $category->setProductCount($productPager->count());
        }

        // проверка на максимально допустимый номер страницы
        if ((1 != $productPager->getPage()) && (($productPager->getPage() - $productPager->getLastPage()) > 0)) {
            //throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $productPager->getPage()));
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([
                'page' => $productPager->getLastPage(),
            ]));
        }

        $columnCount = (bool)array_intersect(array_map(function(\Model\Product\Category\Entity $category) { return $category->getId(); }, $category->getAncestor()), [1320, 4649]) ? 3 : 4;

        // ajax
        if ($request->isXmlHttpRequest() && 'true' == $request->get('ajax')) {
            $selectedFilter = $category->isV2() ? new \View\Partial\ProductCategory\V2\SelectedFilter() : new \View\ProductCategory\SelectedFilterAction();
            $data = [
                'list'           => (new \View\Product\ListAction())->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productPager,
                    !empty($catalogJson['bannerPlaceholder']) && $hasBanner ? $catalogJson['bannerPlaceholder'] : [],
                    null,
                    true,
                    $columnCount,
                    $productView
                ),
                'selectedFilter' => $selectedFilter->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productFilter
                ),
                'pagination'     => (new \View\PaginationAction())->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productPager
                ),
                'sorting'        => (new \View\Product\SortingAction())->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productSorting
                ),
                'page'           => [
                    'title'      => $this->getPageTitle()
                ],
                'countProducts'  => ($hasBanner) ? ( $productPager->count() - 1 ) : $productPager->count(),
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

        $page->setParam('smartChoiceProducts', $smartChoiceData);
        $page->setParam('productPager', $productPager);
        $page->setParam('productSorting', $productSorting);
        $page->setParam('productView', $productView);
        $page->setParam('hasBanner', $hasBanner);
        $page->setParam('columnCount', $columnCount);

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
            if (0 !== strpos($k, \View\Product\FilterForm::$name)) {
                continue;
            }

            $parts = array_pad(explode('-', $k), 3, null);

            if ('from' == $parts[2] || 'to' == $parts[2]) {
                $values[$parts[1]][$parts[2]] = $v;
            } else {
                $values[$parts[1]][] = $v;
            }
        }

        foreach ($values as $k => $v) {
            if (isset($v['from']) && isset($v['to'])) {
                if ($v['from'] > $v['to']) {
                    $values[$k]['from'] = $v['to'];
                }
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
    public function getFilter(array $filters, \Model\Product\Category\Entity $category = null, \Model\Brand\Entity $brand = null, \Http\Request $request, $shop = null) {
        // флаг глобального списка в параметрах запроса
        $isGlobal = self::isGlobal();
        //
        $inStore = self::inStore();

        // регион для фильтров
        $region = $isGlobal ? null : \App::user()->getRegion();

        // добывание фильтров из http-запроса
        $values = $this->getFilterFromUrl($request);
        $values = $this->deleteNotExistsValues($values, $filters);

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

        foreach ($productFilter->getFilterCollection() as $property) {
            if (\Model\Product\Filter\Entity::TYPE_LIST == $property->getTypeId() && !in_array($property->getId(), ['shop', 'category'])) {
                $property->setIsMultiple(true);
            } else {
                $property->setIsMultiple(false);
            }
        }

        return $productFilter;
    }

    /**
     * @param array $values
     * @param \Model\Product\Filter\Entity[] $filters
     * @return array
     */
    private function deleteNotExistsValues(array $values, array $filters) {
        // SITE-4818 Не учитывать фильтр при переходе в подкатегорию, если такового не существует
        foreach ($values as $propertyId => $propertyValues) {
            $isPropertyExistsInFilter = false;

            foreach ($filters as $property) {
                if ($property->getId() === $propertyId) {
                    $isPropertyExistsInFilter = true;
                    if ($property->getTypeId() === \Model\Product\Filter\Entity::TYPE_LIST) {
                        $optionIds = [];
                        foreach ($property->getOption() as $option) {
                            $optionIds[] = (string)$option->getId();
                        }

                        foreach ($propertyValues as $i => $value) {
                            if (!in_array((string)$value, $optionIds, true)) {
                                unset($values[$propertyId][$i]);
                            }
                        }

                        if (!count($values[$propertyId])) {
                            unset($values[$propertyId]);
                        }
                    }

                    break;
                }
            }

            if (!$isPropertyExistsInFilter) {
                unset($values[$propertyId]);
            }
        }

        return $values;
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
    private function getPageTitle() {
        return $this->pageTitle;
    }

    private function setPageTitle(\Model\Product\Category\Entity $category, \Model\Brand\Entity $brand = null) {
        $this->pageTitle = $category->getName();
        if ($brand) {
            $this->pageTitle .= ' ' . $brand->getName();
        }
    }


    /**
     * @param $catalogJson
     * @return array|null
     */
    protected function getSearchHints($catalogJson) {
        if (empty($catalogJson['search_hints'])) return null;
        $hints = $catalogJson['search_hints'];

        if (is_string($hints)) {
            $hints = [$hints];
        } else {
            if (!is_array($hints)) return null;
        }

        return $hints;
    }


    /**
     * убираем/показываем уши
     *
     * @param array $catalogJson
     * @return bool
     */
    static public function checkAdFoxBground(&$catalogJson) {
        if (isset($catalogJson['show_side_panels'])) {
            return (bool)$catalogJson['show_side_panels'];
        }
        return true;
    }

    /**
     * @param \Model\Product\Filter\Entity[] $filters
     */
    private function correctFiltersForJewel(array &$filters, \Model\Product\Category\Entity $category) {
        foreach ($filters as $key => $filter) {
            if ($filter->isPrice() && in_array($category->getUi(), [
                'd792f833-f6fa-4158-83f6-2ac657077076', // Кольца Бронницкий Ювелир
                '4caf66a4-f1c4-4b79-a6e4-1f2e6a1700cc', // Подвески Бронницкий Ювелир
                'd4bc284a-9a1f-4614-a3d0-ec690d7e1b78', // Серьги Бронницкий Ювелир
                'ae6975b8-f6e3-46b3-baba-a85305213dea', // Цепи Бронницкий Ювелир
                'cd2c06d0-a087-47c2-a043-7ca02317424a', // Танцующие бриллианты
            ], true)) {
                unset($filters[$key]);
                break;
            }
        }

        $filters = array_values($filters);
    }

    private function correctProductFilterAndCategoryForJewel(\Model\Product\Category\Entity $category, \Model\Product\Filter $productFilter) {
        if ($category->isV3()) {
            foreach ($productFilter->getFilterCollection() as $filter) {
                if ('Металл' === $filter->getName() || 'Вставка' === $filter->getName()) {
                    $filter->setIsAlwaysShow(true);
                }

                if ('Металл' === $filter->getName() && in_array($category->getUi(), [
                    'd792f833-f6fa-4158-83f6-2ac657077076', // Кольца Бронницкий Ювелир
                    '4caf66a4-f1c4-4b79-a6e4-1f2e6a1700cc', // Подвески Бронницкий Ювелир
                    'd4bc284a-9a1f-4614-a3d0-ec690d7e1b78', // Серьги Бронницкий Ювелир
                    'ae6975b8-f6e3-46b3-baba-a85305213dea', // Цепи Бронницкий Ювелир
                    'cd2c06d0-a087-47c2-a043-7ca02317424a', // Танцующие бриллианты
                ], true)) {
                    $filter->isOpenByDefault = true;
                }
            }

            if (in_array($category->getUi(), [
                '0dd8ef4e-7eb3-4281-95f3-0cf2f1d469e9', // Raganella princess
                '9cbeabe3-0a06-4368-8e16-1e617fb74d7b', // Браслеты Raganella Princess
                'c61f0526-ad96-41e6-8c83-b49b4cb06a7d', // Колье Raganella Princess
                'd2a5feac-110c-4c08-9d49-b69abf9f8861', // Серьги Raganella Princess

                'd792f833-f6fa-4158-83f6-2ac657077076', // Кольца Бронницкий Ювелир
                '4caf66a4-f1c4-4b79-a6e4-1f2e6a1700cc', // Подвески Бронницкий Ювелир
                'd4bc284a-9a1f-4614-a3d0-ec690d7e1b78', // Серьги Бронницкий Ювелир
                'ae6975b8-f6e3-46b3-baba-a85305213dea', // Цепи Бронницкий Ювелир
                'cd2c06d0-a087-47c2-a043-7ca02317424a', // Танцующие бриллианты

                '5505db94-143c-4c28-adb9-b608d39afe26', // КОЛЬЦА
                'd7b951ed-7b94-4ece-a3ae-c685cf77e0dd', // СЕРЬГИ
            ], true)) {
                $category->setProductView(3);
            } else {
                $category->setProductView(4);
            }
        } else {
            foreach ($productFilter->getFilterCollection() as $filter) {
                if ('Металл' === $filter->getName() || 'Вставка' === $filter->getName()) {
                    foreach ($filter->getOption() as $option) {
                        $option->setImageUrl('');
                    }
                }
            }
        }
    }
}