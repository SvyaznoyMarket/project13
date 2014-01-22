<?php

namespace EnterSite\Controller\ProductCatalog;

use EnterSite\ConfigTrait;
use EnterSite\MustacheRendererTrait;

use Enter\Http;
use EnterSite\Repository;

class ChildCategory {
    use ConfigTrait;
    use MustacheRendererTrait {
        ConfigTrait::getConfig insteadof MustacheRendererTrait;
    }

    public function execute(Http\Request $request) {
        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequest($request);

        // токен категории
        $categoryToken = (new Repository\Product\Category())->getTokenByHttpRequest($request);

        // номер страницы
        $pageNum = (new Repository\PageNum())->getByHttpRequest($request);

        // фильтры в запросе
        $filters = (new Repository\Product\Filter())->getRequestObjectListByHttpRequest($request);

        // сортировка
        $sorting = (new Repository\Product\Sorting())->getObjectByHttpRequest($request);

        $page = (new Repository\Page\ProductCatalog\ChildCategory())->getObjectByToken(
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