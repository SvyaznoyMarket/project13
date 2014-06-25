<?php

namespace EnterSite\Controller\ProductCatalog;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\DebugContainerTrait;
use EnterSite\Controller;
use EnterSite\Repository;
use EnterSite\Curl\Query;
use EnterSite\Model;
use EnterSite\Model\Page\ProductCatalog\RootCategory as Page;

class RootCategory {
    use ConfigTrait, CurlClientTrait, MustacheRendererTrait, DebugContainerTrait {
        ConfigTrait::getConfig insteadof CurlClientTrait, MustacheRendererTrait, DebugContainerTrait;
    }

    /**
     * @param Http\Request $request
     * @return Http\Response
     */
    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();
        $productRepository = new Repository\Product();
        $productCategoryRepository = new Repository\Product\Category();

        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequestCookie($request);

        // токен категории
        $categoryToken = $productCategoryRepository->getTokenByHttpRequest($request);

        // запрос региона
        $regionQuery = new Query\Region\GetItemById($regionId);
        $curl->prepare($regionQuery);

        $curl->execute();

        // регион
        $region = (new Repository\Region())->getObjectByQuery($regionQuery);

        // запрос категории
        $categoryItemQuery = new Query\Product\Category\GetItemByToken($categoryToken, $region->id);
        $curl->prepare($categoryItemQuery);

        $categoryAdminItemQuery = null;
        if ($config->adminService->enabled) {
            $categoryAdminItemQuery = new Query\Product\Category\GetAdminItemByToken($categoryToken, $region->id);
            $curl->prepare($categoryAdminItemQuery);
        }

        $curl->execute();

        // категория
        $category = $productCategoryRepository->getObjectByQuery($categoryItemQuery, $categoryAdminItemQuery);
        if (!$category) {
            // костыль для ядра
            $categoryUi = isset($categoryAdminItemQuery->getResult()['ui']) ? $categoryAdminItemQuery->getResult()['ui'] : null;
            $categoryItemQuery = $categoryUi ? new Query\Product\Category\GetItemByUi($categoryUi, $region->id) : null;

            if ($categoryItemQuery) {
                $curl->prepare($categoryItemQuery)->execute();
                $category = $productCategoryRepository->getObjectByQuery($categoryItemQuery, $categoryAdminItemQuery);
            }
        }

        if (!$category) {
            return (new Controller\Error\NotFound())->execute($request, sprintf('Категория товара @%s не найдена', $categoryToken));
        }
        if ($category->redirectLink) {
            return (new Controller\Redirect())->execute($category->redirectLink . ((bool)$request->getQueryString() ? ('?' . $request->getQueryString()) : ''), Http\Response::STATUS_MOVED_PERMANENTLY);
        }

        // запрос предка категории
        $branchCategoryItemQuery = new Query\Product\Category\GetBranchItemByCategoryObject($category, $region->id);
        $curl->prepare($branchCategoryItemQuery);

        // запрос дерева категорий для меню
        $categoryListQuery = new Query\Product\Category\GetTreeList($region->id, 3);
        $curl->prepare($categoryListQuery);

        $curl->execute();

        // предки и дети категории
        $productCategoryRepository->setBranchForObjectByQuery($category, $branchCategoryItemQuery);

        // запрос меню
        $mainMenuQuery = new Query\MainMenu\GetItem();
        $curl->prepare($mainMenuQuery);

        // запрос настроек каталога
        $catalogConfigQuery = new Query\Product\Catalog\Config\GetItemByProductCategoryObject(array_merge($category->ascendants, [$category]));
        $curl->prepare($catalogConfigQuery);

        $curl->execute();

        // меню
        $mainMenu = (new Repository\MainMenu())->getObjectByQuery($mainMenuQuery, $categoryListQuery);

        // настройки каталога
        $catalogConfig = (new Repository\Product\Catalog\Config())->getObjectByQuery($catalogConfigQuery);

        // запрос для получения страницы
        $pageRequest = new Repository\Page\ProductCatalog\RootCategory\Request();
        $pageRequest->region = $region;
        $pageRequest->mainMenu = $mainMenu;
        $pageRequest->category = $category;
        $pageRequest->catalogConfig = $catalogConfig;

        // страница
        $page = new Page();
        (new Repository\Page\ProductCatalog\RootCategory())->buildObjectByRequest($page, $pageRequest);

        // debug
        if ($config->debugLevel) $this->getDebugContainer()->page = $page;
        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        // рендер
        $renderer = $this->getRenderer();
        $renderer->setPartials([
            'content' => 'page/product-catalog/root-category/content',
        ]);
        $content = $renderer->render('layout/default', $page);

        // http-ответ
        $response = new Http\Response($content);

        return $response;
    }
}