<?php

namespace EnterSite\Controller\Cart;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\LoggerTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\RouterTrait;
use EnterSite\Routing;
use EnterSite\Controller;
use EnterSite\Curl\Query;
use EnterSite\Model;
use EnterSite\Model\Page\Cart\Index as Page;
use EnterSite\Repository;

class Index {
    use ConfigTrait, RouterTrait, LoggerTrait, CurlClientTrait, MustacheRendererTrait {
        ConfigTrait::getConfig insteadof RouterTrait, LoggerTrait, CurlClientTrait, MustacheRendererTrait;
        LoggerTrait::getLogger insteadof CurlClientTrait;
    }

    /**
     * @param Http\Request $request
     * @return Http\Response
     * @throws \Exception
     */
    public function execute(Http\Request $request) {
        $curl = $this->getCurlClient();

        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequestCookie($request);

        // запрос региона
        $regionQuery = new Query\Region\GetItemById($regionId);
        $curl->prepare($regionQuery);

        $curl->execute(1, 2);

        // регион
        $region = (new Repository\Region())->getObjectByQuery($regionQuery);

        // запрос дерева категорий для меню
        $categoryListQuery = new Query\Product\Category\GetTreeList($region, 3);
        $curl->prepare($categoryListQuery);

        // запрос меню
        $mainMenuQuery = new Query\MainMenu\GetItem();
        $curl->prepare($mainMenuQuery);

        $curl->execute(1, 2);

        // меню
        $mainMenu = (new Repository\MainMenu())->getObjectByQuery($mainMenuQuery, $categoryListQuery);

        // build page
        $page = new Page();

        $page->mainMenu = $mainMenu;

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