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

        // получаем из json данные о горячих ссылках и content
        $seoTagJson = \Model\Tag\Repository::getSeoJson($tag);
        $hotlinks = empty($seoTagJson['hotlinks']) ? [] : $seoTagJson['hotlinks'];
        // в json-файле в свойстве content содержится массив
        $seoContent = empty($seoTagJson['content']) ? '' : implode('<br>', $seoTagJson['content']);
        // на страницах пагинации сео-контент не показываем
        if ($pageNum > 1) $seoContent = '';

        // категории
        /** @var $tagCategoriesById \Model\Tag\Category\Entity[] */
        $tagCategoriesById = [];
        //$tagCategories = $tag->getCategory();
        /** @var $categoriesByToken \Model\Product\Category\Entity[] */
        $categoriesByToken = [];
        $categories = [];

        $selectedCategory = $this->getSelectedCategoryByRequest($request); // Попробуем получить категорию из request

        if (!$selectedCategory && $categoryToken) {
            // Если категория текущая не определена, но указан токен категории

            // запрос сделаем, если токен указан, u не полученна категория выбранная
            \RepositoryManager::productCategory()->prepareEntityByToken($categoryToken, $region, function ($data) use (&$selectedCategory) {
                $data = reset($data);
                if ((bool)$data) {
                    $selectedCategory = new \Model\Product\Category\Entity($data);
                }
            });
            $client->execute(\App::config()->coreV2['retryTimeout']['short']);

        }

        $queryParams = [
            'filter' => ['filters' => [
                ['tag', 1, $tag->getId()],
            ]],
            'client_id' => 'site',
            //'is_load_parents' => false,
            'min_level'       => 1,
            'max_level'       => 1,
        ];

        if ($selectedCategory) {
            $queryParams['root_id'] = $selectedCategory->getId();
            $queryParams['min_level'] += $selectedCategory->getLevel();
            $queryParams['max_level'] += $selectedCategory->getLevel();
        }

        if ($region) {
            $queryParams['region_id'] = $region->getId();
        }

        $client->addQuery('category/tree', $queryParams, [],
            function ($data) use (&$categories, &$tagCategoriesNumbers) {
                foreach ($data as $catFields) {
                    $category = new \Model\Product\Category\Entity($catFields);
                    $categories[] = $category;
                }
            }
        );
        $client->execute();


        foreach ($categories as $category) {
            /** @var $category \Model\Product\Category\Entity */
            $categoriesByToken[$category->getToken()] = $category;
            $tagCategoriesById[$category->getId()] = $category;
        }


        // Проверим ещё раз: Для указанного $categoryToken обязательно должна быть $selectedCategory
        if (!$selectedCategory && $categoryToken) {
            if (isset($categoriesByToken[$categoryToken])) { // возьмём его из массива загруженных
                $selectedCategory = $categoriesByToken[$categoryToken];
            }

            // Без $selectedCategory дальше не пойдём в этом случае
            if (!$categoryToken) {
                throw new \Exception\NotFoundException(sprintf('Категория @%s не найдена', $categoryToken));
            }
        }



        // фильтры
        $filters = []; // фильтр для тегов
        $filter = new \Model\Product\Filter\Entity();
        $filter->setId('tag');
        $filter->setIsInList(false);

        $filters[] = $filter;

        \RepositoryManager::productFilter()->prepareCollectionByTag( $tag,
            \App::user()->getRegion(),
            function($data) use (&$filters) {
                foreach ($data as $item) {
                    $filters[] = new \Model\Product\Filter\Entity($item);
                }
            }, function (\Exception $e) { \App::exception()->remove($e); });
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['long'], 2);


        $shop = null;
        try {
            if (!\Controller\ProductCategory\Action::isGlobal() && \App::request()->get('shop') && \App::config()->shop['enabled']) {
                $shop = \RepositoryManager::shop()->getEntityById( \App::request()->get('shop') );
            }
        } catch (\Exception $e) {
            \App::logger()->error(sprintf('Не удалось отфильтровать товары по магазину #%s', \App::request()->get('shop')));
        }



        $brand = null;

        $productFilter = (new \Controller\ProductCategory\Action())->getFilter($filters, $selectedCategory, $brand, $request, $shop);
        $productFilter->setValue( 'tag', $tag->getId() );
        if ($selectedCategory) {
            $productFilter->setCategory($selectedCategory);
        }




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
        $productView = \Model\Product\Category\Entity::PRODUCT_VIEW_COMPACT;
        if ($selectedCategory) {
            $productView = $request->get('view', $selectedCategory->getHasLine() ? 'line' : $selectedCategory->getProductView());
        }


        // листалка
        $limit = \App::config()->product['itemsPerPage'];
        $repository = \RepositoryManager::product();
        $repository->setEntityClass('\\Model\\Product\\Entity');

        $productIds = [];
        $productCount = 0;
        $repository->prepareIteratorByFilter(
            $productFilter->dump(),
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

            $scoreData = [];
            \RepositoryManager::review()->prepareScoreCollection($productIds, function($data) use (&$scoreData) {
                if (isset($data['product_scores'][0])) {
                    $scoreData = $data;
                }
            });
        }
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        \RepositoryManager::review()->addScores($products, $scoreData);

        $productPager = new \Iterator\EntityPager($products, $productCount);
        $productPager->setPage($pageNum);
        $productPager->setMaxPerPage($limit);

        // проверка на максимально допустимый номер страницы
        if (($productPager->getPage() - $productPager->getLastPage()) > 0) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $productPager->getPage()));
        }

        $templating = \App::closureTemplating();
        /** @var $helper \Helper\TemplateHelper */
        if ($selectedCategory) {
            $templating->setParam('selectedCategory', $selectedCategory);
        }
        //if ($shop) $templating->setParam('shop', $shop);
        $helper = $templating->getParam('helper');
        $selectedFilter = (new \View\ProductCategory\SelectedFilterAction())->execute(
            $helper,
            $productFilter,
            \App::router()->generate('product.category', ['categoryPath' => $selectedCategory ? $selectedCategory->getPath() : null])
        );

        // ajax
        if ($request->isXmlHttpRequest() && 'true' == $request->get('ajax')) {

            $productVideosByProduct = [];

            return new \Http\JsonResponse([
                'list'           => (new \View\Product\ListAction())->execute(
                    $helper,
                    $productPager,
                    $productVideosByProduct,
                    !empty($catalogJson['bannerPlaceholder']) ? $catalogJson['bannerPlaceholder'] : []
                ),
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
                    'title'      => 'Тег «'.$tag->getName() . '»' .
                        ( $selectedCategory ? ( ' — ' . $selectedCategory->getName() ) : '' )
                ],
            ]);
        }


        // seo из shopscript
        $shopScriptSeo = [];
        if ($selectedCategory && \App::config()->shopScript['enabled']) {
            $shopScript = \App::shopScriptClient();
            $shopScript->addQuery('category/get-seo', [
                'slug' => $selectedCategory->getToken(),
                'geo_id' => \App::user()->getRegion()->getId(),
            ], [], function ($data) use (&$shopScriptSeo) {
                if($data && is_array($data)) $shopScriptSeo = reset($data);
            });
            $shopScript->execute();
        }



        // new
        $page = new \View\Tag\IndexPage();
        $page->setParam('productPager', $productPager);
        $page->setParam('productFilter', $productFilter);
        $page->setParam('selectedFilter', $selectedFilter);
        $page->setParam('productSorting', $productSorting);
        $page->setParam('tag', $tag);
        $page->setParam('productFilter', $productFilter);
        $page->setParam('productSorting', $productSorting);
        $page->setParam('sort', $sort);
        $page->setParam('productView', $productView);
        $page->setParam('selectedCategory', $selectedCategory);
        $page->setParam('categories', array_values($categoriesByToken));
        $page->setParam('hotlinks', $hotlinks);
        $page->setParam('seoContent', $seoContent);
        $page->setParam('sidebarHotlinks', true);
        $page->setParam('categoriesByToken', $categoriesByToken);
        $page->setParam('productView', $productView);
        $page->setParam('shopScriptSeo', $shopScriptSeo);
        return new \Http\Response($page->show());

