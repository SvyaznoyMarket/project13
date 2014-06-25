<?php

namespace EnterSite\Repository\Page;

use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\RouterTrait;
use EnterSite\ViewHelperTrait;
use EnterSite\Routing;
use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;
use EnterSite\Model\Page\Search\Index as Page;

class Search {
    use ConfigTrait, LoggerTrait, RouterTrait, ViewHelperTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, RouterTrait, ViewHelperTrait;
    }

    /**
     * @param Page $page
     * @param Search\Request $request
     */
    public function buildObjectByRequest(Page $page, Search\Request $request) {
        (new Repository\Page\DefaultLayout)->buildObjectByRequest($page, $request);

        $config = $this->getConfig();
        $router = $this->getRouter();
        $viewHelper = $this->getViewHelper();

        $templateDir = $config->mustacheRenderer->templateDir;

        $productCardRepository = new Repository\Partial\ProductCard();
        $cartProductButtonRepository = new Repository\Partial\Cart\ProductButton();

        $currentRoute = new Routing\Search\Index($request->searchPhrase);

        $page->dataModule = 'product.catalog';

        $page->content->title = (bool)$request->products ? 'Нашли ' . '"' . $request->searchPhrase . '"' : '';

        // хлебные крошки
        $page->breadcrumbBlock = new Model\Page\DefaultLayout\BreadcrumbBlock();
        $breadcrumb = new Model\Page\DefaultLayout\BreadcrumbBlock\Breadcrumb();
        $breadcrumb->name = 'Поиск ' . '"' . $request->searchPhrase . "'";
        $breadcrumb->url = $router->getUrlByRoute($currentRoute);
        $page->breadcrumbBlock->breadcrumbs[] = $breadcrumb;

        $page->content->categoryBlock = false;
        /*
        if ((bool)$request->categories) {
            $page->content->categoryBlock = new Partial\ProductCatalog\CategoryBlock();
            foreach ($request->categories as $childCategoryModel) {
                $childCategory = new Partial\ProductCatalog\CategoryBlock\Category();
                $childCategory->name = $childCategoryModel->name;
                $childCategory->url = $childCategoryModel->link;
                $childCategory->image = (string)(new Routing\Product\Category\GetImage($childCategoryModel->image, $childCategoryModel->id, 1));

                $page->content->categoryBlock->categories[] = $childCategory;
            }
        }
        */

        $page->content->productBlock = false;
        $page->content->sortingBlock = false;
        if ((bool)$request->products) {
            // TODO: вынести productBlock в репозиторий
            $page->content->productBlock = new Partial\ProductBlock();
            $page->content->productBlock->limit = $config->product->itemPerPage;
            $page->content->productBlock->url = $router->getUrlByRoute(new Routing\Product\GetListByFilter());
            // [data-reset] && [data-value]
            $dataReset = [
                'page'       => 1,
                'limit'      => $page->content->productBlock->limit,
                'count'      => $request->count,
                'q'          => $request->searchPhrase,
                'sort'       => ('default' == $request->sorting->token) ? null : ($request->sorting->token . '-' . $request->sorting->direction),
            ];

            $dataValue = $dataReset;
            $dataValue['page']++;
            foreach ($request->requestFilters as $requestFilter) {
                if (!$requestFilter->name) {
                    $this->getLogger()->push(['type' => 'warn', 'message' => 'Пустой токен', 'requestFilter' => $requestFilter, 'action' => __METHOD__, 'tag' => ['repository']]);
                    continue;
                }
                $dataValue[$requestFilter->name] = $requestFilter->value;
                if ('category' == $requestFilter->token) {
                    $dataReset[$requestFilter->name] = $requestFilter->value;
                }
            }
            $page->content->productBlock->dataValue = $viewHelper->json($dataValue);
            $page->content->productBlock->dataReset = $viewHelper->json($dataReset);

            foreach ($request->products as $productModel) {
                $productCard = $productCardRepository->getObject($productModel, $cartProductButtonRepository->getObject($productModel));

                $page->content->productBlock->products[] = $productCard;
            }

            $page->content->sortingBlock = (new Repository\Partial\ProductSortingBlock())->getObject(
                $request->sortings,
                $request->sorting,
                $currentRoute,
                $request->httpRequest
            );

            $page->content->productBlock->moreLink = (new Repository\Partial\ProductList\MoreLink())->getObject($request->pageNum, $request->limit, $request->count) ?: false;

            // фильтры
            $page->content->filterBlock = false;
            if ((bool)($filters = (new Repository\Partial\ProductFilter())->getList($request->filters, $request->requestFilters, false))) {
                $page->content->filterBlock = new Partial\ProductFilterBlock();
                $page->content->filterBlock->filters = $filters;
                $page->content->filterBlock->openedFilters = (new Repository\Partial\ProductFilter())->getList($request->filters, $request->requestFilters, true);
                $page->content->filterBlock->actionBlock->shownProductCount = sprintf('Показать (%s)', $request->count > 999 ? '&infin;' : $request->count);
            }

            // выбранные фильтры
            $page->content->selectedFilterBlock = new Partial\SelectedFilterBlock();
            $page->content->selectedFilterBlock->filters = (new Repository\Partial\ProductFilter())->getSelectedList(
                $request->filters,
                $request->requestFilters,
                $currentRoute,
                $request->httpRequest
            );
            $page->content->selectedFilterBlock->hasFilter = (bool)$page->content->selectedFilterBlock->filters;
        }

        // шаблоны mustache
        foreach ([
            [
                'id'   => 'tpl-productList-moreLink',
                'name' => 'partial/product-list/moreLink',
            ],
            [
                'id'   => 'tpl-product-selectedFilter',
                'name' => 'partial/product-list/selectedFilter',
            ],
            [
                'id'   => 'tpl-productSorting',
                'name' => 'partial/product-list/sorting',
            ],
            [
                'id'   => 'tpl-productFilter-action',
                'name' => 'partial/product-list/filterAction',
            ],
        ] as $templateItem) {
            try {
                $template = new Model\Page\DefaultLayout\Template();
                $template->id = $templateItem['id'];
                $template->content = file_get_contents($templateDir . '/' . $templateItem['name'] . '.mustache');

                $page->templates[] = $template;
            } catch (\Exception $e) {
                $this->getLogger()->push(['type' => 'error', 'error' => $e, 'action' => __METHOD__, 'tag' => ['template']]);
            }
        }

        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}