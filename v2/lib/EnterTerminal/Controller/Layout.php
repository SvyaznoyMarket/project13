<?php

namespace EnterTerminal\Controller;

use Enter\Http;
use EnterTerminal\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\Controller;
use EnterTerminal\Repository;
use EnterSite\Curl\Query;
use EnterSite\Model;
use EnterTerminal\Model\Layout as Page;

class Layout {
    use ConfigTrait, CurlClientTrait {
        ConfigTrait::getConfig insteadof CurlClientTrait;
    }

    /**
     * @param Http\Request $request
     * @throws \Exception
     * @return Http\JsonResponse
     */
    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();

        // ид магазина
        $shopId = (new Repository\Shop())->getIdByHttpRequest($request);

        // запрос магазина
        $shopItemQuery = new Query\Shop\GetItemById($shopId);
        $curl->prepare($shopItemQuery);

        $curl->execute();

        // магазин
        $shop = (new Repository\Shop())->getObjectByQuery($shopItemQuery);
        if (!$shop) {
            throw new \Exception(sprintf('Магазин #%s не найден', $shopId));
        }

        // запрос дерева категорий для меню
        $categoryListQuery = new Query\Product\Category\GetTreeList($shop->regionId, 3);
        $curl->prepare($categoryListQuery);

        // запрос меню
        $mainMenuQuery = new Query\MainMenu\GetItem();
        $curl->prepare($mainMenuQuery);

        $curl->execute();

        // меню
        $mainMenu = (new Repository\MainMenu())->getObjectByQuery($mainMenuQuery, $categoryListQuery);

        // страница
        $page = new Page();
        $page->region = $shop->region;
        $page->shop = $shop;
        $page->mainMenu = $mainMenu;

        return new Http\JsonResponse($page);
    }
}