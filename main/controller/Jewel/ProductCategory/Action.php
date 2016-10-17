<?php

namespace Controller\Jewel\ProductCategory;

class Action extends \Controller\ProductCategory\Action {
    /**
     * @param \Model\Product\Filter\Entity[]  $filters
     * @param \Model\Brand\Entity|null        $brand
     * @param \Http\Request                   $request
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function categoryDirect($filters, \Model\Product\Category\Entity $category, $brand, $request, $catalogJson, $promoContent, $page = null) {

        // фильтры
        $productFilter = \RepositoryManager::productFilter()->createProductFilter($filters, $category, $brand, $request);

        // получаем из json данные о горячих ссылках и content
        try {
            $hotlinks = $category->getSeoHotlinks();
            $seoContent = $category->getSeoContent();
        } catch (\Exception $e) {
            $hotlinks = [];
            $seoContent = '';
        }

        // на страницах пагинации сео-контент не показываем
        if ($page > 1) {
            $seoContent = '';
        }

        $subCatMenu = &$catalogJson['sub_category_filter_menu'];
        if ( !empty($subCatMenu) && is_array($subCatMenu) ) {
            $subCatMenu = reset($subCatMenu);
        }

        /*
        switch (\App::abTest()->getTest('jewel_items') && \App::abTest()->getTest('jewel_items')->getChosenCase()->getKey()) {
            case 'jewelItems3':
                $itemsPerRow = 3;
                break;
            case 'jewelItems4':
                $itemsPerRow = 4;
                break;
            default:
                $itemsPerRow = \App::config()->product['itemsPerRowJewel'];
        }
        */
        // Pandora, Guess - по 3, остальные - по 4
        $itemsPerRow = (bool)array_intersect(array_map(function(\Model\Product\Category\Entity $category) { return $category->getId(); }, $category->getAncestor()), [1320, 4649]) ? 3 : 4;

        $setPageParameters = function(\View\Layout $page) use (
            &$category,
            &$productFilter,
            &$brand,
            &$hotlinks,
            &$seoContent,
            &$catalogJson,
            &$promoContent,
            &$itemsPerRow
        ) {
            $page->setParam('category', $category);
            $page->setParam('productFilter', $productFilter);
            $page->setParam('brand', $brand);
            $page->setParam('hotlinks', $hotlinks);
            $page->setParam('seoContent', $seoContent);
            $page->setParam('catalogJson', $catalogJson);
            $page->setParam('promoContent', $promoContent);
            $page->setParam('itemsPerRow', $itemsPerRow);
            $page->setParam('scrollTo', 'smalltabs');
            $page->setParam('searchHints', $this->getSearchHints($catalogJson));
            $page->setParam('viewParams', [
                'showSideBanner' => \Controller\ProductCategory\Action::checkAdFoxBground($catalogJson)
            ]);
        };

        // если категория содержится во внешнем узле дерева
        if ($category->isLeaf()) {
            $pageView = new \View\Jewel\ProductCategory\LeafPage();
            $setPageParameters($pageView);

            return $this->leafCategory($category, $productFilter, $pageView, $request, null, $page);
        }
        // иначе, если в запросе есть фильтрация
        else if ($request->get(\View\Product\FilterForm::$name)) {
            $pageView = new \View\Jewel\ProductCategory\BranchPage();
            $setPageParameters($pageView);

            return $this->branchCategory($category, $productFilter, $pageView, $request);
        }
        // иначе, если категория самого верхнего уровня
        else if ($category->isRoot()) {
            $pageView = new \View\Jewel\ProductCategory\RootPage();
            $setPageParameters($pageView);

            return $this->rootCategory($category, $productFilter, $pageView, $request);
        }

        $pageView = new \View\Jewel\ProductCategory\BranchPage();
        $setPageParameters($pageView);

