<?php

namespace Controller\Search;

class Action {
    /**
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $searchQuery = trim((string)$request->get('q'));
        if (empty($searchQuery)) {
            throw new \Exception\NotFoundException(sprintf('Пустая фраза поиска.', $searchQuery));
        }
        $pageNum = (int)$request->get('page', 1);
        if ($pageNum < 1) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $pageNum));
        }

        $limit = \App::config()->product['itemsPerPage'];
        $offset = intval($pageNum - 1) * $limit;
        $categoryId = (int)$request->get('category');
        if (!$categoryId) $categoryId = null;

        // параметры ядерного запроса
        $params = array(
            'request'  => $searchQuery,
            'geo_id'   => \App::user()->getRegion()->getId(),
            'start'    => $offset,
            'limit'    => $limit,
            'use_mean' => true,
        );
        if ($categoryId) {
            $params['product_category_id'] = $categoryId;
        } else {
            //$params['is_product_category_first_only'] = false;
        }
        // ядерный запрос
        $result = [];
        \App::coreClientV2()->addQuery('search/get', $params, [], function ($data) use (&$result) {
            $result = $data;
        });
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['default'], \App::config()->coreV2['retryCount']);

        if (!isset($result[1]) || !isset($result[1]['data'])) {
            $page = new \View\Search\EmptyPage();

            return new \Http\Response($page->show());
        }
        $forceMean = isset($result['forced_mean']) ? $result['forced_mean'] : false;
        $meanQuery = isset($result['did_you_mean']) ? $result['did_you_mean'] : '';

        $resultCategories = $result[3];
        $result = $result[1];

        // проверка на пустоту
        if (empty($result['count'])) {
            $page = new \View\Search\EmptyPage();
            $page->setParam('searchQuery', $searchQuery);

            return new \Http\Response($page->show());
        }

        // категории
        $categoriesFound = empty($resultCategories['data']) ? [] : \RepositoryManager::productCategory()->getCollectionById($resultCategories['data']);

        $categoriesById = [];
        foreach ($result['category_list'] as $item) {
            $categoriesById[$item['category_id']] = new \Model\Product\Category\Entity(array(
                'id'            => $item['category_id'],
                'name'          => $item['category_name'],
                'product_count' => \App::config()->search['itemLimit'] < $item['count']
                    ? \App::config()->search['itemLimit']
                    : (int)$item['count']
                ,
            ));
        }

        // если ид категории из http-запроса нет в коллекции категорий ...
        if ($categoryId && !isset($categoriesById[$categoryId])) {
            throw new \Exception\NotFoundException(sprintf('Не найдена категория #%s', $categoryId));
        }
        /** @var $selectedCategory \Model\Product\Category\Entity */
        $selectedCategory = $categoryId ? $categoriesById[$categoryId] : null;

        // общее количество найденных товаров
        $productCount = $selectedCategory ? $selectedCategory->getProductCount() : $result['count'];
        if (\App::config()->search['itemLimit'] && (\App::config()->search['itemLimit'] < $productCount)) {
            // ограничиваем количество найденных товаров
            $productCount = \App::config()->search['itemLimit'];
        }

        // вид товаров
        $productView = $request->get('view', $selectedCategory ? $selectedCategory->getProductView() : \Model\Product\Category\Entity::PRODUCT_VIEW_COMPACT);

        // товары
        $productRepository = \RepositoryManager::product();
        $productRepository->setEntityClass(
            \Model\Product\Category\Entity::PRODUCT_VIEW_EXPANDED == $productView
            ? '\\Model\\Product\\ExpandedEntity'
            : '\\Model\\Product\\CompactEntity'
        );
        $products = $productRepository->getCollectionById($result['data']);
        $productPager = new \Iterator\EntityPager($products, $productCount);
        $productPager->setPage($pageNum);
        $productPager->setMaxPerPage(\App::config()->product['itemsPerPage']);

        // проверка на максимально допустимый номер страницы
        if (($productPager->getPage() - $productPager->getLastPage()) > 0) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $productPager->getPage()));
        }

        // ajax
        if ($request->isXmlHttpRequest()) {
            return new \Http\Response(\App::templating()->render('product/_list', array(
                'page'   => new \View\Layout(),
                'pager'  => $productPager,
                'view'   => $productView,
                'isAjax' => true,
            )));
        }

        // страница
        $page = new \View\Search\IndexPage();
        $page->setParam('searchQuery', $searchQuery);
        $page->setParam('meanQuery', $meanQuery);
        $page->setParam('forceMean', $forceMean);
        $page->setParam('productPager', $productPager);
        $page->setParam('categories', $categoriesById);
        $page->setParam('categoriesFound', $categoriesFound);
        $page->setParam('selectedCategory', $selectedCategory);
        $page->setParam('productView', $productView);
        $page->setParam('productCount', $selectedCategory ? $selectedCategory->getProductCount() : $result['count']);

        return new \Http\Response($page->show());
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

        $limit = 5;
        $keyword = mb_strtolower($request->get('q'));
        $data = [
            'product'  => null,
            'category' => null,
        ];
        $mapData = [1 => 'product', 3 => 'category'];

        if (mb_strlen($keyword) >= 3) {
            \App::coreClientV2()->addQuery('search/autocomplete', ['letters' => $keyword], [], function($result) use(&$data, $limit, $mapData){
                foreach ($mapData as $key => $value) {
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
            $response = new \Http\Response(\App::templating()->render('search/_autocomplete_easter_egg'));
        } else {
            $response = new \Http\Response((bool)$data['product'] || (bool)$data['category'] ? \App::templating()->render('search/_autocomplete', ['products' => $data['product'], 'categories' => $data['category'], 'searchQuery' => $keyword]) : '');
        }
        
        $response->setIsShowDebug(false);

        return $response;
    }
}