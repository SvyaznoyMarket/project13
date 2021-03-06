<?php

namespace Controller\ProductCategory;

use EnterApplication\CurlTrait;
use Model\Product\Category\Entity as Category;
use Model\Product\Filter\Entity;
use View\Partial\ProductCategory\RootPage\Brands;
use View\Product\FilterForm;

class Action {
    use CurlTrait;

    protected $pageTitle;

    /**
     * @param \Http\Request $request
     * @param string        $categoryPath
     * @param string|null   $brandToken
     * @param string|null   $page
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function category(\Http\Request $request, $categoryPath, $brandToken = null, $page = null) {
        $client = \App::coreClientV2();
        $user = \App::user();

        $categoryToken = explode('/', $categoryPath);
        $categoryToken = end($categoryToken);

        // подготовка 1-го пакета запросов

        // запрашиваем текущий регион, если есть кука региона
        if ($regionId = $user->getRegionId()) {
            if ((true === \App::config()->region['cache']) && ($regionId === \App::config()->region['defaultId'])) {
                $data = \App::dataStoreClient()->query('/region-default.json');
                $data = !empty($data['result'][0]['id']) ? $data['result'][0] : null;
                if ($data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            } else {
                \RepositoryManager::region()->prepareEntityById($regionId, function($data) {
                    $data = reset($data);
                    if ((bool)$data) {
                        \App::user()->setRegion(new \Model\Region\Entity($data));
                    }
                });
            }

            $client->execute(\App::config()->coreV2['retryTimeout']['tiny']);
        }

        /** @var $region \Model\Region\Entity */
        $region = \App::user()->getRegion();

        // подготовка 2-го пакета запросов

        /** @var $category \Model\Product\Category\Entity|null */
        $category = null;
        $catalogJson = [];