        return $this->branchCategory($category, $productFilter, $pageView, $request);
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Product\Filter          $productFilter
     * @param \View\Layout                   $page
     * @param \Http\Request                  $request
     * @return \Http\Response
     */
    protected function branchCategory(\Model\Product\Category\Entity $category, \Model\Product\Filter $productFilter, \View\Layout $page, \Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->debug) \App::debug()->add('routeSubAction', 'ProductCategory\\Action::branchCategory', 134);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Product\Filter          $productFilter
     * @param \View\Layout                   $pageView
     * @param \Http\Request                  $request
     * @param string|null                    $categoryToken
     * @param string|null                    $page
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    protected function leafCategory(\Model\Product\Category\Entity $category, \Model\Product\Filter $productFilter, \View\Layout $pageView, \Http\Request $request, $categoryToken = null, $page = null) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->debug) \App::debug()->add('routeSubAction', 'ProductCategory\\Action::leafCategory', 134);

        $region = \App::user()->getRegion();

        $productFilter = $pageView->getParam('productFilter');
        // был нажат фильтр или сортировка
        $scrollTo = $pageView->getParam('scrollTo');

        $catalogJson = $pageView->getParam('catalogJson');

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
        $limit = \App::config()->product['itemsPerPageJewel'];
        $repository = \RepositoryManager::product();

        /** @var \Model\Product\Entity[] $products */
        $products = [];
        $productCount = 0;
        $repository->prepareIteratorByFilter(
            $productFilter->dump(),
            $sort,
            ($page - 1) * $limit,
            $limit,
            $region,
            function($data) use (&$products, &$productCount) {
                if (isset($data['list'][0])) $products = array_map(function($productId) { return new \Model\Product\Entity(['id' => $productId]); }, $data['list']);
                if (isset($data['count'])) $productCount = (int)$data['count'];
            }
        );
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        $repository->prepareProductQueries($products, 'media label brand category');
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        \RepositoryManager::review()->prepareScoreCollection($products, function($data) use(&$products) {
            if (isset($data['product_scores'][0])) {
                \RepositoryManager::review()->addScores($products, $data);
            }
        });

        // SITE-5772
        call_user_func(function() use(&$products, $category) {
            $sender = $category->getSenderForGoogleAnalytics();
            if ($sender) {
                foreach ($products as $product) {
                    $product->setLink($product->getLink() . (strpos($product->getLink(), '?') === false ? '?' : '&') . http_build_query(['sender' => $sender]));
                }
            }
        });

        $productPager = new \Iterator\EntityPager($products, $productCount);
        $productPager->setPage($page);
        $productPager->setMaxPerPage($limit);
        $category->setProductCount($productPager->count());

        // проверка на максимально допустимый номер страницы
        if (($productPager->getPage() - $productPager->getLastPage()) > 0) {
            //throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $productPager->getPage()));
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([
                'page' => $productPager->getLastPage(),
            ]));
        }

        if ($request->isXmlHttpRequest()) {
            $responseData = [];
            $responseData['products'] = \App::templating()->render('jewel/product/_list', [
                'page'                   => new \View\Layout(),
                'pager'                  => $productPager,
                'isAjax'                 => true,
                'isAddInfo'              => true,
                'itemsPerRow'            => $pageView->getParam('itemsPerRow'),
            ]);
            // бесконечный скролл
            if(empty($scrollTo)) {
                return new \Http\Response($responseData['products']);
            }
            // фильтры, сортировка и товары с пагинацией
            else {
                $responseData['tabs'] = \App::templating()->render('jewel/product-category/filter/_tabs', [
                    'filters'           => $productFilter->getFilterCollection(),
                    'catalogJson'       => $catalogJson,
                    'productFilter'     => $productFilter,
                    'category'          => $pageView->getParam('category'),
                    'scrollTo'          => $scrollTo,
                    'isAddInfo'         => true,
                ]);
                $responseData['filters'] = \App::templating()->render('jewel/product-category/_filters', [
                    'page'              => new \View\Layout(),
                    'filters'           => $productFilter->getFilterCollection(),
                    'catalogJson'       => $catalogJson,
                    'productSorting'    => $productSorting,
                    'productPager'      => $productPager,
                    'productFilter'     => $productFilter,
                    'category'          => $pageView->getParam('category'),
                    'scrollTo'          => $scrollTo,
                    'isAjax'            => true,
                    'isAddInfo'         => true,
                ]);
                $responseData['pager'] = \App::templating()->render('jewel/product/_pager', [
                    'page'                      => new \View\Layout(),
                    'request'                   => $request,
                    'pager'                     => $productPager,
                    'productFilter'             => $productFilter,
                    'productSorting'            => $productSorting,
                    'hasListView'               => true,
                    'category'                  => $pageView->getParam('category'),
                    'isAddInfo'                 => true,
                ]);
                $responseData['query_string'] = $request->getQueryString();

                return new \Http\JsonResponse($responseData);
            }
        }

        $pageView->setParam('productPager', $productPager);
        $pageView->setParam('productSorting', $productSorting);

        return new \Http\Response($pageView->show());
    }

}