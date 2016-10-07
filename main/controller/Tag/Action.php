<?php

namespace Controller\Tag;

class Action {
    public function index($tagToken, \Http\Request $request, $categoryToken = null, $page = null) {
        if (!isset($page) && $request->query->get('page')) {
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([
                'page' => (int)$request->query->get('page'),
            ]), 301);
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

        //\App::logger()->debug('Exec ' . __METHOD__);
        $client = \App::coreClientV2();
        /** @var $region \Model\Region\Entity */
        $region = \App::user()->getRegion();

        $tag = \RepositoryManager::tag()->getEntityByToken($tagToken);
        if (!$tag) {
            throw new \Exception\NotFoundException(sprintf('Тег @%s не найден', $tagToken));
        }

        /** @var $categories \Model\Product\Category\Entity[] */
        $categories = [];

        $selectedCategory = null;
        $categoryId = $request->get('category');
        if ($categoryId) {
            $selectedCategory = \RepositoryManager::productCategory()->getEntityById($categoryId);
        }

        if (!$selectedCategory && $categoryToken) {
            // Если категория текущая не определена, но указан токен категории

            // запрос сделаем, если токен указан, u не полученна категория выбранная
            \RepositoryManager::productCategory()->prepareEntityByToken($categoryToken, $region, function ($data) use (&$selectedCategory) {
                if ($data && is_array($data)) {
                    $selectedCategory = new \Model\Product\Category\Entity($data);
                }
            });
            $client->execute(\App::config()->coreV2['retryTimeout']['short']);

        }

        $queryParams = [
            'filter' => ['filters' => [
                ['tag', 1, $tag->id],
            ]],
            'client_id' => 'site',
            'min_level' => 1,
            'max_level' => 1,
        ];

        if ($selectedCategory) {
            $queryParams['root_id'] = $selectedCategory->getId();
            $queryParams['min_level'] += $selectedCategory->getLevel();
            $queryParams['max_level'] += $selectedCategory->getLevel();
        }

        if ($region) {
            $queryParams['region_id'] = $region->getId();
        }

        \App::searchClient()->addQuery('category/tree', $queryParams, [],
            function ($data) use (&$categories) {
                if (is_array($data)) {
                    foreach ($data as $catFields) {
                        if (is_array($catFields)) {
                            $categories[] = new \Model\Product\Category\Entity($catFields);
                        }
                    }
                }
            }
        );

        if ($selectedCategory) {
            $catalogJson = $selectedCategory->catalogJson;
        }

        $client->execute();

        if (!$selectedCategory && $categoryToken) {
            foreach ($categories as $category) {
                if ($category->getToken() === $categoryToken) {
                    $selectedCategory = $category;
                }
            }

            if (!$categoryToken) {
                throw new \Exception\NotFoundException(sprintf('Категория @%s не найдена', $categoryToken));
            }
        }

        $filters = [];
        $filter = new \Model\Product\Filter\Entity();
        $filter->setId('tag');
        $filter->setIsInList(false);
        $filters[] = $filter;

        \RepositoryManager::productFilter()->prepareCollectionByTag($tag, \App::user()->getRegion(), function($data) use (&$filters) {
            foreach ($data as $item) {
                $filters[] = new \Model\Product\Filter\Entity($item);
            }
        }, function (\Exception $e) { \App::exception()->remove($e); });

        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['long'], 2);


        $shop = null;
        try {
            if (\App::request()->get('shop') && \App::config()->shop['enabled']) {
                $shop = \RepositoryManager::shop()->getEntityById( \App::request()->get('shop') );
            }
        } catch (\Exception $e) {
            \App::logger()->error(sprintf('Не удалось отфильтровать товары по магазину #%s', \App::request()->get('shop')));
        }

        $productFilter = \RepositoryManager::productFilter()->createProductFilter($filters, $selectedCategory, null, $request, $shop);
        $productFilter->setValue('tag', $tag->id);
        if ($selectedCategory) {
            $productFilter->setCategory($selectedCategory);
        }

