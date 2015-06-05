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
    public function categoryDirect($filters, \Model\Product\Category\Entity $category, $brand, $request, $catalogJson, $promoContent) {

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

        $pageNum = (int)$request->get('page', 1);
        // на страницах пагинации сео-контент не показываем
        if ($pageNum > 1) {
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
            $page = new \View\Jewel\ProductCategory\LeafPage();
            $setPageParameters($page);

            return $this->leafCategory($category, $productFilter, $page, $request);
        }
        // иначе, если в запросе есть фильтрация
        else if ($request->get(\View\Product\FilterForm::$name)) {
            $page = new \View\Jewel\ProductCategory\BranchPage();
            $setPageParameters($page);

            return $this->branchCategory($category, $productFilter, $page, $request);
        }
        // иначе, если категория самого верхнего уровня
        else if ($category->isRoot()) {
            $page = new \View\Jewel\ProductCategory\RootPage();
            $setPageParameters($page);

            return $this->rootCategory($category, $productFilter, $page, $request);
        }

        $page = new \View\Jewel\ProductCategory\BranchPage();
        $setPageParameters($page);

        return $this->branchCategory($category, $productFilter, $page, $request);
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

        if (\App::config()->debug) \App::debug()->add('sub.act', 'ProductCategory\\Action.branchCategory', 134);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Product\Filter          $productFilter
     * @param \View\Layout                   $page
     * @param \Http\Request                  $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    protected function leafCategory(\Model\Product\Category\Entity $category, \Model\Product\Filter $productFilter, \View\Layout $page, \Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->debug) \App::debug()->add('sub.act', 'ProductCategory\\Action.leafCategory', 134);

        $region = \App::user()->getRegion();

        // если не-ajax то практически никаких действий не производим, чтобы ускорить загрузку,
        // так как при загрузке сразу же будет отправлен аякс-запрос для получения табов, фильтров, товаров
        // такой подход нужен для поддержки урлов с хэшем, чтобы не было такого UX когда страница открывается
        // с одним списком товаром на определенной вкладке, а затем переключается на другую вкладку
        // и список товаров меняется

        // TODO: после правки аякса для 
        // if ($request->isXmlHttpRequest()) {
        $pageNum = (int)$request->get('page', 1);
        if ($pageNum < 1) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $pageNum));
        }

        $productFilter = $page->getParam('productFilter');
        // был нажат фильтр или сортировка
        $scrollTo = $page->getParam('scrollTo');

        $catalogJson = $page->getParam('catalogJson');

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
        $productView = $category->getProductView();
        // листалка
        $limit = \App::config()->product['itemsPerPageJewel'];
        $repository = \RepositoryManager::product()->useV3();

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
                foreach ((array)$data as $item) {
                    if (!isset($item['id'])) continue;

                    $products[] = new \Model\Product\Entity($item);
                }
            });
        }
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        $scoreData = [];
        if ((bool)$products) {
            $productUIs = [];
            foreach ($products as $product) {
                if (!$product instanceof \Model\Product\Entity) continue;
                $productUIs[] = $product->getUi();
            }

            \RepositoryManager::review()->prepareScoreCollectionByUi($productUIs, function($data) use (&$scoreData) {
                if (isset($data['product_scores'][0])) {
                    $scoreData = $data;
                }
            });
        }

        $repository->prepareProductsMedias($products);

        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        \RepositoryManager::review()->addScores($products, $scoreData);

        $productPager = new \Iterator\EntityPager($products, $productCount);
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

        if ($request->isXmlHttpRequest()) {
            $responseData = [];
            $responseData['products'] = \App::templating()->render('jewel/product/_list', [
                'page'                   => new \View\Layout(),
                'pager'                  => $productPager,
                'view'                   => $productView,
                'isAjax'                 => true,
                'isAddInfo'              => true,
                'itemsPerRow'            => $page->getParam('itemsPerRow'),
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
                    'category'          => $page->getParam('category'),
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
                    'category'          => $page->getParam('category'),
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
                    'category'                  => $page->getParam('category'),
                    'view'                      => $productView,
                    'isAddInfo'                 => true,
                ]);
                $responseData['query_string'] = $request->getQueryString();

                return new \Http\JsonResponse($responseData);
            }
        }

        $page->setParam('productPager', $productPager);
        $page->setParam('productSorting', $productSorting);
        $page->setParam('productView', $productView);

        return new \Http\Response($page->show());
    }

}