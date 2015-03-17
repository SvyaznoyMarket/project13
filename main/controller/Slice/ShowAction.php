<?php

namespace Controller\Slice;

use Controller\Product\SetAction;

class ShowAction {
    /**
     * @param \Http\Request $request
     * @param string        $sliceToken
     * @param string|null   $categoryToken
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function execute(\Http\Request $request, $sliceToken, $categoryToken = null) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $region = \App::user()->getRegion();

        $helper = new \Helper\TemplateHelper();

        /** @var $slice \Model\Slice\Entity|null */
        $slice = null;
        \RepositoryManager::slice()->prepareEntityByToken(
            $sliceToken,
            function($data) use (&$slice, $sliceToken) {
                if (is_array($data) && $data) {
                    $data['token'] = $sliceToken;
                    $slice = new \Model\Slice\Entity($data);
                }
            },
            function (\Exception $e) {
                \App::exception()->remove($e);
            }
        );

        \App::scmsSeoClient()->execute();

        if (!$slice) {
            throw new \Exception\NotFoundException(sprintf('Срез @%s не найден', $sliceToken));
        }

        // добывание фильтров из среза
        $requestData = [];
        parse_str($slice->getFilterQuery(), $requestData);

        // Если в слайсе только список баркодов
        if ((count(array_keys($requestData)) == 1) && !empty($requestData['barcode'])) {
            return (new SetAction())->execute(
                (is_array($requestData['barcode']) && isset($requestData['barcode'][1])) ? join(',', $requestData['barcode']) : $requestData['barcode'], // поддержка как barcode=2060103001326,2060103001814 так и barcode[]=2060103001326&barcode[]=2060103001814
                $request,
                $slice->getName()
            );
        }

        // region
        if (!empty($requestData['region'])) {
            $region = \RepositoryManager::region()->getEntityById((int)$requestData['region']);
        }

        // фильтры среза
        $filterData = $this->getSliceFilters($slice);

