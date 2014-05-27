<?php

namespace EnterSite\Repository\Page\ProductCatalog;

use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\RouterTrait;
use EnterSite\ViewHelperTrait;
use EnterSite\Routing;
use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;
use EnterSite\Model\Page\ProductCatalog\ChildCategory as Page;

class ChildCategory {
    use ConfigTrait, LoggerTrait, RouterTrait, ViewHelperTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, RouterTrait, ViewHelperTrait;
    }

    /**
     * @param Page $page
     * @param ChildCategory\Request $request
     */
    public function buildObjectByRequest(Page $page, ChildCategory\Request $request) {
        (new Repository\Page\DefaultLayout)->buildObjectByRequest($page, $request);

        $config = $this->getConfig();
        $router = $this->getRouter();
        $viewHelper = $this->getViewHelper();

        $templateDir = $config->mustacheRenderer->templateDir;

        $productCardRepository = new Repository\Partial\ProductCard();
        $cartProductButtonRepository = new Repository\Partial\Cart\ProductButton();

        $page->dataModule = 'product.catalog';

        $page->content->productBlock = false;
        $page->content->sortingBlock = false;
        if ((bool)$request->products) {
            // TODO: вынести productBlock в репозиторий
            $page->content->productBlock = new Page\Content\ProductBlock();
            $page->content->productBlock->limit = $config->product->itemPerPage;
            $page->content->productBlock->url = $router->getUrlByRoute(new Routing\Product\GetListByFilter());
            // [data-value]
            $dataValue = [
                'page'       => 2,
                'limit'      => $page->content->productBlock->limit,
                'count'      => $request->count,
                'sort'       => ('default' == $request->sorting->token) ? null : ($request->sorting->token . '-' . $request->sorting->direction),
            ];
            foreach ($request->requestFilters as $requestFilter) {
                if (!$requestFilter->name) {
                    $this->getLogger()->push(['type' => 'warn', 'message' => 'Пустой токен', 'requestFilter' => $requestFilter, 'action' => __METHOD__, 'tag' => ['repository']]);
                    continue;
                }
                $dataValue[$requestFilter->name] = $requestFilter->value;
            }
            $page->content->productBlock->dataValue = $viewHelper->json($dataValue);

            foreach ($request->products as $productModel) {
                $productCard = $productCardRepository->getObject($productModel, $cartProductButtonRepository->getObject($productModel));

                $page->content->productBlock->products[] = $productCard;
            }

            $page->content->sortingBlock = new Page\Content\SortingBlock();
            $page->content->sortingBlock->sortings = (new Repository\Partial\Sorting())->getList(
                $request->sortings,
                $request->sorting,
                new Routing\ProductCatalog\GetChildCategory($request->category->path),
                $request->httpRequest
            );

            $page->content->productBlock->moreLink = (new Repository\Partial\ProductList\MoreLink())->getObject($request->pageNum, $request->limit, $request->count) ?: false;

            // фильтры
            $page->content->filterBlock = new Page\Content\FilterBlock();
            $page->content->filterBlock->filters = (new Repository\Partial\ProductFilter())->getList($request->filters, $request->requestFilters);
        }

        // шаблоны mustache
        foreach ([
            [
                'id'   => 'tpl-productList-moreLink',
                'name' => 'partial/product-list/moreLink',
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