<?php

namespace Controller\Jewel\ProductCategory;

class Action extends \Controller\ProductCategory\Action {
    private static $globalCookieName = 'global';

    /**
     * Сейчас categoryDirect() вызывается из \Controller\ProductCategory\Action напрямую
     * Чтобы вызывать через роутинг, надо обращаться к этой функции
     *
     * @param \Http\Request $request
     * @param string        $categoryPath
     * @param string|null   $brandToken
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function prepareCategory(\Http\Request $request, $categoryPath, $brandToken = null) {

        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        $categoryToken = explode('/', $categoryPath);
        $categoryToken = end($categoryToken);

        // подготовка 1-го пакета запросов

        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
            if ($user->getRegionId()) {
                \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                    $data = reset($data);
                    if ((bool)$data) {
                        \App::user()->setRegion(new \Model\Region\Entity($data));
                    }
                });
            }
        }

        // запрашиваем список регионов для выбора
        $regionsToSelect = [];
        \RepositoryManager::region()->prepareShownInMenuCollection(function($data) use (&$regionsToSelect) {
            foreach ($data as $item) {
                $regionsToSelect[] = new \Model\Region\Entity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute(\App::config()->coreV2['retryTimeout']['tiny']);

        /** @var $region \Model\Region\Entity|null */
        $region = self::isGlobal() ? null : \App::user()->getRegion();

        // подготовка 2-го пакета запросов

        // TODO: запрашиваем меню

        // запрашиваем категорию по токену
        /** @var $category \Model\Product\Category\Entity */
        $category = null;
        \RepositoryManager::productCategory()->prepareEntityByToken($categoryToken, $region, function($data) use (&$category) {
            $data = reset($data);
            if ((bool)$data) {
                $category = new \Model\Product\Category\Entity($data);
            }
        });

        // запрашиваем бренд по токену
        /** @var $brand \Model\Brand\Entity */
        $brand = null;
        if ($brandToken) {
            \RepositoryManager::brand()->prepareEntityByToken($brandToken, $region, function($data) use (&$brand) {
                $data = reset($data);
                if ((bool)$data) {
                    $brand = new \Model\Brand\Entity($data);
                }
            });
        }

