<?php

namespace Controller\Slice;

use Controller\Product\SetAction;
use Model\Product\Category\Entity;
use EnterApplication\CurlTrait;
use EnterQuery as Query;
use \Model\Product\Category\Entity as Category;

class Action {
    use CurlTrait;

    /**
     * @param \Http\Request $request
     * @param string        $sliceToken
     * @param string|null   $categoryToken
     * @param string|null   $brandToken
     * @param string|null   $page
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function execute(\Http\Request $request, $sliceToken, $categoryToken = null, $brandToken = null, $page = null) {
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

        /** @var $slice \Model\Slice\Entity */
        $slice = call_user_func(function() use($sliceToken) {
            $slice = null;
            \RepositoryManager::slice()->prepareEntityByToken(
                $sliceToken,
                function($data) use (&$slice, $sliceToken) {
                    if (is_array($data) && $data) {
                        $data['token'] = $sliceToken;
                        $slice = new \Model\Slice\Entity($data);
                    }
                },
                function(\Exception $e) {
                    \App::exception()->remove($e);
                }
            );

            \App::scmsSeoClient()->execute();
    
            if (!$slice) {
                throw new \Exception\NotFoundException(sprintf('Срез @%s не найден', $sliceToken));
            }
            
            return $slice;
        });

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

        $productSorting = $this->getSorting();
        $shop = $this->getShop();
        $region = $this->getRegion(isset($sliceRequestFilters['region']) ? $sliceRequestFilters['region'] : null);
        $category = $this->getCategory($categoryToken, $slice, $region);
        $sliceFiltersForSearchClientRequest = \RepositoryManager::slice()->getSliceFiltersForSearchClientRequest($slice, $category->getId() ? true : false, (bool)$brandToken);

        $this->prepareEntityBranch($category, $sliceToken, $sliceFiltersForSearchClientRequest, $region);

        /** @var \Model\Config\Entity[] $configParameters */
        $configParameters = [];
        $callbackPhrases = [];
        \RepositoryManager::config()->prepare(['site_call_phrases'], $configParameters, function(\Model\Config\Entity $entity) use (&$category, &$callbackPhrases) {
            if ('site_call_phrases' === $entity->name) {
                $callbackPhrases = !empty($entity->value['special_action']) ? $entity->value['special_action'] : [];
            }

            return true;
        });

