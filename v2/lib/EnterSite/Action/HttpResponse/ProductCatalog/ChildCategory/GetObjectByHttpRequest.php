<?php

namespace EnterSite\Action\HttpResponse\ProductCatalog\ChildCategory;

use EnterSite\Action\Page\ProductCatalog\ChildCategory\GetObjectByToken;
use EnterSite\ConfigTrait;
use EnterSite\MustacheRendererTrait;

use Enter\Http\Request;
use Enter\Http\Response;
use EnterSite\Action\Region\GetIdByHttpRequest as GetRegionId;
use EnterSite\Action\Product\Category\GetTokenByHttpRequest as GetCategoryToken;
use EnterSite\Action\PageNum\GetByHttpRequest as GetPageNum;
use EnterSite\Action\Product\Filter\GetRequestObjectListByHttpRequest as GetRequestFilterList;
use EnterSite\Action\Product\Sorting\GetObjectByHttpRequest as GetSorting;

class GetObjectByHttpRequest {
    use ConfigTrait;
    //use MustacheRendererTrait;
    use MustacheRendererTrait {
        ConfigTrait::getConfig insteadof MustacheRendererTrait;
    }

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

        $page = (new GetObjectByToken())->execute(
            $regionId,
            $categoryToken,
            $pageNum,
            $filters,
            $sorting
        );

        $renderer = $this->getRenderer();
        $renderer->setPartials([
            'content' => 'page/product-catalog/child-category/content',
            //'content' => file_get_contents($this->getConfig()->mustacheRenderer->templateDir . '/page/product-catalog/child-category/content.mustache'),
        ]);
        $content = $renderer->render('layout/default', $page);

        $response = new Response($content);

        return $response;
    }
}