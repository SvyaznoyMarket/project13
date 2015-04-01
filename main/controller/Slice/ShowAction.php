<?php

namespace Controller\Slice;

use Controller\Product\SetAction;
use Model\Product\Category\Entity;

class ShowAction {
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
                isset($sliceRequestFilters['barcode'][1]) ? join(',', $sliceRequestFilters['barcode']) : $sliceRequestFilters['barcode'], // поддержка как barcode=2060103001326,2060103001814 так и barcode[]=2060103001326&barcode[]=2060103001814
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

        $this->prepareEntityBranch($category, $sliceToken, $sliceFiltersForSearchClientRequest, $region);
        $this->prepareProductFilter($filters, $category, $sliceFiltersForSearchClientRequest, $region);
        \App::coreClientV2()->execute();

        $productFilter = \RepositoryManager::productFilter()->createProductFilter($filters, $category->getId() ? $category : null, null, $request, $shop);
        $productPager = $this->getProductPager($productFilter, $sliceFiltersForSearchClientRequest, $productSorting, $pageNum, $region);
        $category->setProductCount($productPager->count());

        if ($productPager->getPage() > $productPager->getLastPage()) {
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([
                'page' => $productPager->getLastPage(),
            ]));
        }

        if ($request->isXmlHttpRequest() && 'true' == $request->get('ajax')) {
            return new \Http\JsonResponse([
                'list'           => (new \View\Product\ListAction())->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productPager,
                    [],
                    $slice->getProductBuyMethod(),
                    $slice->getShowProductState()
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
                    'title'      => $slice->getName()
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
        $page->setParam('productView', $request->get('view', $category->getHasLine() ? 'line' : $category->getProductView()));
        $page->setParam('hasCategoryChildren', in_array($request->get('route'), ['slice.show', 'slice.category'])); // SITE-3558
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

            if (!$region) {
                $region = \App::user()->getRegion();
            }

            return $region;
        } else {
            return \App::user()->getRegion();
        }
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
        $productIds = [];
        $productCount = 0;
        /** @var \Model\Product\Entity[] $products */
        $products = [];
        $productRepository = \RepositoryManager::product();
        $limit = \App::config()->product['itemsPerPage'];

        $productRepository->prepareIteratorByFilter(array_merge($productFilter->dump(), $sliceFiltersForSearchClientRequest), $productSorting->dump(), ($pageNum - 1) * $limit, $limit, $region, function ($data) use (&$productIds, &$productCount) {
            if (isset($data['list'][0])) {
                $productIds = $data['list'];
            }

            if (isset($data['count'])) {
                $productCount = (int)$data['count'];
            }
        }
        );

        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        if ($productIds) {
            $productRepository->prepareCollectionById($productIds, $region, function($data) use (&$products) {
                if (is_array($data)) {
                    foreach ($data as $item) {
                        $products[] = new \Model\Product\Entity($item);
                    }
                }
            });
        }

        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        if ($products) {
            $productUis = [];
            foreach ($products as $product) {
                $productUis[] = $product->getUi();
            }

            \RepositoryManager::review()->prepareScoreCollectionByUi($productUis, function($data) use(&$products) {
                if (isset($data['product_scores'][0])) {
                    \RepositoryManager::review()->addScores($products, $data);
                }
            });
        }

        $productRepository->prepareProductsMedias($products);

        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        $productPager = new \Iterator\EntityPager($products, $productCount);
        $productPager->setPage($pageNum);
        $productPager->setMaxPerPage($limit);
        return $productPager;
    }

    private function prepareEntityBranch(Entity $category, $sliceToken, array $filters = [], \Model\Region\Entity $region = null) {
        $params = [
            'root_id'         => $category->getId() ? $category->getId() : ($category->getParentId() ? $category->getParentId() : 0),
            'max_level'       => $category->getId() ? $category->getLevel() + 1 : 1,
            'is_load_parents' => true,
            'filter' => ['filters' => $filters],
        ];

        if ($region) {
            $params['region_id'] = $region->getId();
        }

        \App::searchClient()->addQuery('category/tree', $params, [], function($data) use (&$category, &$region, $sliceToken) {
            $helper = new \Helper\TemplateHelper();

            $changeCategoryUrlToSliceUrl = function(\Model\Product\Category\Entity $category) use($sliceToken, $helper) {
                $url = explode('/', $category->getLink());
                $url = $helper->url('slice.category', ['sliceToken' => $sliceToken, 'categoryToken' => end($url)]);
                $category->setLink($url);
            };

            /**
             * Загрузка дочерних и родительских узлов категории
             *
             * @param \Model\Product\Category\Entity $category
             * @param array $data
             * @use \Model\Region\Entity $region
             */
            $loadBranch = function(\Model\Product\Category\Entity $category, array $data) use (&$region, $changeCategoryUrlToSliceUrl) {
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
                            $changeCategoryUrlToSliceUrl($child);
                            $category->addChild($child);
                        }
                    }
                }

                // если категория не выбрана, выводим рутовые категории
                if (!$category->getId()) {
                    if (is_array($data)) {
                        $child = new \Model\Product\Category\Entity($data);
                        // переделываем url для категорий
                        $changeCategoryUrlToSliceUrl($child);
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

            // переделываем url для breadcrumbs
            foreach ($category->getAncestor() as $ancestor) {
                $changeCategoryUrlToSliceUrl($ancestor);
            }

            $changeCategoryUrlToSliceUrl($category);
        });
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
}