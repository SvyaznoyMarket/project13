<?php

namespace Controller\Slice;

use Controller\Product\SetAction;
use Model\Product\Category\Entity;
use EnterApplication\CurlTrait;
use EnterQuery as Query;

class ShowAction {
    use CurlTrait;

    /**
     * @param \Http\Request $request
     * @param string        $sliceToken
     * @param string|null   $categoryToken
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function execute(\Http\Request $request, $sliceToken, $categoryToken = null) {
        //\App::logger()->debug('Exec ' . __METHOD__);

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
        $sliceRequestFilters = [];
        parse_str($slice->getFilterQuery(), $sliceRequestFilters);

        if (count($sliceRequestFilters) == 1 && !empty($sliceRequestFilters['barcode'])) {
            return (new SetAction())->execute(
                !empty($sliceRequestFilters['barcode'][0]) ? join(',', $sliceRequestFilters['barcode']) : $sliceRequestFilters['barcode'], // поддержка как barcode=2060103001326,2060103001814 так и barcode[]=2060103001326&barcode[]=2060103001814
                $request,
                $slice->getName()
            );
        }

        $pageNum = $this->getPageNum();
        $productSorting = $this->getSorting();
        $shop = $this->getShop();
        $region = $this->getRegion(isset($sliceRequestFilters['region']) ? $sliceRequestFilters['region'] : null);
        $category = $this->getCategory($categoryToken, $slice, $region);
        $sliceFiltersForSearchClientRequest = \RepositoryManager::slice()->getSliceFiltersForSearchClientRequest($slice, $category->getId() ? true : false);

        $categoryTreeData = [];
        $availableCategoriesDataByUi = [];
        call_user_func(function() use($category, $sliceToken, $sliceFiltersForSearchClientRequest, $region, &$categoryTreeData, &$availableCategoriesDataByUi) {
            $rootId = $category->getId() ? $category->getId() : ($category->getParentId() ? $category->getParentId() : 0);
            $depth = $category->getId() ? $category->getLevel() : 0;

            \App::scmsClient()->addQuery('api/category/tree', [
                'root_id'         => $rootId,
                'depth'           => $depth,
                'load_parents'    => true,
                'load_medias'     => true,
            ], [], function($data) use(&$categoryTreeData) {
                $categoryTreeData = $data;
            });

            \App::searchClient()->addQuery('category/get-available', [
                'root_id'         => $rootId,
                'depth'           => $depth,
                'is_load_parents' => true,
                'filter'          => ['filters' => $sliceFiltersForSearchClientRequest],
                'region_id'       => $region->getId(),
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
        });

        $this->prepareProductFilter($filters, $category, $sliceFiltersForSearchClientRequest, $region);
        /** @var \Model\Product\Category\Entity[] $sliceCategories */
        $sliceCategories = [];
        if (!$request->isXmlHttpRequest()) {
            // категории в фильтре среза, например { slice.filter: category[]=1239&category[]=1245&category[]=1232&category[]=1216&category[]=1224&category[]=1209&category[]=338 }
            $this->prepareSliceCategories($slice, $region, $sliceCategories);
        }

        \App::coreClientV2()->execute();

