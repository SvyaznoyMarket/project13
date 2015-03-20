<?php

namespace Controller\Tag;

class Action {
    public function index($tagToken, \Http\Request $request, $categoryToken = null) {
        \App::logger()->debug('Exec ' . __METHOD__);
        $client = \App::coreClientV2();
        /** @var $region \Model\Region\Entity */
        $region = \App::user()->getRegion();

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
        //$tagCategories = $tag->getCategory();
        /** @var $categoriesByToken \Model\Product\Category\Entity[] */
        $categoriesByToken = [];
        $categories = [];

        $selectedCategory = $this->getSelectedCategoryByRequest($request); // Попробуем получить категорию из request

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

        \App::searchClient()->addQuery('category/tree', $queryParams, [],
            function ($data) use (&$categories, &$tagCategoriesNumbers) {
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
            if (\App::request()->get('shop') && \App::config()->shop['enabled']) {
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

        // SITE-4734
        foreach ($productFilter->getFilterCollection() as $filter) {
            if ('brand' === $filter->getId()) {
                foreach ($filter->getOption() as $option) {
                    $option->setImageUrl('');
                }

                break;
            }
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
        }
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        if ((bool)$products) {
            $productUIs = [];
            foreach ($products as $product) {
                if (!$product instanceof \Model\Product\BasicEntity) continue;
                $productUIs[] = $product->getUi();
            }

            \RepositoryManager::review()->prepareScoreCollectionByUi($productUIs, function($data) use(&$products) {
                if (isset($data['product_scores'][0])) {
                    \RepositoryManager::review()->addScores($products, $data);
                }
            });
        }
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        $productPager = new \Iterator\EntityPager($products, $productCount);
        $productPager->setPage($pageNum);
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
        //if ($shop) $templating->setParam('shop', $shop);
        $helper = $templating->getParam('helper');
        $selectedFilter = (new \View\ProductCategory\SelectedFilterAction())->execute(
            $helper,
            $productFilter
        );

        // ajax
        if ($request->isXmlHttpRequest() && 'true' == $request->get('ajax')) {
            return new \Http\JsonResponse([
                'list'           => (new \View\Product\ListAction())->execute(
                    $helper,
                    $productPager,
                    []
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
        $page->setParam('categoriesByToken', $categoriesByToken);
        $page->setParam('productView', $productView);
        return new \Http\Response($page->show());
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

        $region = \App::user()->getRegion();
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
                if ($data && is_array($data)) {
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
            if (\App::request()->get('shop') && \App::config()->shop['enabled']) {
                $shop = \RepositoryManager::shop()->getEntityById( \App::request()->get('shop') );
            }
        } catch (\Exception $e) {
            \App::logger()->error(sprintf('Не удалось отфильтровать товары по магазину #%s', \App::request()->get('shop')));
        }


        // Бренды
        $brand = null;


        // Product Filter
        $productFilter = new \Model\Product\Filter($filters, $shop);

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