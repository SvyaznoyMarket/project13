<?php

namespace Controller\Search;

use Model\Product\Category\Entity;

class Action {
    /**
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $searchQuery = $this->getSearchQueryByRequest($request);

        if (empty($searchQuery) || (mb_strlen($searchQuery) <= \App::config()->search['queryStringLimit'])) {
            $page = new \View\Search\EmptyPage();
            $page->setParam('searchQuery', $searchQuery);
            return new \Http\Response($page->show());
            //throw new \Exception\NotFoundException(sprintf('Пустая фраза поиска.', $searchQuery));
        }
        $pageNum = (int)$request->get('page', 1);
        if ($pageNum < 1) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $pageNum));
        }

        $limit = \App::config()->product['itemsPerPage'];
        $offset = intval($pageNum - 1) * $limit - (1 === $pageNum ? 0 : 1);
        $categoryId = $request->get('category');

        $selectedCategory = $categoryId ? \RepositoryManager::productCategory()->getEntityById((int)$categoryId) : null;

        // запрашиваем фильтры
        /** @var $filters \Model\Product\Filter\Entity[] */
        $filters = [];
        \RepositoryManager::productFilter()->prepareCollectionBySearchText($searchQuery, \App::user()->getRegion(), function($data) use (&$filters) {
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

        // фильтры
        $brand = null;
        $productFilter = (new \Controller\ProductCategory\Action())->getFilter($filters, $selectedCategory, $brand, $request, $shop);

        // SITE-4734
        foreach ($productFilter->getFilterCollection() as $filter) {
            if ('brand' === $filter->getId()) {
                foreach ($filter->getOption() as $option) {
                    $option->setImageUrl('');
                }

                break;
            }
        }

        // параметры ядерного запроса
        $params = [
            'request'  => $searchQuery,
            'geo_id'   => \App::user()->getRegion()->getId(),
            'start'    => $offset,
            'limit'    => 1 === $pageNum ? $limit-1 : $limit,
            'use_mean' => true,
        ];

        if ($categoryId) {
            $params['product_category_id'] = $categoryId;
        } else {
            //$params['is_product_category_first_only'] = false;
        }
        if ((bool)$productFilter->getFilterCollection()) {
            $params['filter'] = [
                'filters' => $productFilter->dump(),
            ];
        }

        // SITE-5207 Временно исключить из выдачи сайта партнёрские товары-слоты
        $params['filter']['filters'][] = ['exclude_partner_type', 1, \Model\Product\BasicEntity::PARTNER_OFFER_TYPE_SLOT];

        // сортировка
        $productSorting = new \Model\Product\Sorting();
        list($sortingName, $sortingDirection) = array_pad(explode('-', $request->get('sort')), 2, null);
        $productSorting->setActive($sortingName, $sortingDirection);
        if(!empty($sortingName) && !empty($sortingDirection)) {
            $params['product'] = ['sort' => [$sortingName => $sortingDirection]];
        }

        try {
            // ядерный запрос
            \App::coreClientV2()->addQuery('search/get', $params, [], function ($data) use (&$result) {
                $result = $data;
            }, function(\Exception $e) { \App::exception()->remove($e); });

            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['huge'], 2);
        } catch (\Exception $e) {
            \App::logger()->error($e);

            $result = [];
        }

        if (!isset($result[1]) || !isset($result[1]['data'])) {
            $page = new \View\Search\EmptyPage();

            //return new \Http\Response($page->show());
        }
        $forceMean = isset($result['forced_mean']) ? $result['forced_mean'] : false;
        $meanQuery = isset($result['did_you_mean']) ? $result['did_you_mean'] : '';

        $resultCategories = isset($result[3]) ? $result[3] : [];
        $result = isset($result[1]) ? $result[1] : [
            'count'         => 0,
            'data'          => [],
            'category_list' => [],
            'forced_mean'   => null,
            'did_you_mean'  => null,
            'method'        => null,
        ];

        // категории (фильтруем дубли, оставляем из дублей ту категорию, которая вернулась первой)
        $categoriesFoundTmp = empty($resultCategories['data']) ? [] : \RepositoryManager::productCategory()->getCollectionById($resultCategories['data']);
        $categoriesFound = [];
        foreach ($categoriesFoundTmp as $category) {
            $tokenPrefix = str_replace('-'.$category->getId(), '', $category->getToken());
            $doubleFound = (bool)array_filter($categoriesFound, function(\Model\Product\Category\Entity $cat) use (&$tokenPrefix) {
                return $tokenPrefix == str_replace('-'.$cat->getId(), '', $cat->getToken());
            });
            if(!$doubleFound) {
                $categoriesFound[] = $category;
            }
        }

        if (count($result['category_list']) > \App::config()->search['categoriesLimit']) {
            // ограничиваем, чтобы не было 414 Request-URI Too Large
            $result['category_list'] = array_slice($result['category_list'], 0, \App::config()->search['categoriesLimit']);
        }

        /** @var $categoriesById \Model\Product\Category\Entity[] */
        $categoriesById = [];
        foreach ($result['category_list'] as $item) {
            $categoriesById[$item['category_id']] = new \Model\Product\Category\Entity([
                'id'            => $item['category_id'],
                'name'          => $item['category_name'],
                'product_count' => \App::config()->search['itemLimit'] < $item['count']
                    ? \App::config()->search['itemLimit']
                    : (int)$item['count']
                ,
            ]);
        }
        \RepositoryManager::productCategory()->prepareCollectionById(array_keys($categoriesById), \App::user()->getRegion(), function($data) use (&$categoriesById) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if ($item && is_array($item)) {
                        $category = new Entity($item);
                        if (isset($categoriesById[$category->getId()])) {
                            $categoriesById[$category->getId()]->medias = $category->medias;
                        }
                    }
                }
            }
        });

        \App::coreClientV2()->execute();

        $categoriesById = array_filter($categoriesById);
        if (!(bool)$categoriesById && $selectedCategory) {
            $categoriesById[$selectedCategory->getId()] = $selectedCategory;
        }

        // общее количество найденных товаров
        $productCount = $result['count'];
        if (\App::config()->search['itemLimit'] && (\App::config()->search['itemLimit'] < $productCount)) {
            // ограничиваем количество найденных товаров
            //$productCount = \App::config()->search['itemLimit'];
        }

        // вид товаров
        $productView = $request->get('view', $selectedCategory ? $selectedCategory->getProductView() : \Model\Product\Category\Entity::PRODUCT_VIEW_COMPACT);

        // товары
        $productRepository = \RepositoryManager::product();
        $productRepository->setEntityClass('\\Model\\Product\\Entity');

        $products = $productRepository->getCollectionById($result['data']);

        $productRepository->prepareProductsMedias($products);

        $bannerPlaceholder = [];
        \App::scmsClient()->addQuery('category/get/v1', ['uid' => \App::config()->rootCategoryUi, 'geo_id' => \App::user()->getRegion()->getId(), 'load_inactive' => 1], [], function($data) use (&$bannerPlaceholder) {
            if ($data && is_array($data)) {
                $category = new Entity($data);
                if (isset($category->catalogJson['bannerPlaceholder'])) {
                    $bannerPlaceholder = $category->catalogJson['bannerPlaceholder'];
                }
            }
        });

        \App::scmsClient()->execute();

        $productPager = new \Iterator\EntityPager($products, $productCount);
        $productPager->setPage($pageNum);
        $productPager->setMaxPerPage(\App::config()->product['itemsPerPage']);

        // проверка на максимально допустимый номер страницы
        if (($productPager->getPage() - $productPager->getLastPage()) > 0) {
            //throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $productPager->getPage()));
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([
                'page' => $productPager->getLastPage(),
            ]));
        }

        // ajax
        if ($request->isXmlHttpRequest() && 'true' == $request->get('ajax')) {
            $templating = \App::closureTemplating();
            /** @var $helper \Helper\TemplateHelper */
            $helper = \App::closureTemplating()->getParam('helper');
            $templating->setParam('selectedCategory', $selectedCategory);
            $templating->setParam('shop', $shop);

            return new \Http\JsonResponse([
                'list'           => (new \View\Product\ListAction())->execute(
                    $helper,
                    $productPager,
                    !empty($bannerPlaceholder) ? $bannerPlaceholder : []
                ),
                'selectedFilter' => (new \View\ProductCategory\SelectedFilterAction())->execute(
                    $helper,
                    $productFilter
                ),
                'pagination'     => (new \View\PaginationAction())->execute(
                    $helper,
                    $productPager
                ),
                'sorting'        => (new \View\Product\SortingAction())->execute(
                    $helper,
                    $productSorting
                ),
            ]);
        }

        // если по поиску нашелся только один товар и это первая стр. поиска, то редиректим сразу в карточку товара
        if (!$request->isXmlHttpRequest() && (1 == count($products)) && !$offset) {
            return new \Http\RedirectResponse(reset($products)->getLink() . '?q=' . urlencode($searchQuery));
        }

        if (0 == count($products)) {
            $page = new \View\Search\EmptyPage();
            $page->setParam('searchQuery', $searchQuery);
            $page->setParam('meanQuery', $meanQuery);
            $page->setParam('forceMean', $forceMean);

            return new \Http\Response($page->show());
        }

        // страница
        $page = new \View\Search\IndexPage();
        $page->setParam('searchQuery', $searchQuery);
        $page->setParam('meanQuery', $meanQuery);
        $page->setParam('forceMean', $forceMean);
        $page->setParam('productPager', $productPager);
        $page->setParam('productFilter', $productFilter);
        $page->setParam('productSorting', $productSorting);
        $page->setParam('categories', $categoriesById);
        $page->setParam('categoriesFound', $categoriesFound);
        $page->setGlobalParam('selectedCategory', $selectedCategory);
        $page->setParam('productView', $productView);
        $page->setParam('productCount', $selectedCategory && !is_null($selectedCategory->getProductCount()) ? $selectedCategory->getProductCount() : $result['count']);
        $page->setGlobalParam('shop', $shop);
        $page->setParam('bannerPlaceholder', $bannerPlaceholder);

        return new \Http\Response($page->show());
    }


    public function count(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }


        $searchQuery = (string)$request->get('q');
        $encode = mb_detect_encoding($searchQuery, array('UTF-8', 'Windows-1251'), TRUE);
        switch ($encode) {
            case 'Windows-1251': {
                $searchQuery = iconv('Windows-1251', 'UTF-8', $searchQuery);
            }
        }
        $searchQuery = trim(preg_replace('/[^\wА-Яа-я-]+/u', ' ', $searchQuery));

        if (empty($searchQuery) || (mb_strlen($searchQuery) < \App::config()->search['queryStringLimit'])) {
            return new \Http\JsonResponse(array(
                'success' => false,
                'count'    => 0,
            ));
        }


        $categoryId = (int)$request->get('category');
        if (!$categoryId) $categoryId = null;

        $selectedCategory = $categoryId ? \RepositoryManager::productCategory()->getEntityById($categoryId) : null;

        // запрашиваем фильтры
        /** @var $filters \Model\Product\Filter\Entity[] */
        $filters = [];
        \RepositoryManager::productFilter()->prepareCollectionBySearchText($searchQuery, \App::user()->getRegion(), function($data) use (&$filters) {
            foreach ($data as $item) {
                $filters[] = new \Model\Product\Filter\Entity($item);
            }
        }, function (\Exception $e) { \App::exception()->remove($e); });
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['long'], 2);

        $shop = null;
        try {
            if (
                !\Controller\ProductCategory\Action::isGlobal() &&
                \App::request()->get('shop') &&
                \App::config()->shop['enabled']
            ) {
                $shop = \RepositoryManager::shop()->getEntityById( \App::request()->get('shop') );
            }
        } catch (\Exception $e) {}

        // фильтры
        $brand = null;
        $productFilter = (new \Controller\ProductCategory\Action())->getFilter($filters, $selectedCategory, $brand, $request, $shop);

        $count = \RepositoryManager::product()->countByFilter($productFilter->dump());

        return new \Http\JsonResponse(array(
            'success' => true,
            'count'    => $count,
        ));
    }


    /**
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function autocomplete(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        if ($request->get('sender') == 'knockout') return $this->autocompleteForKnockout($request);

        $limit = 5;
        $keyword = trim(mb_strtolower($request->get('q')));
        $data = [
            'product'  => null,
            'category' => null,
        ];
        $mapData = [1 => 'product', 3 => 'category'];
        $responseData = [];

        if (mb_strlen($keyword) >= 3) {
            // параметры ядерного запроса
            $params = ['letters' => $keyword];
            if (\App::user()->getRegion()) {
                $params['region_id'] = \App::user()->getRegion()->getId();
            }
            \App::coreClientV2()->addQuery('search/autocomplete', $params, [], function($result) use(&$data, $limit, $mapData){
                foreach ($mapData as $key => $value) {
                    if (!is_array($result[$key])) continue;

                    $i = 0;
                    $entity = '\\Model\\Search\\'.ucfirst($value).'\\Entity';

                    foreach ($result[$key] as $item) {
                        if ($i >= $limit) break;

                        $data[$value][] = new $entity($item);
                        $i++;
                    }
                }
            }, function ($e) use (&$data, $mapData) {
                \App::exception()->remove($e);
                \App::logger()->error($e);
            });
            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['short'], \App::config()->coreV2['retryCount']);
        }

        if (!(bool)$data['product'] && !(bool)$data['category'] && preg_match('/^enter разработка$/iu', $keyword)) {
            $responseData = [
                'success' => true,
                'content' => \App::templating()->render('search/_autocomplete_easter_egg'),
            ];
        } else {
            $responseData = [
                'success' => true,
                'content' => (bool)$data['product'] || (bool)$data['category']
                    ? \App::templating()->render(
                        'search/_autocomplete',
                        ['products' => $data['product'],
                        'categories' => $data['category'],
                        'searchQuery' => $keyword]
                    )
                    : '',
            ];
        }
        
        return new \Http\JsonResponse($responseData);
    }

    /**
     * @param \Http\Request $request
     * @return string
     */
    public function getSearchQueryByRequest(\Http\Request $request) {
        $searchQuery = (string)$request->get('q');
        $encode = mb_detect_encoding($searchQuery, ['UTF-8', 'Windows-1251'], true);
        switch ($encode) {
            case 'Windows-1251': {
                $searchQuery = iconv('Windows-1251', 'UTF-8', $searchQuery);
            }
        }
        $searchQuery = trim(preg_replace('/[^\wА-Яа-я-]+/u', ' ', $searchQuery));

        return $searchQuery;
    }

    private function autocompleteForKnockout(\Http\Request $request) {

        $searchTerm = trim(mb_strtolower($request->query->get('q')));

        $responseData['result'] = $this->coreRequest($searchTerm, $request->query->get('catId'));

        return new \Http\JsonResponse($responseData);
    }

    private function coreRequest($searchQuery, $category = null) {

        // Структура выходных данных
        $data = [
            'categories' => [],
            'products' => []
        ];

        // Локальные переменные
        $productsIds = [];
        $products = [];

        // Параметры для автокомплита (забираем категории)

        $params1 = ['letters' => $searchQuery];

        if (\App::user()->getRegion()) $params1['region_id'] = \App::user()->getRegion()->getId();

        // Параметры для сфинкса (забираем продукты)

        $params2 = [
            'filter'=> [
                'filters' => [
                    ['text', 3, $searchQuery]
                ],
                'limit' => 5
            ]
        ];

        if ($category) $params2['filter']['filters'][] = ['category', 1, (int)$category];

        // SITE-5207 Временно исключить из выдачи сайта партнёрские товары-слоты
        $params2['filter']['filters'][] = ['exclude_partner_type', 1, \Model\Product\BasicEntity::PARTNER_OFFER_TYPE_SLOT];

        // Параллельный запрос

        \App::coreClientV2()->addQuery('search/autocomplete', $params1, [], function($result) use(&$data){
            $data['categories'] = array_slice((array)@$result[3], 0, 5);
        }, function ($e)  {
            \App::exception()->remove($e);
            \App::logger()->error($e);
        });

        \App::coreClientV2()->addQuery('listing/list', $params2, [], function ($result) use(&$productsIds){
            $productsIds = (array)@$result['list'];
        }, function ($e) {
            \App::exception()->remove($e);
            \App::logger()->error($e);
        });

        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['short'], \App::config()->coreV2['retryCount']);

        if (!empty($productsIds)) $products = \RepositoryManager::product()->getCollectionById($productsIds, null, false);

        foreach ($products as $product) {
            $data['products'][] = [
                'name'  => $product->getName(),
                'link'  => $product->getLink(),
                'image'   => $product->getImageUrl(0)
            ];
        }

        // уберем слэши у категорий
        array_walk($data['categories'], function(&$val) {
            $val['link'] = preg_replace('/\/$/', '', (string)@$val['link']);
        });

        return $data;
    }

}