        call_user_func(function() use($category, $sliceToken, $region, $categoryTreeData, $availableCategoriesDataByUi) {
            if ($this->isSeoSlice()) {
                // SITE-5432
                $changeCategoryUrlToSliceUrl = function() {};
            } else {
                $helper = new \Helper\TemplateHelper();
                $changeCategoryUrlToSliceUrl = function(\Model\Product\Category\Entity $category) use($sliceToken, $helper) {
                    $url = explode('/', $category->getLink());
                    $url = $helper->url('slice.category', ['sliceToken' => $sliceToken, 'categoryToken' => end($url)]);
                    $category->setLink($url);
                };
            }

            /**
             * Загрузка дочерних и родительских узлов категории
             *
             * @param \Model\Product\Category\Entity $category
             * @param array $data
             */
            $loadBranch = function(\Model\Product\Category\Entity $category, array $data) use($changeCategoryUrlToSliceUrl, $availableCategoriesDataByUi) {
                if (!isset($data['uid']) || !isset($availableCategoriesDataByUi[$data['uid']])) {
                    return;
                }

                if (isset($availableCategoriesDataByUi[$data['uid']]['product_count'])) {
                    $category->setProductCount($availableCategoriesDataByUi[$data['uid']]['product_count']);
                }

                // добавляем дочерние узлы
                if (isset($data['children']) && is_array($data['children'])) {
                    foreach ($data['children'] as $childData) {
                        if (isset($childData['uid']) && isset($availableCategoriesDataByUi[$childData['uid']])) {
                            $child = new \Model\Product\Category\Entity($childData);
                            $changeCategoryUrlToSliceUrl($child);
                            $category->addChild($child);
                        }
                    }
                }

                // если категория не выбрана, выводим рутовые категории
                if (!$category->getId()) {
                    $child = new \Model\Product\Category\Entity($data);
                    $changeCategoryUrlToSliceUrl($child);
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
            $iterateLevel = function($data) use(&$iterateLevel, &$loadBranch, $category, $availableCategoriesDataByUi) {
                if (!is_array($data)) {
                    return;
                }

                $item = reset($data);
                if (!$item || !isset($item['uid']) || !isset($availableCategoriesDataByUi[$item['uid']])) {
                    return;
                }

                $level = (int)$item['level'];
                if ($level < $category->getLevel()) {
                    // если текущий уровень меньше уровня категории, загружаем данные для предков и прямого родителя категории
                    $ancestor = new \Model\Product\Category\Entity($item);
                    if (1 == $category->getLevel() - $level) {
                        $loadBranch($ancestor, $item);
                        $category->setParent($ancestor);
                    }

                    $category->addAncestor($ancestor);
                } else if ($level == $category->getLevel()) {
                    // если текущий уровень равен уровню категории, пробуем найти данные для категории
                    foreach ($data as $item) {
                        // ура, наконец-то наткнулись на текущую категорию
                        if (isset($item['uid']) && isset($availableCategoriesDataByUi[$item['uid']]) && $item['id'] == $category->getId() || !$category->getId()) {
                            $loadBranch($category, $item);
                            // SITE-2444
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

            $iterateLevel($categoryTreeData);

            // переделываем url для breadcrumbs
            foreach ($category->getAncestor() as $ancestor) {
                $changeCategoryUrlToSliceUrl($ancestor);
            }

            $changeCategoryUrlToSliceUrl($category);
        });

        // если есть категории в фильтре среза
        if ($sliceCategories) {
            $availableCategoryQuery = new Query\Product\Category\GetAvailable();
            $availableCategoryQuery->regionId = $region->getId();
            $availableCategoryQuery->rootCriteria = $category->id ? ['id' => $category->id] : [];
            //$availableCategoryQuery->depth = 1;
            $availableCategoryQuery->filterData = call_user_func(function() use ($sliceFiltersForSearchClientRequest) {
                foreach ($sliceFiltersForSearchClientRequest as $i => $item) {
                    if (isset($item[0]) && ('category' === $item[0])) {
                        unset($sliceFiltersForSearchClientRequest[$i]); // TODO: убрать как только будет готова SPPX-259
                    }
                }

                return $sliceFiltersForSearchClientRequest;
            });
            $availableCategoryQuery->prepare();

            $this->getCurl()->execute();

            $availableCategoryUis = [];
            if (!$availableCategoryQuery->error) {
                foreach ($availableCategoryQuery->response->categories as $item) {
                    if (!isset($item['product_count']) || !$item['product_count'] || !isset($item['uid'])) continue;
                    $availableCategoryUis[$item['uid']] = true;
                }

                $level = 0;
                /**
                 * @param \Model\Product\Category\Entity[] $categories
                 * @return \Model\Product\Category\Entity[]
                 */
                $filter = function($categories) use (&$filter, &$level, &$availableCategoryUis) {
                    $level++;
                    if ($level > 6) return []; // защита от чрезмерного глубокого погружения

                    foreach ($categories as $i => $category) {
                        if (!isset($availableCategoryUis[$category->ui])) {
                            unset($categories[$i]);
                        }

                        if ($children = $category->getChild()) {
                            $category->setChild($filter($children));
                        }
                    }

                    return $categories;
                };

                $category->setChild($filter($category->id ? $category->getChild() : $sliceCategories));
            }
        }

        $productFilter = \RepositoryManager::productFilter()->createProductFilter($filters, $category->getId() ? $category : null, null, $request, $shop);
        $productPager = $this->getProductPager($productFilter, $sliceFiltersForSearchClientRequest, $productSorting, $pageNum, $region);
        $category->setProductCount($productPager->count());

        call_user_func(function() use(&$category) {
            $userChosenCategoryView = \App::request()->cookies->get('categoryView');

            if (
                (!$category->config->listingDisplaySwitch && $category->config->listingDefaultView->isList)
                || (
                    $category->config->listingDisplaySwitch
                    && (
                        $userChosenCategoryView === 'expanded'
                        || ($category->config->listingDefaultView->isList && $userChosenCategoryView == '')
                    )
                )
            ) {
                $category->listingView->isList = true;
                $category->listingView->isMosaic = false;
            } else {
                $category->listingView->isList = false;
                $category->listingView->isMosaic = true;
            }
        });

        if ($productPager->getPage() > $productPager->getLastPage()) {
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([
                'page' => $productPager->getLastPage(),
            ]));
        }

        // SITE-5770
        $cartButtonSender = [];
        if ('all_labels' === $slice->getToken()) {
            $cartButtonSender = [
                'from'     => $request->getUri(),
                'position' => 'Listing',
            ];
        }

        if ($request->isXmlHttpRequest() && 'true' == $request->get('ajax')) {
            return new \Http\JsonResponse([
                'list'           => (new \View\Product\ListAction())->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productPager,
                    [],
                    $slice->getProductBuyMethod(),
                    $slice->getShowProductState(),
                    4,
                    'compact',
                    $cartButtonSender
                ),
                'selectedFilter' => (new \View\ProductCategory\SelectedFilterAction())->execute(
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
                    'title' => $slice->getName()
                ],
                'countProducts'  => $productPager->count(),
            ]);
        }

        $page = new \View\Slice\ShowPage();
        $page->setParam('category', $category);

        if ($slice->categoryUid) {
            $page->setParam('seoContent', $slice->getContent());
        } else {
            $page->setParam('hotlinks', $category->getSeoHotlinks());

            $seoContent = $slice->getContent();

            if (!$seoContent) {
                $seoContent = $category->getSeoContent();
            }

            if ($pageNum > 1) {
                $seoContent = '';
            }

            $page->setParam('seoContent', $seoContent);
        }

        $page->setParam('slice', $slice);
        $page->setParam('productPager', $productPager);
        $page->setParam('productSorting', $productSorting);
        $page->setParam('productFilter', $productFilter);
        $page->setParam('productView', $request->get('view', $category->getProductView()));
        $page->setParam('hasCategoryChildren', !$this->isSeoSlice()); // SITE-3558
        $page->setParam('cartButtonSender', $cartButtonSender);
        $page->setParam('sliceCategories', $sliceCategories);
        $page->setGlobalParam('shop', $shop);

        return new \Http\Response($page->show());
    }

    /**
     * @param string|int $regionFilterValue
     * @return \Model\Region\Entity
     */
    private function getRegion($regionFilterValue) {
        if (!empty($regionFilterValue)) {
            $region = \RepositoryManager::region()->getEntityById((int)$regionFilterValue);
            if ($region) {
                return $region;
            }
        }

        return \App::user()->getRegion();
    }

    /**
     * @param string $categoryToken
     * @return \Model\Product\Category\Entity
     */
    private function getCategory($categoryToken, \Model\Slice\Entity $slice, \Model\Region\Entity $region = null) {
        $category = new \Model\Product\Category\Entity(['level' => 1]);

        if ($categoryToken) {
            \RepositoryManager::productCategory()->prepareEntityByToken($categoryToken, $region, function ($data) use (&$category) {
                if ($data && is_array($data)) {
                    $category = new \Model\Product\Category\Entity($data);
                }
            });

            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['short']);
        } else if ($slice->categoryUid) {
            $result = \RepositoryManager::productCategory()->getEntityByUid($slice->categoryUid);
            if ($result) {
                $category = $result;
            }
        }

        return $category;
    }

    /**
     * @param \Model\Slice\Entity $slice
     * @param \Model\Region\Entity $region
     * @param \Model\Product\Category\Entity[] $categories
     */
    private function prepareSliceCategories(\Model\Slice\Entity $slice, \Model\Region\Entity $region, &$categories = []) {
        parse_str($slice->getFilterQuery(), $requestFilters);

        $categoryIds = isset($requestFilters['category'][0]) ? (array)$requestFilters['category'] : [];
        if (!$categoryIds) {
            return;
        }

        \RepositoryManager::productCategory()->prepareCollectionById($categoryIds, $region, function ($data) use (&$slice, &$categories) {
            if (!isset($data[0])) return;

            $router = \App::router();

            foreach ($data as $item) {
                if (!isset($item['uid'])) continue;

                $category = new \Model\Product\Category\Entity($item);
                $category->setLink($router->generate('slice.category', ['sliceToken' => $slice->getToken(), 'categoryToken' => $category->getToken()]));

                $categories[] = $category;
            }
        });
    }

    /**
     * @return \Model\Shop\Entity|null
     */
    private function getShop() {
        $shopId = \App::request()->get('shop');
        try {
            if ($shopId && \App::config()->shop['enabled']) {
                return \RepositoryManager::shop()->getEntityById($shopId);
            }
        } catch (\Exception $e) {
            \App::logger()->error(sprintf('Не удалось отфильтровать товары по магазину #%s', $shopId));
        }

        return null;
    }

    /**
     * @return int
     */
    private function getPageNum() {
        $page = (int)\App::request()->get('page', 1);

        if ($page < 1) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $page));
        }