        // выполнение 2-го пакета запросов
        $client->execute(\App::config()->coreV2['retryTimeout']['short']);

        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара @%s не найдена', $categoryToken));
        }

        // подготовка 3-го пакета запросов

        // запрашиваем дерево категорий
        \RepositoryManager::productCategory()->prepareEntityBranch($category, $region);

        // запрашиваем фильтры
        /** @var $filters \Model\Product\Filter\Entity[] */
        $filters = [];
        \RepositoryManager::productFilter()->prepareCollectionByCategory($category, $region, function($data) use (&$filters) {
            foreach ($data as $item) {
                $filters[] = new \Model\Product\Filter\Entity($item);
            }
        });

        // выполнение 3-го пакета запросов
        $client->execute(\App::config()->coreV2['retryTimeout']['tiny']);

        // получаем catalog json для категории (например, тип раскладки)
        $catalogJson = \RepositoryManager::productCategory()->getCatalogJson($category);

        return $this->category($filters, $category, $brand, $request, $regionsToSelect, $catalogJson, $promoContent);
    }


    /**
     * @param \Model\Product\Filter\Entity[]  $filters
     * @param \Model\Product\Category\Entity  $category
     * @param \Model\Brand\Entity|null        $brand
     * @param \Http\Request                   $request
     * @param \Model\Region\Entity[]          $regionsToSelect
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function categoryDirect($filters, $category, $brand, $request, $regionsToSelect, $catalogJson, $promoContent) {
        // убираем/показываем уши
        if(isset($catalogJson['show_side_panels'])) {
            \App::config()->adFox['enabled'] = (bool)$catalogJson['show_side_panels'];
        }

        \App::logger()->debug('Exec ' . __METHOD__);

        // если в catalogJson'e указан category_layout_type == 'promo', то подгружаем промо-контент
        if(!empty($catalogJson['category_layout_type']) && $catalogJson['category_layout_type'] == 'promo') {
            $htmlPromoContent = \RepositoryManager::productCategory()->getCatalogHtml($category);
            if(!empty($htmlPromoContent)) {
                $promoContent = $htmlPromoContent;
            }
        }

        // фильтры
        $productFilter = $this->getFilter($filters, $category, $brand, $request);

        // получаем из json данные о горячих ссылках и content
        try {
            $seoCatalogJson = \Model\Product\Category\Repository::getSeoJson($category);
            $hotlinks = empty($seoCatalogJson['hotlinks']) ? [] : $seoCatalogJson['hotlinks'];
            // в json-файле в свойстве content содержится массив
            if (empty($brand)) {
                $seoContent = empty($seoCatalogJson['content']) ? '' : implode('<br />', $seoCatalogJson['content']);
            } else {
                $seoBrandJson = \Model\Product\Category\Repository::getSeoJson($category, $brand);
                $seoContent = empty($seoBrandJson['content']) ? '' : implode('<br />', $seoBrandJson['content']);
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

        $setPageParameters = function(\View\Layout $page) use (
            &$category,
            &$regionsToSelect,
            &$productFilter,
            &$brand,
            &$hotlinks,
            &$seoContent,
            &$catalogJson,
            &$promoContent
        ) {
            $page->setParam('category', $category);
            $page->setParam('regionsToSelect', $regionsToSelect);
            $page->setParam('productFilter', $productFilter);
            $page->setParam('brand', $brand);
            $page->setParam('hotlinks', $hotlinks);
            $page->setParam('seoContent', $seoContent);
            $page->setParam('catalogJson', $catalogJson);
            $page->setParam('promoContent', $promoContent);
            $page->setParam('itemsPerRow', \App::config()->product['itemsPerRowJewel']);
            $page->setParam('scrollTo', 'smalltabs');
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
        \App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->debug) \App::debug()->add('sub.act', 'ProductCategory\\Action.branchCategory', 138);

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
        \App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->debug) \App::debug()->add('sub.act', 'ProductCategory\\Action.leafCategory', 138);

        // если не-ajax то практически никаких действий не производим, чтобы ускорить загрузку,
        // так как при загрузке сразу же будет отправлен аякс-запрос для получения табов, фильтров, товаров
        // такой подход нужен для поддержки урлов с хэшем, чтобы не было такого UX когда страница открывается
        // с одним списком товаром на определенной вкладке, а затем переключается на другую вкладку
        // и список товаров меняется
        if ($request->isXmlHttpRequest()) {
            $pageNum = (int)$request->get('page', 1);
            if ($pageNum < 1) {
                throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $pageNum));
            }

            $productFilter = $page->getParam('productFilter');
            // был нажат фильтр или сортировка
            $scrollTo = $page->getParam('scrollTo');

             // сортировка
            $productSorting = new \Model\Product\Sorting();
            list($sortingName, $sortingDirection) = array_pad(explode('-', $request->get('sort')), 2, null);
            $productSorting->setActive($sortingName, $sortingDirection);

            // вид товаров
            $productView = $request->get('view', $category->getHasLine() ? 'line' : $category->getProductView());
            // листалка
            $limit = \App::config()->product['itemsPerPageJewel'];
            $repository = \RepositoryManager::product();
            $repository->setEntityClass(
                \Model\Product\Category\Entity::PRODUCT_VIEW_EXPANDED == $productView
                    ? '\\Model\\Product\\ExpandedEntity'
                    : '\\Model\\Product\\CompactEntity'
            );
            $productPager = $repository->getIteratorByFilter(
                $productFilter->dump(),
                $productSorting->dump(),
                ($pageNum - 1) * $limit,
                $limit
            );
            $productPager->setPage($pageNum);
            $productPager->setMaxPerPage($limit);
            if (self::isGlobal()) {
                $category->setGlobalProductCount($productPager->count());
            } else {
                $category->setProductCount($productPager->count());
            }

            // проверка на максимально допустимый номер страницы
            if (($productPager->getPage() - $productPager->getLastPage()) > 0) {
                throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $productPager->getPage()));
            }

            // video
            $productVideosByProduct = [];
            foreach ($productPager as $product) {
                /** @var $product \Model\Product\Entity */
                $productVideosByProduct[$product->getId()] = [];
            }
            if ((bool)$productVideosByProduct) {
                \RepositoryManager::productVideo()->prepareCollectionByProductIds(array_keys($productVideosByProduct), function($data) use (&$productVideosByProduct) {
                    foreach ($data as $id => $items) {
                        if (!is_array($items)) continue;
                        foreach ($items as $item) {
                            $productVideosByProduct[$id][] = new \Model\Product\Video\Entity((array)$item);
                        }
                    }
                });
                \App::dataStoreClient()->execute(\App::config()->dataStore['retryTimeout']['tiny'], \App::config()->dataStore['retryCount']);
            }

            $responseData = [];
            $responseData['products'] = \App::templating()->render('jewel/product/_list', [
                'page'                   => new \View\Layout(),
                'pager'                  => $productPager,
                'view'                   => $productView,
                'productVideosByProduct' => $productVideosByProduct,
                'isAjax'                 => true,
                'itemsPerRow'            => \App::config()->product['itemsPerRowJewel'],
            ]);
            // бесконечный скролл
            if(empty($scrollTo)) {
                return new \Http\Response($responseData['products']);
            }
            // фильтры, сортировка и товары с пагинацией
            else {
                $responseData['tabs'] = \App::templating()->render('jewel/product-category/filter/_tabs', [
                    'filters'           => $productFilter->getFilterCollection(),
                    'catalogJson'       => $page->getParam('catalogJson'),
                    'productFilter'     => $productFilter,
                    'category'          => $page->getParam('category'),
                    'scrollTo'          => $scrollTo,
                ]);
                $responseData['filters'] = \App::templating()->render('jewel/product-category/_filters', [
                    'page'              => new \View\Layout(),
                    'filters'           => $productFilter->getFilterCollection(),
                    'catalogJson'       => $page->getParam('catalogJson'),
                    'productSorting'    => $productSorting,
                    'productPager'      => $productPager,
                    'productFilter'     => $productFilter,
                    'category'          => $page->getParam('category'),
                    'scrollTo'          => $scrollTo,
                    'isAjax'            => true,
                ]);
                $responseData['pager'] = \App::templating()->render('jewel/product/_pager', [
                    'page'                      => new \View\Layout(),
                    'request'                   => $request,
                    'pager'                     => $productPager,
                    'productFilter'             => $productFilter,
                    'productSorting'            => $productSorting,
                    'hasListView'               => true,
                    'category'                  => $page->getParam('category'),
                    'productVideosByProduct'    => $productVideosByProduct,
                    'view'                      => $productView,
                    'itemsPerRow'               => $page->getParam('itemsPerRow'),
                ]);
                $responseData['query_string'] = $request->getQueryString();

                return new \Http\JsonResponse($responseData);
            }
        }

        // $page->setParam('productPager', $productPager);
        // $page->setParam('productSorting', $productSorting);
        // $page->setParam('productView', $productView);
        // $page->setParam('productVideosByProduct', $productVideosByProduct);
        // $page->setParam('sidebarHotlinks', true);

        $page->setParam('myThingsData', [
            'EventType'   => 'MyThings.Event.Visit',
            'Action'      => '1011',
            'Category'    => isset($category->getAncestor()[0]) ? $category->getAncestor()[0]->getName() : null,
            'SubCategory' => $category->getName()
        ]);

        return new \Http\Response($page->show());
    }

}