        // сортировка
        $productSorting = new \Model\Product\Sorting();
        list($sortingName, $sortingDirection) = array_pad(explode('-', $request->get('sort')), 2, null);
        $productSorting->setActive($sortingName, $sortingDirection);

        // если сортировка по умолчанию и в json заданы настройки сортировок,
        // то применяем их
        if (!empty($catalogJson['sort']) && $productSorting->isDefault()) {
            $sort = $catalogJson['sort'];
        } else {
            $sort = $productSorting->dump();
        }

        // листалка
        $limit = \App::config()->product['itemsPerPage'];
        $repository = \RepositoryManager::product();

        /** @var \Model\Product\Entity[] $products */
        $products = [];
        $productCount = 0;
        $repository->prepareIteratorByFilter(
            $productFilter->dump(),
            $sort,
            ($page - 1) * $limit,
            $limit,
            $region,
            function($data) use (&$products, &$productCount) {
                if (isset($data['list'][0])) {
                    $products = array_map(function($productId) { return new \Model\Product\Entity(['id' => $productId]); }, $data['list']);
                }
                if (isset($data['count'])) $productCount = (int)$data['count'];
            }
        );
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        if ($products) {
            \RepositoryManager::product()->prepareProductQueries($products, 'model media label brand category');
            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);
        }

        \RepositoryManager::review()->prepareScoreCollection($products, function($data) use(&$products) {
            if (isset($data['product_scores'][0])) {
                \RepositoryManager::review()->addScores($products, $data);
            }
        });

        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        $productPager = new \Iterator\EntityPager($products, $productCount);
        $productPager->setPage($page);
        $productPager->setMaxPerPage($limit);

        // проверка на максимально допустимый номер страницы
        if (($productPager->getPage() - $productPager->getLastPage()) > 0) {
            //throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $productPager->getPage()));
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([
                'page' => $productPager->getLastPage(),
            ]));
        }

        $templating = \App::closureTemplating();
        /** @var $helper \Helper\TemplateHelper */
        if ($selectedCategory) {
            $templating->setParam('selectedCategory', $selectedCategory);
        }

        $helper = $templating->getParam('helper');
        $selectedFilter = (new \View\ProductCategory\SelectedFilterAction())->execute(
            $helper,
            $productFilter
        );

        $helper = new \Helper\TemplateHelper();

        $listViewData = (new \View\Product\ListAction())->execute(
            $helper,
            $productPager
        );

        if ($request->isXmlHttpRequest() && 'true' == $request->get('ajax')) {
            return new \Http\JsonResponse([
                'list'           => $listViewData,
                'selectedFilter' => $selectedFilter,
                'pagination'     => (new \View\PaginationAction())->execute(
                    $helper,
                    $productPager
                ),
                'sorting'        => (new \View\Product\SortingAction())->execute(
                    $templating->getParam('helper'),
                    $productSorting
                ),
                'page'           => [
                    'title'      => 'Тег «' . $tag->name . '»' .
                        ( $selectedCategory ? ( ' — ' . $selectedCategory->getName() ) : '' )
                ],
                'request' => [
                    'route' => [
                        'name' => \App::request()->routeName,
                        'pathVars' => \App::request()->routePathVars->all(),
                    ],
                ],
            ]);
        }

        $pageView = new \View\Tag\IndexPage();
        $pageView->setParam('productPager', $productPager);
        $pageView->setParam('productFilter', $productFilter);
        $pageView->setParam('selectedFilter', $selectedFilter);
        $pageView->setParam('productSorting', $productSorting);
        $pageView->setParam('tag', $tag);
        $pageView->setParam('sort', $sort);
        $pageView->setParam('selectedCategory', $selectedCategory);
        $pageView->setParam('categories', $categories);
        $pageView->setParam('listViewData', $listViewData);
        return new \Http\Response($pageView->show());
    }
}