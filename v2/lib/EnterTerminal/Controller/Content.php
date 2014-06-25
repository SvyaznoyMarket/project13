<?php

namespace EnterTerminal\Controller;

use Enter\Http;
use EnterTerminal\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\Controller;
use EnterTerminal\Repository;
use EnterSite\Curl\Query;
use EnterSite\Model;
use EnterTerminal\Model\Page\Content as Page;

class Content {
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

        /*
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
        */

        $contentToken = $request->query['contentToken'];
        if (!$contentToken) {
            throw new \Exception('Не передан contentToken');
        }

        $contentItemQuery = new Query\Content\GetItemByToken($contentToken);
        $curl->prepare($contentItemQuery);

        $curl->execute();

        // страница
        $page = new Page();

        $item = $contentItemQuery->getResult();
        $page->content = preg_replace('/<a(\s+[^>]*href="http:\/\/www\.enter\.ru\/)([^"\s]*)/i', '<a$1$2" data-content-token="$2"', $item['content']);
        $page->title = isset($item['title']) ? $item['title'] : null;

        return new Http\JsonResponse($page);
    }
}