/*//old
        if($category) {
            // seo из shopscript
            $shopScriptSeo = [];
            if(\App::config()->shopScript['enabled']) {
                $shopScript = \App::shopScriptClient();
                $shopScript->addQuery('category/get-seo', [
                        'slug' => $category->getToken(),
                        'geo_id' => \App::user()->getRegion()->getId(),
                    ], [], function ($data) use (&$shopScriptSeo) {
                    if($data && is_array($data)) $shopScriptSeo = reset($data);
                });
                $shopScript->execute();
            }


            $setPageParameters = function(\View\Layout $page) use (
                &$tag,
                &$productPager,
                &$productFilter,
                &$selectedFilter,
                &$productSorting,
                &$sort,
                &$productView,
                &$category,
                &$categoryToken,
                &$categoriesByToken,
                &$hotlinks,
                &$seoContent,
                &$sidebarCategoriesTree,
                &$categoriesByToken,
                &$catalogJson,
                &$shopScriptSeo
            ) {
                $page->setParam('tag', $tag);
                $page->setParam('productPager', $productPager);
                $page->setParam('productFilter', $productFilter);
                $page->setParam('selectedFilter', $selectedFilter);
                $page->setParam('productSorting', $productSorting);
                $page->setParam('sort', $sort);
                $page->setParam('productView', $productView);
                $page->setParam('category', $category);
                $page->setParam('categoryToken', $categoryToken);
                $page->setParam('categories', array_values($categoriesByToken));
                $page->setParam('hotlinks', $hotlinks);
                $page->setParam('seoContent', $seoContent);
                $page->setParam('sidebarHotlinks', true);
                $page->setParam('sidebarCategoriesTree', $sidebarCategoriesTree);
                $page->setParam('categoriesByToken', $categoriesByToken);
                $page->setParam('catalogJson', $catalogJson);
                $page->setParam('shopScriptSeo', $shopScriptSeo);
            };
        } else { // if (!$category)
            $setPageParameters = function(\View\Layout $page) use (
                &$tag,
                &$category,
                &$categoryToken,
                &$categoriesByToken,
                &$hotlinks,
                &$seoContent,
                &$sidebarCategoriesTree,
                &$categoriesByToken,
                &$productFilter,
                &$selectedFilter,
                &$productPager,
                &$productSorting,
                &$productView
            ) {
                $page->setParam('tag', $tag);
                $page->setParam('productPager', $productPager);
                $page->setParam('productFilter', $productFilter);
                $page->setParam('selectedFilter', $selectedFilter);
                $page->setParam('productSorting', $productSorting);
                $page->setParam('productView', $productView);
                $page->setParam('category', $category);
                $page->setParam('categoryToken', $categoryToken);
                $page->setParam('categories', array_values($categoriesByToken));
                $page->setParam('hotlinks', $hotlinks);
                $page->setParam('seoContent', $seoContent);
                $page->setParam('sidebarHotlinks', true);
                $page->setParam('sidebarCategoriesTree', $sidebarCategoriesTree);
                $page->setParam('categoriesByToken', $categoriesByToken);
            };
        }

        if(empty($seoTagJson['acts_as_category'])) {
            $sidebarCategoriesTree = null;
            $page = new \View\Tag\IndexPage();
            $setPageParameters($page);
            return new \Http\Response($page->show());
        } else {
            $productCategoryRepository = \RepositoryManager::productCategory();
            $productCategoryRepository->setEntityClass('\Model\Product\Category\TreeEntity');

            $category = null;
            $rootCategory = null;
            $currentRoot = null;
            $categoriesByToken = [];
            $sidebarCategoriesTree = [];
            $categoryProductCountsByToken = [];
            $walk = function($treeCategories) use (&$walk, &$category, &$rootCategory, &$categoryToken, &$currentRoot, &$sidebarCategoriesTree, &$categoriesByToken, &$categoryProductCountsByToken, $tagCategoriesById) {
                foreach ($treeCategories as $treeCategory) {
                    if($treeCategory->isRoot()) {
                        $currentRoot = $treeCategory;
                    } elseif($treeCategory->isLeaf() && in_array($treeCategory->getId(), array_keys($tagCategoriesById))) {
                        if(!in_array($currentRoot->getToken(), $categoriesByToken)) {
                            $categoriesByToken[$currentRoot->getToken()] = $currentRoot;
                        }
                        if(!in_array($currentRoot->getToken(), array_keys($sidebarCategoriesTree))) {
                            $sidebarCategoriesTree[$currentRoot->getToken()] = [];
                        }
                        if(!in_array($treeCategory->getToken(), $categoriesByToken)) {
                            $categoriesByToken[$treeCategory->getToken()] = $treeCategory;
                        }
                        $this->addToken($sidebarCategoriesTree, $currentRoot->getToken(), $treeCategory->getToken());

                        $tagCategory = $tagCategoriesById[$treeCategory->getId()];
                        $treeCategory->setProductCount($tagCategory->getProductCount());
                        $categoryProductCountsByToken[$treeCategory->getToken()] = $tagCategory->getProductCount();
                        $categoryProductCountsByToken[$currentRoot->getToken()] = 
                            isset($categoryProductCountsByToken[$currentRoot->getToken()]) ? 
                                $categoryProductCountsByToken[$currentRoot->getToken()] + $tagCategory->getProductCount() : $tagCategory->getProductCount();
                    }

                    if(!empty($categoryToken) && $categoryToken == $treeCategory->getToken()) {
                        $category = $treeCategory;
                        $rootCategory = $currentRoot;
                    }

                    if ((bool)$treeCategory->getChild()) {
                        $walk($treeCategory->getChild());
                    }
                }
            };
            $walk($productCategoryRepository->getTreeCollection($region));

            // если категория не указана
            if (!$category) {
                $page = new \View\Tag\TagRootPage();
                $page->setParam('categoryProductCountsByToken', $categoryProductCountsByToken);
                $setPageParameters($page);

                return $this->tagRoot($page, $request);
            // если категория содержится во внешнем узле дерева
            } elseif ($category->isRoot()) {
                $page = new \View\Tag\RootPage();
                $page->setParam('categoryProductCountsByToken', $categoryProductCountsByToken);
                $page->setParam('category', $category);
                $page->setParam('rootCategory', $rootCategory);
                $setPageParameters($page);

                return $this->rootCategory($category, $productFilter, $page, $request);
            }

            $page = new \View\Tag\LeafPage();
            $page->setParam('categoryProductCountsByToken', $categoryProductCountsByToken);
            $page->setParam('category', $category);
            $page->setParam('rootCategory', $rootCategory);
            $setPageParameters($page);

            return $this->leafCategory($category, $productFilter, $page, $request);
        }
    */
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

        $productIds = [];
        $productCount = 0;
        $repository->prepareIteratorByFilter(
            $productFilter->dump(),
            $productSorting->dump(),
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
                    $products[] = new \Model\Product\CompactEntity($item);
                }
            });

            $scoreData = [];
            \RepositoryManager::review()->prepareScoreCollection($productIds, function($data) use (&$scoreData) {
                if (isset($data['product_scores'][0])) {
                    $scoreData = $data;
                }
            });
        }
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        \RepositoryManager::review()->addScores($products, $scoreData);

        $productPager = new \Iterator\EntityPager($products, $productCount);
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
    public function count($tagToken, $categoryPath = null, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        // vars
        $region = self::isGlobal() ? null : \App::user()->getRegion();
        $categoryToken = null;
        $category = null;
        $selectedCategory = null;


        // tag
        $tag = \RepositoryManager::tag()->getEntityByToken($tagToken);
        if (!$tag) {
            throw new \Exception\NotFoundException(sprintf('Тег @%s не найден', $tagToken));
        }
        if (!(bool)$tag->getCategory()) {
            throw new \Exception\NotFoundException(sprintf('Тег "%s" не связан ни с одной категорией', $tag->getToken()));
        }


        // category
        if ($categoryPath) {
            $categoryToken = explode('/', $categoryPath);
            $categoryToken = end($categoryToken);
        }
        $selectedCategory = $this->getSelectedCategoryByRequest($request); // Попробуем получить категорию из request

        if (!$selectedCategory && $categoryToken) { // Если категория текущая не определена, но указан токен категории

            // запрос сделаем, если токен указан, u не полученна категория выбранная
            \RepositoryManager::productCategory()->prepareEntityByToken($categoryToken, $region, function ($data) use (&$selectedCategory) {
                $data = reset($data);
                if ((bool)$data) {
                    $selectedCategory = new \Model\Product\Category\Entity($data);
                }
            });
            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['short']);

        }

        if (null === $category) {
            // Попробуем получить категорию из request
            $category = $this->getSelectedCategoryByRequest($request);
            /*if (!$category) {
                // категория в запросе не указана, берём из тега
                $categories = $tag->getCategory();
                $category = reset($categories);
                $category = \RepositoryManager::productCategory()->getEntityById($category->getId());
            }*/
        } else {
            // категория в урле указана, используем
            $categoryToken = explode('/', $categoryPath);
            $categoryToken = end($categoryToken);
            $category = \RepositoryManager::productCategory()->getEntityByToken($categoryToken);
        }
        /*if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара @%s не найдена.', $categoryToken));
        }*/ // нет категории - ну и ок. бывает.


        // фильтры
        $filters = [];

        if ($category) {
            try {
                $filters = \RepositoryManager::productFilter()->getCollectionByCategory($category, $region);
            } catch (\Exception $e) {
                \App::exception()->add($e);
                \App::logger()->error($e);
            }
        }

        // добавим id tag-a в фильтр
        $filter = new \Model\Product\Filter\Entity();
        $filter->setId('tag');
        $filter->setIsInList(false);

        $filters[] = $filter;


        // магазины
        $shop = null;
        try {
            if (!self::isGlobal() && \App::request()->get('shop') && \App::config()->shop['enabled']) {
                $shop = \RepositoryManager::shop()->getEntityById( \App::request()->get('shop') );
            }
        } catch (\Exception $e) {
            \App::logger()->error(sprintf('Не удалось отфильтровать товары по магазину #%s', \App::request()->get('shop')));
        }


        // Бренды
        $brand = null;


        // Product Filter
        $productFilter = new \Model\Product\Filter($filters, self::isGlobal(), self::inStore(), $shop);
        //$productFilter = $this->getFilter($filters, $category, $brand, $request, $shop); // old
        //$productFilter = (new \Controller\ProductCategory\Action())->getFilter($filters, $category, $brand, $request, $shop);

        $productFilter->setValue( 'tag', $tag->getId() );
        if (isset($selectedCategory)) {
           $productFilter->setCategory($selectedCategory);
        }

        $count = \RepositoryManager::product()->countByFilter($productFilter->dump());

        return new \Http\JsonResponse(array(
            'success' => true,
            'count'    => $count,
        ));
    }


    /**
     * @param \View\Layout                   $page
     * @param \Http\Request                  $request
     * @return \Http\Response
     */
    protected function tagRoot(\View\Layout $page, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->debug) \App::debug()->add('sub.act', 'Tag\\Action.tagRoot', 134);

        $page->setParam('sidebarHotlinks', true);

        $catalogJsonBulk = \RepositoryManager::productCategory()->getCatalogJsonBulk();
        $page->setParam('catalogJsonBulk', $catalogJsonBulk);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Model\Product\Category\TreeEntity $category
     * @param \Model\Product\Filter              $productFilter
     * @param \View\Layout                       $page
     * @param \Http\Request                      $request
     * @return \Http\Response
     * @throws \Exception
     */
    protected function rootCategory(\Model\Product\Category\TreeEntity $category, \Model\Product\Filter $productFilter, \View\Layout $page, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->debug) \App::debug()->add('sub.act', 'Tag\\Action.rootCategory', 134);

        // сортировка
        $productSorting = new \Model\Product\Sorting();

        $sidebarCategoriesTree = $page->getParam('sidebarCategoriesTree');
        $categoriesByToken = $page->getParam('categoriesByToken');
        $childTokens = (!empty($sidebarCategoriesTree[$category->getToken()]) && is_array($sidebarCategoriesTree[$category->getToken()]))
            ? array_keys($sidebarCategoriesTree[$category->getToken()])
            : [];

        // дочерние категории сгруппированные по идентификаторам
        $childrenById = [];
        foreach ($childTokens as $childToken) {
            $child = $categoriesByToken[$childToken];
            $childrenById[$child->getId()] = $child;
        }

        // листалки сгруппированные по идентификаторам категорий
        $limit = \App::config()->product['itemsInCategorySlider'] * 2;
        $repository = \RepositoryManager::product();
        $repository->setEntityClass('\\Model\\Product\\CompactEntity');
        // массив фильтров для каждой дочерней категории

        $filterData = array_map(function(\Model\Product\Category\TreeEntity $category) use ($productFilter) {
            $productFilter = clone $productFilter;
            $productFilter->setCategory($category);

            return $productFilter->dump();
        }, $childrenById);

        /** @var $child \Model\Product\Category\TreeEntity */
        $child = reset($childrenById);
        $productPagersByCategory = [];
        $productCount = 0;

        // TODO: сделать настройку для переключения иконки/линейки
        // следующее условие должно выполняться если линейки
        if(false) {
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
        $page->setParam('childrenById', $childrenById);

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
     * @param \Model\Product\Category\TreeEntity $category
     * @param \Model\Product\Filter          $productFilter
     * @param \View\Layout                   $page
     * @param \Http\Request                  $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    protected function leafCategory(\Model\Product\Category\TreeEntity $category, \Model\Product\Filter $productFilter, \View\Layout $page, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->debug) \App::debug()->add('sub.act', 'Tag\\Action.leafCategory', 134);

        $region = \App::user()->getRegion();

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
            $productIds = [];
            $productCount = 0;
            $repository->prepareIteratorByFilter(
                $productFilter->dump(),
                $page->getParam('sort'),
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
                        $products[] = new \Model\Product\CompactEntity($item);
                    }
                });

                $scoreData = [];
                \RepositoryManager::review()->prepareScoreCollection($productIds, function($data) use (&$scoreData) {
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
            $productPager = null;

            $productIds = [];
            $productCount = 0;
            $repository->prepareIteratorByFilter(
                $productFilter->dump(),
                $page->getParam('sort'),
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
                        $products[] = new \Model\Product\CompactEntity($item);
                    }
                });

                $scoreData = [];
                \RepositoryManager::review()->prepareScoreCollection($productIds, function($data) use (&$scoreData) {
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
     * @param \Model\Product\Category\TreeEntity $category
     * @param \Model\Brand\Entity|null       $brand
     * @param \Http\Request $request
     * @return \Model\Product\Filter
     */
    protected function getFilter(array $filters, \Model\Product\Category\TreeEntity $category, \Model\Brand\Entity $brand = null, \Http\Request $request, $shop = null) {
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
            if ((bool)$diff && (bool)$category) {
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


    /**
     * @param \Http\Request $request
     * @return \Model\Product\Category\Entity|null
     */
    private function getSelectedCategoryByRequest(\Http\Request $request)
    {
        $selectedCategory = null;
        $categoryId = $request->get('category');
        if ($categoryId) {
            $selectedCategory = \RepositoryManager::productCategory()->getEntityById($categoryId);
            //$categoryToken = $category->getToken();
        }
        return $selectedCategory;
    }

}