        // если в слайсе задан category_uid, то отображаем листинг данной категории
        if ($slice->categoryUid) {
            $productCategoryRepository = \RepositoryManager::productCategory();
            $category = $productCategoryRepository->getEntityByUid($slice->categoryUid);

            if ($category) {
                // запрашиваем дерево категорий
                $productCategoryRepository->prepareEntityBranch($category->getId(), $category, $region);

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

        // подготовка 2-го пакета запросов

        $category = new \Model\Product\Category\Entity();
        if ($categoryToken) {
            \RepositoryManager::productCategory()->prepareEntityByToken($categoryToken, $region, function($data) use (&$category) {
                if ($data && is_array($data)) {
                    $category = new \Model\Product\Category\Entity($data);
                }
            });
        }

        // выполнение 2-го пакета запросов
        $client->execute(\App::config()->coreV2['retryTimeout']['short']);

        // запрашиваем дерево категорий
        //\RepositoryManager::productCategory()->prepareEntityBranch($category, $region);
        if (!$category->getId()) {
            $category->setLevel(1);
        }

        $params = [
            'root_id'         => $category->getId() ? $category->getId() : ($category->getParentId() ? $category->getParentId() : 0),
            'max_level'       => $category->getId() ? $category->getLevel() + 1 : 1,
            'is_load_parents' => true,
            'filter' => ['filters' => $filterData],
        ];

        if ($region) {
            $params['region_id'] = $region->getId();
        }

        \App::searchClient()->addQuery('category/tree', $params, [], function($data) use (&$category, &$region, $sliceToken, $helper) {
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

                // добавляем дочерние узлы
                if (isset($data['children']) && is_array($data['children'])) {
                    foreach ($data['children'] as $childData) {
                        if (is_array($childData)) {
                            $child = new \Model\Product\Category\Entity($childData);
                            // переделываем url для дочерних категорий
                            $url = explode('/', $child->getLink());
                            $url = $helper->url('slice.category', ['sliceToken' => $sliceToken, 'categoryToken' => end($url)]);
                            $child->setLink($url);

                            $category->addChild($child);
                        }
                    }
                }

                // если категория не выбрана, выводим рутовые категории
                if (!$category->getId()) {
                    if (is_array($data)) {
                        $child = new \Model\Product\Category\Entity($data);
                        // переделываем url для категорий
                        $url = explode('/', $child->getLink());
                        $url = $helper->url('slice.category', ['sliceToken' => $sliceToken, 'categoryToken' => end($url)]);
                        $child->setLink($url);

                        $category->addChild($child);
                    }
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
                if (!is_array($data)) {
                    return;
                }

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

        // выполнение 3-го пакета запросов
        $client->execute();

        // если в catalogJson'e указан category_class, то обрабатываем запрос соответствующим контроллером
        $categoryClass = !empty($catalogJson['category_class']) ? strtolower(trim((string)$catalogJson['category_class'])) : null;

        // поддержка GET-запросов со старыми фильтрами
        if (!$categoryClass && is_array($request->get(\View\Product\FilterForm::$name)) && (bool)$request->get(\View\Product\FilterForm::$name)) {
            return new \Http\RedirectResponse(\App::router()->generate('product.category', ['categoryPath' => $category->getPath()]));
        }

        $shop = null;
        try {
            if (\App::request()->get('shop') && \App::config()->shop['enabled']) {
                $shop = \RepositoryManager::shop()->getEntityById( \App::request()->get('shop') );
            }
        } catch (\Exception $e) {
            \App::logger()->error(sprintf('Не удалось отфильтровать товары по магазину #%s', \App::request()->get('shop')));
        }

        // получаем из json данные о горячих ссылках и content
        try {
            $hotlinks = $category->getSeoHotlinks();
            $seoContent = $slice->getContent();
            if (!$seoContent) {
                $seoContent = $category->getSeoContent();
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

        // переделываем url для breadcrumbs
        foreach ($category->getAncestor() as $ancestor) {
            $this->changeCategoryUrlToSliceUrl($ancestor, $sliceToken);
        }

        $this->changeCategoryUrlToSliceUrl($category, $sliceToken);

        $setPageParameters = function(\View\Layout $page) use (
            &$category,
            &$regionsToSelect,
            &$hotlinks,
            &$seoContent,
            &$catalogJson,
            &$shop,
            &$slice
        ) {
            $page->setParam('category', $category);
            $page->setParam('regionsToSelect', $regionsToSelect);
            $page->setParam('hotlinks', $hotlinks);
            $page->setParam('seoContent', $seoContent);
            $page->setParam('catalogJson', $catalogJson);
            $page->setParam('slice', $slice);
            $page->setGlobalParam('shop', $shop);
        };

        $page = new \View\Slice\ShowPage();
        $setPageParameters($page);

        return $this->leafCategory($category, $page, $request, $filterData, $region, $slice);
    }

    private function changeCategoryUrlToSliceUrl(\Model\Product\Category\Entity $category, $sliceToken) {
        $url = explode('/', $category->getLink());
        $url = (new \Helper\TemplateHelper())->url('slice.category', ['sliceToken' => $sliceToken, 'categoryToken' => end($url)]);
        $category->setLink($url);
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
    protected function leafCategory(\Model\Product\Category\Entity $category, \View\Layout $page, \Http\Request $request, $filterData, \Model\Region\Entity $region = null, \Model\Slice\Entity $slice) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->debug) \App::debug()->add('sub.act', 'ProductCategory\\Action.leafCategory', 134);

        if (!$region) {
            $region = \App::user()->getRegion();
        }

        $pageNum = (int)$request->get('page', 1);
        if ($pageNum < 1) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $pageNum));
        }

        // сортировка
        $productSorting = new \Model\Product\Sorting();
        list($sortingName, $sortingDirection) = array_pad(explode('-', $request->get('sort')), 2, null);
        $productSorting->setActive($sortingName, $sortingDirection);

        $sort = $productSorting->dump();

        // вид товаров
        $productView = $request->get('view', $category->getHasLine() ? 'line' : $category->getProductView());
        // листалка
        $limit = \App::config()->product['itemsPerPage'];
        $repository = \RepositoryManager::product();

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

            $repository->prepareProductsMedias($products);

            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

            $pagerAll = new \Iterator\EntityPager($products, $productCount);
            $page->setGlobalParam('allCount', $pagerAll->count());
        }

        if (!empty($pagerAll)) {
            $productPager = $pagerAll;
        } else {
            $productIds = [];
            $productCount = 0;

            // проверка наличия ид товаров
            if (!(bool)$filterData && (false !== strpos($slice->getFilterQuery(), 'product'))) {
                parse_str($slice->getFilterQuery(), $productIds);
                $productIds = isset($productIds['product'][0]) ? $productIds['product'] : [];
            }

            // добавляем фильтр по категории
            if ($category->getId()) {
                $filterData[] = ['category', 1, [$category->getId()]];
            }

            $productPager = null;

            // если есть баркоды товаров, то
            if ((bool)$productIds) {
                $productCount = count($productIds);
            } else {
                $repository->prepareIteratorByFilter($filterData, $sort, ($pageNum - 1) * $limit, $limit, $region,
                    function ($data) use (&$productIds, &$productCount) {
                        if (isset($data['list'][0])) $productIds = $data['list'];
                        if (isset($data['count'])) $productCount = (int)$data['count'];
                    }
                );
            }

            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

            $products = [];
            if ((bool)$productIds) {
                $repository->prepareCollectionById($productIds, $region, function($data) use (&$products) {
                    foreach ((array)$data as $item) {
                        if (!isset($item['id'])) continue;

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

            $repository->prepareProductsMedias($products);

            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

            $productPager = new \Iterator\EntityPager($products, $productCount);
        }

        $productPager->setPage($pageNum);
        $productPager->setMaxPerPage($limit);
        $category->setProductCount($productPager->count());

        // проверка на максимально допустимый номер страницы
        if (($productPager->getPage() - $productPager->getLastPage()) > 0) {
            //throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $productPager->getPage()));
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([
                'page' => $productPager->getLastPage(),
            ]));
        }

        // ajax
        if ($request->isXmlHttpRequest() && 'true' == $request->get('ajax')) {
            return new \Http\JsonResponse([
                'list'           => (new \View\Product\ListAction())->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productPager,
                    !empty($catalogJson['bannerPlaceholder']) ? $catalogJson['bannerPlaceholder'] : [],
                    $slice->getProductBuyMethod(),
                    $slice->getShowProductState()
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
                    'title'     => $slice->getName()
                ],
            ]);
        }

        $page->setParam('productPager', $productPager);
        $page->setParam('productSorting', $productSorting);
        $page->setParam('productView', $productView);
        $page->setParam('hasCategoryChildren', in_array($request->get('route'), ['slice.show', 'slice.category']));

        return new \Http\Response($page->show());
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
            } else if ('category' == $k) {
                $values['category'] = $v;
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
            if ('f-segment' == $k) {
                $filterData[] = ['segment', 4, $v];
            } else if ('text' === $k) {
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