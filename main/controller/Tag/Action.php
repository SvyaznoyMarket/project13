<?php

namespace Controller\Tag;

class Action {
    public function index($tagToken, \Http\Request $request, $categoryToken = null) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $tag = \RepositoryManager::tag()->getEntityByToken($tagToken);
        if (!$tag) {
            throw new \Exception\NotFoundException(sprintf('Тег @%s не найден', $tagToken));
        }

        $pageNum = (int)$request->get('page', 1);
        if ($pageNum < 1) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s"', $pageNum));
        }

        if (!(bool)$tag->getCategory()) {
            throw new \Exception\NotFoundException(sprintf('Тег "%s" не связан ни с одной категорией', $tag->getToken()));
        }

        // категории
        /** @var $tagCategoriesById \Model\Tag\Category\Entity[] */
        $tagCategoriesById = [];
        foreach ($tag->getCategory() as $tagCategory) {
            $tagCategoriesById[$tagCategory->getId()] = $tagCategory;
        }
        /** @var $categoriesByToken \Model\Product\Category\Entity[] */
        $categoriesByToken = [];
        foreach (\RepositoryManager::productCategory()->getCollectionById(array_keys($tagCategoriesById)) as $category) {
            /** @var $category \Model\Product\Category\Entity */
            $tagCategory = $tagCategoriesById[$category->getId()];
            $category->setProductCount($tagCategory->getProductCount());
            $categoriesByToken[$category->getToken()] = $category;
        }
        if ($categoryToken) {
            if (!isset($categoriesByToken[$categoryToken])) {
                throw new \Exception\NotFoundException(sprintf('Категория @%s не найдена', $categoryToken));
            }
            $category = $categoriesByToken[$categoryToken];
        } else {
            $category = reset($categoriesByToken);
        }

        // сортировка
        $productSorting = new \Model\Product\Sorting();
        list($sortingName, $sortingDirection) = array_pad(explode('-', $request->get('sort')), 2, null);
        $productSorting->setActive($sortingName, $sortingDirection);

        // вид товаров
        $productView = $request->get('view', $category->getHasLine() ? 'line' : $category->getProductView());
        // фильтры
        $filter = new \Model\Product\Filter\Entity();
        $filter->setId('tag');
        $productFilter = new \Model\Product\Filter(array($filter));
        $productFilter->setCategory($category);
        $productFilter->setValues(array('tag' => array($tag->getId())));
        // листалка
        $limit = \App::config()->product['itemsPerPage'];
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

        // получаем из json данные о горячих ссылках и content
        $seoTagJson = \Model\Tag\Repository::getSeoJson($tag);
        $hotlinks = empty($seoTagJson['hotlinks']) ? [] : $seoTagJson['hotlinks'];
        // в json-файле в свойстве content содержится массив
        $seoContent = empty($seoTagJson['content']) ? '' : implode('<br>', $seoTagJson['content']);

        // на страницах пагинации сео-контент не показываем
        if ($pageNum > 1) $seoContent = '';

        $page = new \View\Tag\IndexPage();
        $page->setParam('tag', $tag);
        $page->setParam('productPager', $productPager);
        $page->setParam('productSorting', $productSorting);
        $page->setParam('productView', $productView);
        $page->setParam('category', $category);
        $page->setParam('categories', array_values($categoriesByToken));
        $page->setParam('hotlinks', $hotlinks);
        $page->setParam('seoContent', $seoContent);
        $page->setParam('sidebarHotlinks', true);

        return new \Http\Response($page->show());
    }
}