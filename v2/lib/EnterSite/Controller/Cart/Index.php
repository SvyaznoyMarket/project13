<?php

namespace EnterSite\Controller\Cart;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\LoggerTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\RouterTrait;
use EnterSite\SessionTrait;
use EnterSite\DebugContainerTrait;
use EnterSite\Routing;
use EnterSite\Controller;
use EnterSite\Curl\Query;
use EnterSite\Model;
use EnterSite\Model\Page\Cart\Index as Page;
use EnterSite\Repository;

class Index {
    use ConfigTrait, RouterTrait, LoggerTrait, CurlClientTrait, SessionTrait, MustacheRendererTrait, DebugContainerTrait {
        ConfigTrait::getConfig insteadof RouterTrait, LoggerTrait, CurlClientTrait, SessionTrait, MustacheRendererTrait, DebugContainerTrait;
        LoggerTrait::getLogger insteadof SessionTrait, CurlClientTrait;
    }

    /**
     * @param Http\Request $request
     * @return Http\Response
     * @throws \Exception
     */
    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();
        $session = $this->getSession();
        $cartRepository = new Repository\Cart();

        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequestCookie($request);

        // корзина из сессии
        $cart = $cartRepository->getObjectByHttpSession($session);

        // запрос региона
        $regionQuery = new Query\Region\GetItemById($regionId);
        $curl->prepare($regionQuery);

        $cartItemQuery = new Query\Cart\GetItem($cart, $regionId);
        $curl->prepare($cartItemQuery);

        $curl->execute();

        // регион
        $region = (new Repository\Region())->getObjectByQuery($regionQuery);

        $productListQuery = (bool)$cart->product ? new Query\Product\GetListByIdList(array_values(array_map(function(Model\Cart\Product $cartProduct) { return $cartProduct->id; }, $cart->product)), $region->id) : null;
        if ($productListQuery) {
            $curl->prepare($productListQuery);
        }

        // корзина из ядра
        $cart = $cartRepository->getObjectByQuery($cartItemQuery);

        // запрос дерева категорий для меню
        $categoryListQuery = new Query\Product\Category\GetTreeList($region->id, 3);
        $curl->prepare($categoryListQuery);

        // запрос меню
        $mainMenuQuery = new Query\MainMenu\GetItem();
        $curl->prepare($mainMenuQuery);

        $curl->execute();

        $cartProducts = $cart->product;
        $productsById = $productListQuery ? (new Repository\Product)->getIndexedObjectListByQueryList([$productListQuery]) : [];

        // меню
        $mainMenu = (new Repository\MainMenu())->getObjectByQuery($mainMenuQuery, $categoryListQuery);

        // запрос для получения страницы
        $pageRequest = new Repository\Page\Cart\Index\Request();
        $pageRequest->region = $region;
        $pageRequest->mainMenu = $mainMenu;
        $pageRequest->cart = $cart;
        $pageRequest->productsById = $productsById;
        $pageRequest->cartProducts = $cartProducts;

        // страница
        $page = new Page();
        (new Repository\Page\Cart\Index())->buildObjectByRequest($page, $pageRequest);

        // debug
        if ($config->debugLevel) $this->getDebugContainer()->page = $page;
        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        // рендер
        $renderer = $this->getRenderer();
        $renderer->setPartials([
            'content' => 'page/cart/content',
        ]);
        $content = $renderer->render('layout/default', $page);

        // http-ответ
        $response = new Http\Response($content);

        return $response;
    }
}