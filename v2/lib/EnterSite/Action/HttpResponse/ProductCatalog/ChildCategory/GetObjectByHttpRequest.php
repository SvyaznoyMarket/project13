<?php

namespace EnterSite\Action\HttpResponse\ProductCatalog\ChildCategory;

use EnterSite\ConfigTrait;
use EnterSite\MustacheRendererTrait;

use Enter\Http;
use EnterSite\Action;

class GetObjectByHttpRequest {
    use ConfigTrait;
    //use MustacheRendererTrait;
    use MustacheRendererTrait {
        ConfigTrait::getConfig insteadof MustacheRendererTrait;
    }

    public function execute(Http\Request $request) {
        // ид региона
        $regionId = (new Action\Region\GetIdByHttpRequest())->execute($request);

        // токен категории
        $categoryToken = (new Action\Product\Category\GetTokenByHttpRequest())->execute($request);

        // номер страницы
        $pageNum = (new Action\PageNum\GetByHttpRequest())->execute($request);

        // фильтры в запросе
        $filters = (new Action\Product\Filter\GetRequestObjectListByHttpRequest())->execute($request);

        // сортировка
        $sorting = (new Action\Product\Sorting\GetObjectByHttpRequest())->execute($request);

        $page = (new Action\Page\ProductCatalog\ChildCategory\GetObjectByToken())->execute(
            $categoryToken,
            $regionId,
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

        $response = new Http\Response($content);

        return $response;
    }
}