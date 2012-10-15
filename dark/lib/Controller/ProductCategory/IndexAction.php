<?php

namespace Controller\ProductCategory;

class IndexAction {
    /**
     * @param $categoryPath
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function execute($categoryPath, \Http\Request $request) {
        $categoryToken = explode('/', trim($categoryPath, '/'));
        $categoryToken = end($categoryToken);

        $repository = \RepositoryManager::getProductCategory();

        $category = $repository->getEntityByToken($categoryToken);
        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара с токеном "%s" не найдена.', $categoryToken));
        }

        // обязательно загружаем предков и детей
        $repository->loadEntityBranch($category);

        // http://en.wikipedia.org/wiki/Tree_%28data_structure%29
        if ($category->isRoot()) {
            return $this->executeRootNode($category, $request);
        } else if ($category->isBranch()) {
            return $this->executeBranchNode($category, $request);
        }

        return $this->executeLeafNode($category, $request);
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception
     */
    private function executeRootNode(\Model\Product\Category\Entity $category, \Http\Request $request) {
        if (\App::config()->debug) \App::debug()->add('subact', 'rootNode');

        if (!(bool)$category->getChild()) {
            throw new \Exception(sprintf('У категории "%s" отстутсвуют дочерние узлы', $category->getId()));
        }

        $page = new \View\ProductCategory\RootPage();
        $page->setParam('category', $category);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Http\Request $request
     * @return \Http\Response
     */
    private function executeBranchNode(\Model\Product\Category\Entity $category, \Http\Request $request) {
        if (\App::config()->debug) \App::debug()->add('subact', 'branchNode');

        $page = new \View\ProductCategory\BranchPage();
        $page->setParam('category', $category);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    private function executeLeafNode(\Model\Product\Category\Entity $category, \Http\Request $request) {
        if (\App::config()->debug) \App::debug()->add('subact', 'leafNode');

        $pageNum = (int)$request->get('page', 1);
        if ($pageNum < 1) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $pageNum));
        }

        // вид товаров
        $productView = $request->get('view', $category->getProductView());
        // фильтры
        $productFilter = $this->getFilter($category);
        // листалка
        $productPager = $this->getPager($productFilter, $pageNum, $productView);

        // ajax
        if ($request->isXmlHttpRequest()) {
            return new \Http\Response(\App::templating()->render('product/_list', array(
                'page'   => new \View\DefaultLayout(),
                'pager'  => $productPager,
                'view'   => $productView,
                'isAjax' => true,
            )));
        }

        $page = new \View\ProductCategory\LeafPage();
        $page->setParam('category', $category);
        $page->setParam('productFilter', $productFilter);
        $page->setParam('productPager', $productPager);
        $page->setParam('productView', $productView);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @return \Model\Product\Filter
     */
    private function getFilter(\Model\Product\Category\Entity $category = null) {
        $filters = \RepositoryManager::getProductFilter()->getCollectionByCategory($category);
        $productFilter = new \Model\Product\Filter($category, $filters);

        return $productFilter;
    }

    /**
     * @param \Model\Product\Filter $productFilter
     * @param $pageNum
     * @param $productView
     * @return \Iterator\EntityPager
     * @throws \Exception\NotFoundException
     */
    private function getPager(\Model\Product\Filter $productFilter, $pageNum, $productView) {
        $limit = \App::config()->product['itemsPerPage'];

        $repository = \RepositoryManager::getProduct();
        $repository->setEntityClass(
            \Model\Product\Category\Entity::PRODUCT_VIEW_EXPANDED == $productView
                ? '\\Model\\Product\\ExpandedEntity'
                : '\\Model\\Product\\CompactEntity'
        );

        $productPager = $repository->getIteratorByFilter(
            $productFilter->dump(),
            array(),
            ($pageNum - 1) * $limit,
            $limit
        );
        $productPager->setPage($pageNum);
        $productPager->setMaxPerPage($limit);

        // проверка на максимально допустимый номер страницы
        if ($productPager->getPage() > $productPager->getLastPage()) {
           throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $productPager->getPage()));
        }

        return $productPager;
    }
}