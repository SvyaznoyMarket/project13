<?php

namespace EnterSite\Action\ProductCatalog\ChildCategory;

use EnterSite\MustacheRendererTrait;

use Enter\Http\Request;
use Enter\Http\Response;
use EnterSite\Action\Region\GetIdByHttpRequest as GetRegionId;
use EnterSite\Action\Product\Category\GetTokenByHttpRequest as GetCategoryToken;
use EnterSite\Action\PageNum\GetByHttpRequest as GetPageNum;
use EnterSite\Action\Product\Filter\GetRequestObjectListByHttpRequest as GetRequestFilterList;
use EnterSite\Action\Product\Sorting\GetObjectByHttpRequest as GetSorting;

class GetHttpResponseByHttpRequest {
    use MustacheRendererTrait;

    public function execute(Request $request) {
        // ид региона
        $regionId = (new GetRegionId())->execute($request);

        // токен категории
        $categoryToken = (new GetCategoryToken())->execute($request);

        // номер страницы
        $pageNum = (new GetPageNum())->execute($request);

        // фильтры в запросе
        $filters = (new GetRequestFilterList())->execute($request);

        // сортировка
        $sorting = (new GetSorting())->execute($request);

        $page = (new GetPageByCategoryToken())->execute(
            $regionId,
            $categoryToken,
            $pageNum,
            $filters,
            $sorting
        );

        $renderer = $this->getRenderer();
        $renderer->setPartials([
            'content' => 'page/product-catalog/child-category/content',
        ]);
        $response = new Response($renderer->render('layout/default', $page));

        return $response;
    }
}