<?php

namespace Controller\Tag;

class Action {
    public function index($tagToken, \Http\Request $request, $categoryToken = null) {
        //\App::logger()->debug('Exec ' . __METHOD__);
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

        if ($selectedCategory) {
            $catalogJson = $selectedCategory->catalogJson;
        }

        /** @var $categoriesByToken \Model\Product\Category\Entity[] */
        $categoriesByToken = [];
        call_user_func(function() use(&$categoriesByToken, $tag, $region, $selectedCategory, $client) {
            $params = [
                'depth' => 0,
            ];

            if ($selectedCategory) {
                $params['root_id'] = $selectedCategory->getId();
                $params['depth'] = 1;
            }

            $categoryTreeData = null;
            \App::scmsClient()->addQuery('api/category/tree', $params + [
                'load_medias' => true,
            ], [], function($data) use(&$categoryTreeData) {
                $categoryTreeData = $data;
            });

            $availableCategoriesDataByUi = null;
            \App::searchClient()->addQuery('category/get-available', $params + [
                'filter'    => ['filters' => [['tag', 1, $tag->getId()]]],
                'region_id' => $region->getId(),
            ], [], function($data) use(&$availableCategoriesDataByUi) {
                $availableCategoriesDataByUi = [];
                if (is_array($data)) {
                    foreach ($data as $item) {
                        if (isset($item['uid'])) {
                            $availableCategoriesDataByUi[$item['uid']] = $item;
                        }
                    }
                }
            });

            $client->execute();

            if (is_array($categoryTreeData)) {
                if ($selectedCategory && isset($categoryTreeData[0]['children'])) {
                    $categoryTreeData = $categoryTreeData[0]['children'];
                }

                foreach ($categoryTreeData as $item) {
                    if (isset($item['uid']) && isset($availableCategoriesDataByUi[$item['uid']])) {
                        $category = new \Model\Product\Category\Entity($item);

                        if (isset($availableCategoriesDataByUi[$item['uid']]['product_count'])) {
                            $category->setProductCount($availableCategoriesDataByUi[$item['uid']]['product_count']);
                        }

                        $categoriesByToken[$category->getToken()] = $category;
                    }
                }
            }
        });


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

        $productFilter = \RepositoryManager::productFilter()->createProductFilter($filters, $selectedCategory, $brand, $request, $shop);
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

        // листалка
        $limit = \App::config()->product['itemsPerPage'];
        $repository = \RepositoryManager::product();

        /** @var \Model\Product\Entity[] $products */
        $products = [];
        $productCount = 0;
        $repository->prepareIteratorByFilter(
            $productFilter->dump(),
            $sort,
            ($pageNum - 1) * $limit,
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

        $page = new \View\Tag\IndexPage();
        $page->setParam('productPager', $productPager);
        $page->setParam('productFilter', $productFilter);
        $page->setParam('selectedFilter', $selectedFilter);
        $page->setParam('productSorting', $productSorting);
        $page->setParam('tag', $tag);
        $page->setParam('productFilter', $productFilter);
        $page->setParam('productSorting', $productSorting);
        $page->setParam('sort', $sort);
        $page->setParam('selectedCategory', $selectedCategory);
        $page->setParam('categories', array_values($categoriesByToken));
        $page->setParam('productView', $selectedCategory ? $request->get('view', $selectedCategory->getProductView()) : \Model\Product\Category\Entity::PRODUCT_VIEW_COMPACT);
        return new \Http\Response($page->show());
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
    private function getSelectedCategoryByRequest(\Http\Request $request) {
        $categoryId = $request->get('category');
        if ($categoryId) {
            return \RepositoryManager::productCategory()->getEntityById($categoryId);
        }
        return null;
    }
}