        return $page;
    }

    /**
     * @return \Model\Product\Sorting
     */
    private function getSorting() {
        $sorting = new \Model\Product\Sorting();
        list($sortingName, $sortingDirection) = array_pad(explode('-', \App::request()->get('sort')), 2, null);
        $sorting->setActive($sortingName, $sortingDirection);
        return $sorting;
    }

    private function hasFilterInFiltersForSearchClientRequest($expectedFilterToken, array $filters) {
        foreach ($filters as $filter) {
            if (isset($filter[0]) && $filter[0] === $expectedFilterToken) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $pageNum
     * @return \Iterator\EntityPager
     */
    private function getProductPager(\Model\Product\Filter $productFilter, array $sliceFiltersForSearchClientRequest, \Model\Product\Sorting $productSorting, $pageNum, \Model\Region\Entity $region = null) {
        $productCount = 0;
        /** @var \Model\Product\Entity[] $products */
        $products = [];
        $productRepository = \RepositoryManager::product();
        $limit = \App::config()->product['itemsPerPage'];

        $productRepository->prepareIteratorByFilter(array_merge($productFilter->dump(), $sliceFiltersForSearchClientRequest), $productSorting->dump(), ($pageNum - 1) * $limit, $limit, $region, function ($data) use (&$products, &$productCount) {
            if (isset($data['list'][0])) {
                $products = array_map(function($productId) { return new \Model\Product\Entity(['id' => $productId]); }, $data['list']);
            }

            if (isset($data['count'])) {
                $productCount = (int)$data['count'];
            }
        });

        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        $productRepository->prepareProductQueries($products, 'model media property label brand category');
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        \RepositoryManager::review()->prepareScoreCollection($products, function($data) use(&$products) {
            if (isset($data['product_scores'][0])) {
                \RepositoryManager::review()->addScores($products, $data);
            }
        });

        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        $productPager = new \Iterator\EntityPager($products, $productCount);
        $productPager->setPage($pageNum);
        $productPager->setMaxPerPage($limit);
        return $productPager;
    }

    private function prepareProductFilter(&$filters, Entity $category, array $sliceFiltersForSearchClientRequest, \Model\Region\Entity $region = null) {
        $filters = [];
        \RepositoryManager::productFilter()->prepareCollectionByCategory($category->getId() ? $category : null, $region, $sliceFiltersForSearchClientRequest, function($data) use (&$filters, $sliceFiltersForSearchClientRequest) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    $filter = new \Model\Product\Filter\Entity($item);
                    if (!$this->hasFilterInFiltersForSearchClientRequest($filter->getId(), $sliceFiltersForSearchClientRequest)) {
                        $filters[] = $filter;
                    }
                }
            }
        });
    }

    private function isSeoSlice() {
        return in_array(\App::request()->get('route'), ['product.category.slice']);
    }
}