        $filters = [];
        /** @var \Model\Brand\Entity|null $brand */
        $brand = null;
        /** @var \Model\Seo\Hotlink\Entity[] $hotlinks */
        $hotlinks = [];
        \RepositoryManager::productFilter()->prepareCollectionByCategory($category->getId() ? $category : null, $region, $sliceFiltersForSearchClientRequest, function($data) use (&$filters, &$brand, &$hotlinks, $sliceFiltersForSearchClientRequest, $brandToken, $slice) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    $filter = new \Model\Product\Filter\Entity($item);
                    
                    if ($filter->isBrand()) {
                        foreach ($filter->getOption() as $option) {
                            if ($brandToken && $option->getToken() === $brandToken) {
                                $brand = $slice->getBrandByToken($brandToken);
                                if (!$brand) {
                                    $brand = new \Model\Brand\Entity();
                                }

                                $brand->id = $option->id;
                                $brand->token = $option->token;
                                $brand->name = $option->name;

                                if (!$brand->title) {
                                    $brand->title = $slice->getName() . ' ' . $brand->name . ' – купить в интернет-магазине Enter.ru';
                                }

                                if (!$brand->metaDescription) {
                                    $brand->metaDescription = $slice->getName() . ' ' . $brand->name . ' — большой выбор, узнать цены, прочитать отзывы. Возможность купить в кредит.';
                                }

                                if (!$brand->heading) {
                                    $brand->heading = $slice->getName() . ' ' . $brand->name;
                                }
                            }
                            
                            if ($this->isSeoSlice()) {
                                $hotlinks[] = new \Model\Seo\Hotlink\Entity([
                                    'url' => \App::router()->generateUrl('product.category.slice', [
                                        'sliceToken' => $slice->getToken(),
                                        'brandToken' => $option->token,
                                    ]),
                                    'name' => $option->name,
                                ]);
                            }
                        }
                    }
                    
                    if (!$this->hasFilterInFiltersForSearchClientRequest($filter->getId(), $sliceFiltersForSearchClientRequest)) {
                        $filters[] = $filter;
                    }
                }
            }
        });

        /** @var \Model\Product\Category\Entity[] $sliceCategories */
        $sliceCategories = [];
        if (!$request->isXmlHttpRequest()) {
            // категории в фильтре среза, например { slice.filter: category[]=1239&category[]=1245&category[]=1232&category[]=1216&category[]=1224&category[]=1209&category[]=338 }
            $this->prepareSliceCategories($slice, $region, $sliceCategories);
        }

        \App::coreClientV2()->execute();

        if ($brandToken && !$brand) {
            throw new \Exception\NotFoundException('Не найден бренд ' . $brandToken);
        }

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

                $children = $category->id ? $category->getChild() : $sliceCategories;

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
                $children = $filter($children);

                $category->setChild($children);
            }
        }

        $productFilter = \RepositoryManager::productFilter()->createProductFilter($filters, $category->getId() ? $category : null, $brand, $request, $shop);
        $productPager = $this->getProductPager($productFilter, $sliceFiltersForSearchClientRequest, $productSorting, $page, $region);
        $category->setProductCount($productPager->count());
        
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

        $heading = $brand ? $brand->heading : $slice->getName();

        if ($brand) {
            $seoContent = $brand->content;
        } else {
            if ($slice->categoryUid) {
                $seoContent = $slice->getContent();
            } else {
                $seoContent = $slice->getContent();

                if (!$seoContent && !$this->isSeoSlice()) {
                    $seoContent = $category->getSeoContent();
                }
            }
        }

        if (!$slice->categoryUid && $page > 1) {
            $seoContent = '';
        }

        $helper = new \Helper\TemplateHelper();

        $listViewData = (new \View\Product\ListAction())->execute(
            $helper,
            $productPager,
            [],
            $slice->getProductBuyMethod(),
            $slice->getShowProductState(),
            4,
            $category->getChosenView(),
            $cartButtonSender,
            $category
        );

        if ($request->isXmlHttpRequest() && 'true' == $request->get('ajax')) {
            return new \Http\JsonResponse([
                'list'           => $listViewData,
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
                    'title' => $heading
                ],
                'countProducts'  => $productPager->count(),
                'request' => [
                    'route' => [
                        'name' => \App::request()->routeName,
                        'pathVars' => \App::request()->routePathVars->all(),
                    ],
                ],
            ]);
        }

        $pageView = new \View\Slice\ShowPage();

        if ($productPager && $productPager->getPage() > 1) {
            $pageSeoText = 'Страница ' . $productPager->getPage() . ' - ' . implode(' > ', call_user_func(function() use($slice, $category, $brand, $heading, $helper) {
                    $parts = [];

                    foreach ($category->getAncestor() as $ancestorCategory) {
                        $parts[] = $ancestorCategory->name;
                    }

                    if ($category->name) {
                        $parts[] = $category->name;
                    }

                    if ($heading) {
                        $parts[] = $heading;
                    }

                    return $parts;
                }));

            $pageView->setTitle($pageSeoText);
            $pageView->addMeta('description', 'В нашем интернет магазине Enter.ru ты можешь купить с доставкой. ' . $pageSeoText);
        } else {
            $pageView->setTitle($brand ? $brand->title : $slice->getTitle());
            $pageView->addMeta('description', $brand ? $brand->metaDescription : $slice->getMetaDescription());
        }

        $pageView->setParam('heading', $heading);
        $pageView->setParam('category', $category);
        $pageView->setParam('slice', $slice);
        $pageView->setParam('productPager', $productPager);
        $pageView->setParam('productSorting', $productSorting);
        $pageView->setParam('productFilter', $productFilter);
        $pageView->setParam('hasCategoryChildren', !$this->isSeoSlice()); // SITE-3558
        $pageView->setParam('sliceCategories', $sliceCategories);
        $pageView->setParam('seoContent', $seoContent);
        $pageView->setParam('hotlinks', $hotlinks);
        $pageView->setParam('listViewData', $listViewData);
        $pageView->setGlobalParam('shop', $shop);
        $pageView->setGlobalParam('callbackPhrases', $callbackPhrases);

        return new \Http\Response($pageView->show());
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
                $category->setLink($router->generateUrl('slice', ['sliceToken' => $slice->getToken(), 'categoryToken' => $category->getToken()]));

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

        if (\App::config()->product['reviewEnabled']) {
            \RepositoryManager::review()->prepareScoreCollection($products, function ($data) use (&$products) {
                if (isset($data['product_scores'][0])) {
                    \RepositoryManager::review()->addScores($products, $data);
                }
            });
        }

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

        $isSeoSlice = $this->isSeoSlice();

        \App::searchClient()->addQuery('category/tree', $params, [], function($data) use (&$category, &$region, $sliceToken, &$isSeoSlice) {
            $helper = new \Helper\TemplateHelper();

            if ($isSeoSlice) {
                // SITE-5432
                $changeCategoryUrlToSliceUrl = function() {};
            } else {
                $changeCategoryUrlToSliceUrl = function(\Model\Product\Category\Entity $category) use($sliceToken, $helper) {
                    $url = explode('/', $category->getLink());
                    $url = $helper->url('slice', ['sliceToken' => $sliceToken, 'categoryToken' => end($url)]);
                    $category->setLink($url);
                };
            }

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
                    $category->setChild([]);
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

    private function isSeoSlice() {
        return in_array(\App::request()->routeName, ['product.category.slice'], true);
    }
}