        // получаем категорию по токену из запроса (если это не фейковый токен)
        if ($categoryToken != Category::FAKE_SHOP_TOKEN) {
            \RepositoryManager::productCategory()->prepareEntityByToken($categoryToken, $region, function($data) use (&$category, &$catalogJson) {
                if ($data) {
                    $category = new Category($data);
                    $catalogJson = $category->catalogJson;
                }
            }, $brandToken);

            $client->execute(\App::config()->coreV2['retryTimeout']['short']);
        } else {
            $category = new Category();
            $category->setView(\Model\Product\Category\Entity::VIEW_COMPACT);
            $category->setName('Товары в cENTER');
            $category->setLink(\App::router()->generateUrl('product.category', ['categoryPath' => Category::FAKE_SHOP_TOKEN]));
            $category->setToken($categoryToken);
        }

        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара @%s не найдена', $categoryToken));
        }

        // SITE-3381
        if (!$request->isXmlHttpRequest() && ($category->getLevel() > 1) && false === strpos($categoryPath, '/')) {
            throw new \Exception\NotFoundException(sprintf('Не передана родительская категория для категории @%s', $categoryToken));
        }

        if (!isset($page) && $request->query->get('page')) {
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([
                'page' => (int)$request->query->get('page'),
            ]), 301);
        }

        if (isset($page) && $category->getLevel() == 1) {
            throw new \Exception\NotFoundException('У корневой категории ' . $categoryToken . ' нет страниц');
        }

        if (isset($page) && $page <= 1) {
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([], ['page'], $request->routeName), 301);
        }

        // Например, ести url = .../page-02
        if (isset($page) && (string)(int)$page !== $page) {
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([
                'page' => (int)$page,
            ]), 301);
        }

        $page = (int)$page ?: 1;

        // подготовка 3-го пакета запросов

        if (\App::config()->product['breadcrumbsEnabled']) { // TODO: надо вообще выпилить
            \RepositoryManager::productCategory()->prepareEntityHasChildren($category);
        } else {
            $category->setHasChild(in_array($category->id, [80, 443, 1, 788, 320, 923, 2545, 185, 224, 1438, 647, 4506]));
        }

        \App::scmsClient()->addQuery(
            'api/word-inflect',
            ['names' => [$category->name]],
            [],
            function($data) use (&$category) {
                $category->inflectedNames = new \Model\Inflections($data[$category->name]);
            }
        );

        $client->execute();

        /** @var \Model\Config\Entity[] $configParameters */
        $configParameters = [];
        $callbackPhrases = [];
        \RepositoryManager::config()->prepare(['site_call_phrases'], $configParameters, function(\Model\Config\Entity $entity) use (&$category, &$callbackPhrases) {
            if ('site_call_phrases' === $entity->name) {
                if (1 === $category->getLevel()) {
                    $callbackPhrases = !empty($entity->value['category']) ? $entity->value['category'] : [];
                } else {
                    $callbackPhrases = !empty($entity->value['listing']) ? $entity->value['listing'] : [];
                }
            }

            return true;
        });

        // запрашиваем дерево категорий
        if ($category->isV2Root()) {
            // Необходимо запросить сестринские категории, т.к. они используется в гридстере (/main/template/product-category/__sibling-list.php)
            \RepositoryManager::productCategory()->prepareEntityBranch($category->getHasChild() ? $category->getId() : $category->getParentId(), $category, $region, $this->convertFiltersToSearchClientRequestFormat(\RepositoryManager::productFilter()->getFilterValuesFromHttpRequest($request)));
        } else {
            // Необходимо запросить сестринские категории, т.к. они используется в гридстере (/main/template/product-category/__sibling-list.php)
            \RepositoryManager::productCategory()->prepareEntityBranch($category->getHasChild() ? $category->getId() : $category->getParentId(), $category, $region);
        }

        // запрашиваем фильтры и извлекаем из них бренды
        /** @var $filters \Model\Product\Filter\Entity[] */
        /** @var $brand \Model\Brand\Entity */
        /** @var $brands \Model\Brand\Entity[] */
        $brand = null;
        $filters = [];
        $brands = [];
        \RepositoryManager::productFilter()->prepareCollectionByCategory(
            $category,
            $region,
            [],
            function($data) use (&$filters, &$brands, &$brand, $brandToken, $categoryToken) {
                foreach ($data as $item) {
                    $filter = new \Model\Product\Filter\Entity($item);

                    if ($categoryToken == Category::FAKE_SHOP_TOKEN) {
                        if ($filter->isShop()) {
                            $filter->defaultTitle = 'Все магазины региона';
                            $filter->showDefaultTitleInSelectedList = true;
                        }

                        if ($filter->isCategory()) {
                            $filter->defaultTitle = 'Все';
                            $filter->showDefaultTitleInSelectedList = true;
                        }
                    }

                    $filters[] = $filter;
                    // бренды
                    if ($filter->isBrand() && $filter->getOption()) {
                        foreach ($filter->getOption() as $option) {
                            $filterBrand = new \Model\Brand\Entity();
                            $filterBrand->id = $option->id;
                            $filterBrand->token = $option->token;
                            $filterBrand->name = $option->name;
                            $filterBrand->image = $option->imageUrl;

                            $brands[] = $filterBrand;
                            if ($brandToken !== null && $option->getToken() == $brandToken) {
                                $brand = $filterBrand;
                            }
                        }
                    }
                }
            },
            function($e) {
                \App::logger()->error($e);
                \App::exception()->remove($e);
            }
        );

        $client->execute();

        if ($category->isAutoGrid()) {
            \RepositoryManager::productCategory()->prepareEnrichCategory($category);
        }

        if ($brandToken && !$brand) {
            return new \Http\RedirectResponse($category->getLink(), 301);
        }

        $promoContent = '';
        if (!empty($catalogJson['promo_token'])) {
            $scmsClient = \App::scmsClient();
            $scmsClient->addQuery(
                'api/static-page',
                [
                    'token' => [trim((string)$catalogJson['promo_token'])],
                    'geo_town_id' => \App::user()->getRegion()->id,
                    'tags' => ['site-web'],
                ],
                [],
                function($data) use (&$promoContent) {
                    if (!empty($data['pages'][0]['content'])) {
                        $promoContent = $data['pages'][0]['content'];
                    }
                },
                function(\Exception $e) {
                    \App::logger()->error(sprintf('Не получено содержимое для промо-страницы %s', \App::request()->getRequestUri()));
                    \App::exception()->add($e);
                }
            );

            $scmsClient->execute();
        }

        // собираем статистику для RichRelevance
        try {
            if (\App::config()->product['pushRecommendation']) {
                \App::richRelevanceClient()->query('recsForPlacements', [
                    'placements'    => 'category_page',
                    'categoryId'    => $category->getId()
                ]);
            }
        } catch (\Exception $e) {
            \App::exception()->remove($e);
        }

        if ($category->isManualGrid()) {
            \App::config()->debug && \App::debug()->add('routeSubAction', 'ProductCategory\Grid\ManualGridAction::execute', 134);
            return (new \Controller\ProductCategory\Grid\ManualGridAction())->execute($request, $category, $catalogJson);
        } else if ($category->isAutoGrid() && $category->isTchibo()) {
            \App::config()->debug && \App::debug()->add('routeSubAction', 'ProductCategory\Grid\AutoGridAction::execute', 134);
            return (new \Controller\ProductCategory\Grid\AutoGridAction())->execute($request, $category);
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
                                    $relatedCategories[] = new Category($item);
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
            if (\App::request()->get('shop') && \App::config()->shop['enabled']) {
                $shop = \RepositoryManager::shop()->getEntityById( \App::request()->get('shop') );
            }
        } catch (\Exception $e) {
            \App::logger()->error(sprintf('Не удалось отфильтровать товары по магазину #%s', \App::request()->get('shop')));
        }

        $this->createInStoreFilter($filters, $category);

        $this->transformFiltersV2($filters, $category);

        $this->correctFiltersForBronnitskiyYuvelir($filters, $category);

        // фильтры

        $productFilter = \RepositoryManager::productFilter()->createProductFilter($filters, $category, $brand, $request, $shop, function(\Model\Product\Filter\Entity $property) use($category) {
            return ($category->isV2() && $property->isBrand() && $property->getIsAlwaysShow() || $category->isV3() && in_array($property->getName(), ['Металл', 'Вставка'], true));
        });

        $this->correctProductFilterAndCategoryForJewel($category, $productFilter);

        if ($category->isV2Furniture() && \Session\AbTest\AbTest::isNewFurnitureListing()) {
            $category->setView(\Model\Product\Category\Entity::VIEW_LIGHT_WITH_BOTTOM_DESCRIPTION);
        } else if ($category->isTchibo()) {
            $category->setView(\Model\Product\Category\Entity::VIEW_LIGHT_WITH_BOTTOM_DESCRIPTION);
        } else if ($category->catalogJson['category_class'] === 'jewel') {
            $category->setView(\Model\Product\Category\Entity::VIEW_LIGHT_WITH_BOTTOM_DESCRIPTION);
        }

        $hotlinks = [];
        $seoContent = '';
        try {
            if ($category) {
                $seoContent = $category->getSeoContent();

                if ($categoryToken != Category::FAKE_SHOP_TOKEN) {
                    $hotlinks = $category->getSeoHotlinks();

                    // SITE-4439
                    if (!$category->getHasChild() || in_array($category->getId(), [1096])) {
                        foreach ($brands as $iBrand) {
                            $hotlinks[] = new \Model\Seo\Hotlink\Entity([
                                'group' => '',
                                'name' => $iBrand->getName(),
                                'url' => \App::router()->generateUrl('product.category', [
                                    'categoryPath' => $categoryPath,
                                    'brandToken'   => $iBrand->getToken(),
                                ]),
                            ]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['hotlinks']);
        }

        // на страницах пагинации сео-контент не показываем
        if ($page > 1) {
            $seoContent = '';
        }

        if (
            // промо-контент не показываем на страницах пагинации, брэнда, фильтров
            $page > 1 || !empty($brand) || (bool)((array)$request->get(\View\Product\FilterForm::$name, []))
        ) {
            $promoContent = '';
        }

        // promo slider
        $slideData = null;
        if (array_key_exists('promo_slider', $catalogJson) || $category->isTchibo()) {
            $show = isset($catalogJson['promo_slider']['show']) ? (bool)$catalogJson['promo_slider']['show'] : false;
            $promoCategoryToken = isset($catalogJson['promo_slider']['promo_token']) ? trim($catalogJson['promo_slider']['promo_token']) : null;

            if ($category->isTchibo()) {
                $promoCategoryToken = 'tchibo';
                $show = true;
            }
            if ($show && !empty($promoCategoryToken)) {
                try {
                    $promoRepository = \RepositoryManager::promo();
                    /** @var $promo \Model\Promo\Entity */
                    $promo = null;

                    $promoRepository->prepareByToken($promoCategoryToken, function($data) use (&$promo, &$promoCategoryToken) {
                        $data = isset($data[0]['uid']) ? $data[0] : null;
                        if ($data) {
                            $data['token'] = $promoCategoryToken;
                            $promo = new \Model\Promo\Entity($data);
                        }
                    });
                    $client->execute();

                    if (!$promo) {
                        throw new \Exception\NotFoundException(sprintf('Не найден промо-каталог "%s"', $promoCategoryToken));
                    }

                    $productsByUi = [];
                    $productUis = [];
                    // перевариваем данные изображений
                    // используя айдишники товаров из секции image.products, получим мини-карточки товаров
                    foreach ($promo->getPages() as $promoPage) {
                        $uiChunk = [];
                        foreach ($promoPage->getProducts() as $product) {
                            $uiChunk[] = $product->ui;
                        }

                        $productUis = array_merge($productUis, $uiChunk);
                    }
                    $productUis = array_unique($productUis);
                    
                    foreach ($productUis as $productUi) {
                        $productsByUi[$productUi] = new \Model\Product\Entity(['ui' => $productUi]);
                    }

                    \RepositoryManager::product()->prepareProductQueries($productsByUi, 'media');
                    $client->execute(\App::config()->coreV2['retryTimeout']['short']);

                    $cartButtonAction = new \View\Cart\ProductButtonAction();
                    // перевариваем данные изображений для слайдера в $slideData
                    foreach ($promo->getPages() as $promoPage) {
                        if (!$promoPage instanceof \Model\Promo\Page\Entity) continue;

                        $itemProducts = [];
                        foreach($promoPage->getProducts() as $promoProduct) {
                            $product = isset($productsByUi[$promoProduct->ui]) ? $productsByUi[$promoProduct->ui] : null;
                            if (!$product || !$promoPage->getImageUrl()) continue;

                            /** @var $product \Model\Product\Entity */
                            $itemProducts[] = [
                                'image'      => $product->getMainImageUrl('product_160'),
                                'link'       => $product->getLink(),
                                'name'       => $product->getName(),
                                'price'      => $product->getPrice(),
                                'isBuyable'  => ($product->getIsBuyable() || $product->isInShopOnly() || $product->isInShopStockOnly()),
                                'statusId'   => $product->getStatusId(),
                                'cartButton' => $cartButtonAction->execute(new \Helper\TemplateHelper(), $product)
                            ];
                        }

                        $slideData[] = [
                            'target'   => '_self',
                            'imgUrl'   => $promoPage->getImageUrl(),
                            'title'    => $promoPage->getName(),
                            'linkUrl'  => $promoPage->getLink()?($promoPage->getLink().'?from='.$promo->getToken()):'',
                            'time'     => $promoPage->getTime() ? $promoPage->getTime() : 3000,
                            'products' => $itemProducts,
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
            &$slideData,
            &$callbackPhrases
        ) {
            $page->setParam('category', $category);
            $page->setParam('productFilter', $productFilter);
            $page->setParam('brand', $brand);
            $page->setParam('hotlinks', $hotlinks);
            $page->setParam('seoContent', $seoContent);
            $page->setParam('catalogJson', $catalogJson);
            $page->setParam('promoContent', $promoContent);
            $page->setGlobalParam('shop', $shop);
            $page->setParam('relatedCategories', $relatedCategories);
            $page->setParam('categoryConfigById', $categoryConfigById);
            $page->setParam('viewParams', [
                'showSideBanner' => \Controller\ProductCategory\Action::checkAdFoxBground($catalogJson)
            ]);
            $page->setParam('categoryPath', $categoryPath);
            $page->setGlobalParam('slideData', $slideData);
            $page->setGlobalParam('isTchibo', ($category->getRoot() && 'Tchibo' === $category->getRoot()->getName()));
            $page->setGlobalParam('callbackPhrases', $callbackPhrases);
        };

        // если категория содержится во внешнем узле дерева
        if ($category->isLeaf()) {
            $pageView = new \View\ProductCategory\LeafPage();
            $setPageParameters($pageView);

            return $this->leafCategory($category, $productFilter, $pageView, $request, $categoryToken, $page);
        }
        // иначе, если в запросе есть фильтрация
        else if ($request->get(\View\Product\FilterForm::$name)) {
            $pageView = new \View\ProductCategory\LeafPage();
            $setPageParameters($pageView);

            return $this->leafCategory($category, $productFilter, $pageView, $request, $categoryToken, $page);
        }
        // иначе, если категория самого верхнего уровня
        else if ($category->isRoot() && !$category->isAutoGrid()) {
            $pageView = new \View\ProductCategory\RootPage();
            $setPageParameters($pageView);

            return $this->rootCategory($category, $productFilter, $pageView, $request, $categoryConfigById);
        }

        $pageView = new \View\ProductCategory\LeafPage();
        $setPageParameters($pageView);

        return $this->leafCategory($category, $productFilter, $pageView, $request, $categoryToken, $page);
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
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->debug) \App::debug()->add('routeSubAction', 'ProductCategory\\Action::rootCategory', 134);

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
        $relatedCategories = $page->getParam('relatedCategories');

        /** @var $categories \Model\Product\Category\Entity[] */
        $categories = $category->getChild();
        if (!empty($relatedCategories)) {
            $categories = array_merge($categories, $relatedCategories);
        }

        $links = [];
        foreach ($categories as $child) {
            $config = isset($categoryConfigById[$child->getId()]) ? $categoryConfigById[$child->getId()] : null;
            $productCount = $child->getProductCount();
            $totalText = '';

            if ( $productCount > 0 ) {
                $totalText = $productCount . ' ' . ($page->helper->numberChoice($productCount, array('товар', 'товара', 'товаров')));
            }

            $links[] = [
                'name'          => isset($config['name']) ? $config['name'] : $child->getName(),
                'url'           => call_user_func(function() use($child) {
                    $query = \App::request()->query->all();
                    unset($query['ajax']);
                    if (\App::request()->get('instore')) {
                        $query['instore'] = 1;
                    }

                    $url = $child->getLink();
                    return $url . (strpos($url, '?') === false ? '?' : '&') . http_build_query($query);
                }),
                'image'         => (!empty($config['image']))
                    ? $config['image']
                    : $child->getImageUrl(\App::config()->lite['enabled'] ? 3 : 0),
                'css'           => isset($config['css']) ? $config['css'] : null,
                'totalText'     => $totalText,
            ];
        }

        return $links;
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Product\Filter $productFilter
     * @param \View\Layout $pageView
     * @param \Http\Request $request
     * @param string|null $categoryToken
     * @param string|null $page
     * @return \Http\Response
     * @throws \Exception
     */
    protected function leafCategory(\Model\Product\Category\Entity $category, \Model\Product\Filter $productFilter, \View\Layout $pageView, \Http\Request $request, $categoryToken = null, $page = null) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->debug) \App::debug()->add('routeSubAction', 'ProductCategory\\Action::leafCategory', 134);

        $region = \App::user()->getRegion();

        $catalogJson = $pageView->getParam('catalogJson');

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

        // листалка
        if ($category->isV2Furniture() && \Session\AbTest\AbTest::isNewFurnitureListing()) {
            $itemsPerPage = 21;
        } else if ($category->isTchibo()) {
            $itemsPerPage = 21;
        } else {
            $itemsPerPage = \App::config()->product['itemsPerPage'];
        }

        $limit = $itemsPerPage;
        $offset = ($page - 1) * $limit;

        $hasBanner = !empty($catalogJson['bannerPlaceholder']);

        if (\App::config()->lite['enabled']) {
            $hasBanner = false;
        }

        if ($hasBanner) {
            // уменшаем кол-во товаров на первой странице для вывода баннера
            $offset = $offset - (1 === $page ? 0 : 1);
            $limit = $limit - (1 === $page ? 1 : 0);
        }

        $repository = \RepositoryManager::product();

        $filters = $productFilter->dump($categoryToken == Category::FAKE_SHOP_TOKEN);

        $smartChoiceData = [];
        /** @var \Model\Product\Entity[] $smartChoiceProductsById */
        $smartChoiceProductsById = [];
        call_user_func(function() use(&$smartChoiceData, &$smartChoiceProductsById, $filters, $catalogJson, $repository) {
            if (!\App::config()->product['smartChoiceEnabled'] || !isset($catalogJson['smartchoice']) || !$catalogJson['smartchoice']) {
                return;
            }

            try {
                $smartChoiceFilters = $filters;
                if (!in_array('is_store', array_map(function($var){return $var[0];}, $smartChoiceFilters))) {
                    $smartChoiceFilters[] = ["is_store",1,1];
                }

                $smartChoiceData = \App::coreClientV2()->query('listing/smart-choice', ['region_id' => \App::user()->getRegion()->getId(), 'client_id' => 'site', 'filter' => ['filters' => $smartChoiceFilters]]);

                // SITE-4715
                $smartChoiceData = array_filter($smartChoiceData, function($a) {
                    return !empty($a['products'][0]['id']);
                });

                foreach ($smartChoiceData as $smartChoiceItem) {
                    $smartChoiceProductsById[$smartChoiceItem['products'][0]['id']] = new \Model\Product\Entity(['id' => $smartChoiceItem['products'][0]['id']]);
                }

                $repository->prepareProductQueries($smartChoiceProductsById, 'media label');
            } catch (\Exception $e) {
                $smartChoiceData = [];
            }
        });

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
                if (is_array($data)) {
                    if (isset($data['list'][0])) $productIds = $data['list'];
                    if (isset($data['count'])) $productCount = (int)$data['count'];
                }
            }
        );
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        call_user_func(function() use(&$smartChoiceData, &$smartChoiceProductsById) {
            if (count($smartChoiceProductsById) == 3) {
                foreach ($smartChoiceData as &$smartChoiceItem) {
                    if (isset($smartChoiceProductsById[$smartChoiceItem['products'][0]['id']])) {
                        $smartChoiceItem['product'] = $smartChoiceProductsById[$smartChoiceItem['products'][0]['id']];
                    }
                }
            } else {
                $smartChoiceData = [];
            }
        });

        // HINT Можно добавлять ID неопубликованных продуктов для показа в листингах
        // array_unshift($productIds, 201540);

        // TODO удалить (электронный сертификат в листинг сертификатов)
        if ($category->ui === 'b2885b1b-06bc-4c6f-b40d-9a0af22ff61c') array_unshift($productIds, 201540);

        $view = $category->getChosenView();

        /** @var \Model\Product\Entity[] $products */
        $products = array_map(function($productId) { return new \Model\Product\Entity(['id' => $productId]); }, $productIds);

        $repository->prepareProductQueries($products, 'model media label brand category' . ($view === \Model\Product\Category\Entity::VIEW_EXPANDED ? ' property' : ''));

        \App::coreClientV2()->execute();

        // избранные товары пользователя
        /** @var \Model\Favorite\Product\Entity[] $favoriteProductsByUi */
        $favoriteProductsByUi = [];
        call_user_func(function() use (&$products, &$favoriteProductsByUi) {
            $userUi = \App::user()->getEntity() ? \App::user()->getEntity()->getUi() : null;
            if (!$userUi) return;
            $productUis = array_map(function(\Model\Product\Entity $product) { return $product->ui; }, $products);
            if (!$productUis) return;

            $favoriteQuery = new \EnterQuery\User\Favorite\Check($userUi, $productUis);
            $favoriteQuery->prepare();

            $this->getCurl()->execute();

            // избранные товары
            $favoriteProductsByUi = [];
            foreach ($favoriteQuery->response->products as $item) {
                if (!isset($item['is_favorite']) || !$item['is_favorite']) continue;

                $ui = isset($item['uid']) ? (string)$item['uid'] : null;
                if (!$ui) continue;

                $favoriteProductsByUi[$ui] = new \Model\Favorite\Product\Entity($item);
            }
        });

        if (!$products && 'true' == $request->get('ajax') && $page > 1 && !\App::config()->lite['enabled']) {
            throw new \Exception('Не удалось получить товары');
        }

        if (\App::config()->product['reviewEnabled']) {
            \RepositoryManager::review()->prepareScoreCollection($products, function($data) use(&$products) {
                if (isset($data['product_scores'][0])) {
                    \RepositoryManager::review()->addScores($products, $data);
                }
            });
        }

        \App::coreClientV2()->execute();

        // SITE-5772
        call_user_func(function() use(&$products, $category) {
            $sender = $category->getSenderForGoogleAnalytics();
            if ($sender) {
                foreach ($products as $product) {
                    $product->setLink($product->getLink() . (strpos($product->getLink(), '#') === false ? '#' : '&') . http_build_query(['sender' => $sender]));
                }
            }
        });

        $productPager = new \Iterator\EntityPager($products, $productCount);

        // Если товаров слишком мало (меньше 3 строк в листинге), то не показываем SmartChoice
        if ($productPager->count() < 7) $smartChoiceData = [];

        if ($hasBanner) {
            $productPager->setCount($productPager->count() + 1);
        }
        $productPager->setPage($page);
        $productPager->setMaxPerPage($itemsPerPage);
        $category->setProductCount($productPager->count());

        // проверка на максимально допустимый номер страницы
        if ((1 != $productPager->getPage()) && (($productPager->getPage() - $productPager->getLastPage()) > 0)) {
            //throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $productPager->getPage()));
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([
                'page' => $productPager->getLastPage(),
            ]));
        }

        if ($category->isV2Furniture()) {
            if (\Session\AbTest\AbTest::isNewFurnitureListing()) {
                $columnCount = 3;
            } else {
                $columnCount = 4;
            }
        } else {
            $columnCount = (bool)array_intersect(array_map(function(\Model\Product\Category\Entity $category) { return $category->getId(); }, $category->getAncestor()), [1320, 4649]) ? 3 : 4;
        }

        $rootCategoryInMenu = null;
        if ($category->isTchibo()) {
            $columnCount = 3;
            \RepositoryManager::productCategory()->prepareTreeCollectionByRoot($category->getRoot()->getId(), $region, 3, function($data) use (&$rootCategoryInMenu) {
                $data = is_array($data) ? reset($data) : [];
                if (isset($data['id'])) {
                    $rootCategoryInMenu = new \Model\Product\Category\TreeEntity($data);
                }
            });

            \App::searchClient()->execute();
        }

        $helper = new \Helper\TemplateHelper();

        $listViewData = (new \View\Product\ListAction())->execute(
            $helper,
            $productPager,
            $hasBanner ? $catalogJson['bannerPlaceholder'] : [],
            null,
            true,
            $columnCount,
            $view,
            $category->getSenderForGoogleAnalytics(),
            $category,
            $favoriteProductsByUi,
            ($category->isV2Furniture() && \Session\AbTest\AbTest::isNewFurnitureListing()) || $category->isTchibo()
        );

        $title = $category->getName() . ($productPager->getPage() > 1 ? ': страница ' . $productPager->getPage() : '');

        if ($request->isXmlHttpRequest() && 'true' == $request->get('ajax')) {
            $selectedFilter = $category->isV2() ? new \View\Partial\ProductCategory\V2\SelectedFilter() : new \View\ProductCategory\SelectedFilterAction();
            $data = [
                'list'           => $listViewData,
                'selectedFilter' => $selectedFilter->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productFilter
                ),
                'pagination'     => (new \View\PaginationAction())->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productPager,
                    $category
                ),
                'sorting'        => (new \View\Product\SortingAction())->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productSorting
                ),
                'page'           => [
                    'title'      => $title
                ],
                'categoryId'  => $category->id,
                'countProducts'  => ($hasBanner) ? ( $productPager->count() - 1 ) : $productPager->count(),
                'request' => [
                    'route' => [
                        'name' => \App::request()->routeName,
                        'pathVars' => \App::request()->routePathVars->all(),
                    ],
                ],
            ];

            return new \Http\JsonResponse($data);
        }

        $pageView->setParam('title', $title);
        $pageView->setParam('smartChoiceProducts', $smartChoiceData);
        $pageView->setParam('productPager', $productPager);
        $pageView->setParam('productCount', $productCount);
        $pageView->setParam('productSorting', $productSorting);
        $pageView->setParam('hasBanner', $hasBanner);
        $pageView->setParam('rootCategoryInMenu', $rootCategoryInMenu);
        $pageView->setParam('listViewData', $listViewData);

        return new \Http\Response($pageView->show());
    }

    private function convertFiltersToSearchClientRequestFormat($filterValues) {
        $result = [];
        if (is_array($filterValues)) {
            foreach ($filterValues as $name => $values) {
                if (isset($values['from']) || isset($values['to'])) {
                    $result[] = [
                        $name,
                        2,
                        isset($values['from']) ? $values['from'] : null,
                        isset($values['to']) ? $values['to'] : null
                    ];
                } else {
                    $result[] = [$name, 1, $values];
                }
            }
        }

        return $result;
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
    private function createInStoreFilter(array &$filters, \Model\Product\Category\Entity $category) {
        // TODO SITE-2403 Вернуть фильтр instore
        if (!$category->getIsFurniture()) {
            return;
        }
        
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
    private function transformFiltersV2(array &$filters, \Model\Product\Category\Entity $category) {
        if (!$category->isV2()) {
            return;
        }

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
                if ($category->isAlwaysShowBrand()) {
                    $property->setIsAlwaysShow(true);
                }

                $this->sortOptionsByQuantity($property);
            }
        }

        if ($category->isTyre()) {
            foreach ($filters as $key => $property) {
                if ($property->getName() === 'Сезон') {
                    $property->defaultTitle = 'Все сезоны';
                } else if ($property->getName() === 'Бренд') {
                    $property->defaultTitle = 'Все производители';
                } else if ($property->getName() === 'Ширина') {
                    $property->defaultTitle = 'Не выбрано';
                } else if ($property->getName() === 'Профиль') {
                    $property->defaultTitle = 'Не выбрано';
                } else if ($property->getName() === 'Диаметр') {
                    $property->defaultTitle = 'Не выбрано';
                }
            }

            usort($filters, function(\Model\Product\Filter\Entity $a, \Model\Product\Filter\Entity $b) {
                $order = [
                    'Сезон' => 0,
                    'Бренд' => 1,
                    'Ширина' => 2,
                    'Профиль' => 3,
                    'Диаметр' => 4,
                ];

                if (isset($order[$a->getName()])) {
                    $a = $order[$a->getName()];
                } else {
                    $a = 0;
                }

                if (isset($order[$b->getName()])) {
                    $b = $order[$b->getName()];
                } else {
                    $b = 0;
                }

                if ($a == $b) {
                    return 0;
                }

                return $a < $b ? -1 : 1;
            });
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
     * @param \Model\Product\Filter\Entity[] $filters
     */
    private function correctFiltersForBronnitskiyYuvelir(array &$filters, \Model\Product\Category\Entity $category) {
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
                $category->setView(\Model\Product\Category\Entity::VIEW_LIGHT_WITH_BOTTOM_DESCRIPTION);
            } else {
                $category->setView(\Model\Product\Category\Entity::VIEW_LIGHT_WITH_HOVER_BOTTOM_DESCRIPTION);
            }
        }
    }
}