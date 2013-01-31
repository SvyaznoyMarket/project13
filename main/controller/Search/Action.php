<?php

namespace Controller\Search;

class Action {
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
            'type_id'  => 1, // тип искомых сущностей: 1 - товары
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
        $result = \App::coreClientV2()->query('search/get', $params);
        if (!isset($result[1]) || !isset($result[1]['data'])) {
            $page = new \View\Search\EmptyPage();

            return new \Http\Response($page->show());
        }
        $result = $result[1];

        // проверка на пустоту
        if (empty($result['count'])) {
            $page = new \View\Search\EmptyPage();
            $page->setParam('searchQuery', $searchQuery);

            return new \Http\Response($page->show());
        }

        $forceMean = isset($result['forced_mean']) ? $result['forced_mean'] : false;
        $meanQuery = isset($result['did_you_mean']) ? $result['did_you_mean'] : '';

        // категории
        $categoriesById = [];
        foreach ($result['category_list'] as $item) {
            $categoriesById[$item['category_id']] = new \Model\Product\Category\Entity(array(
                'id'            => $item['category_id'],
                'name'          => $item['category_name'],
                'product_count' => $item['count'],
            ));
        }
        // если ид категории из http-запроса нет в коллекции категорий ...
        if ($categoryId && !isset($categoriesById[$categoryId])) {
            throw new \Exception\NotFoundException(sprintf('Не найдена категория #%s', $categoryId));
        }
        /** @var $selectedCategory \Model\Product\Category\Entity */
        $selectedCategory = $categoryId ? $categoriesById[$categoryId] : null;

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
        $productPager = new \Iterator\EntityPager($products, $selectedCategory ? $selectedCategory->getProductCount() : $result['count']);
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
        $page->setParam('selectedCategory', $selectedCategory);
        $page->setParam('productView', $productView);

        return new \Http\Response($page->show());
    }
}