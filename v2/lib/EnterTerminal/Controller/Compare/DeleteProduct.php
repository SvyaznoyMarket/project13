<?php

namespace EnterTerminal\Controller\Compare;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\LoggerTrait;
use EnterSite\SessionTrait;
use EnterSite\Curl\Query;
use EnterSite\Model;
use EnterSite\Repository;
use EnterTerminal\Controller;

class DeleteProduct {
    use ConfigTrait, LoggerTrait, CurlClientTrait, SessionTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, CurlClientTrait, SessionTrait;
        LoggerTrait::getLogger insteadof CurlClientTrait, SessionTrait;
    }

    /**
     * @param Http\Request $request
     * @throws \Exception
     * @return Http\JsonResponse
     */
    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();
        $session = $this->getSession();
        $compareRepository = new Repository\Compare();

        // сравнение из сессии
        $compare = $compareRepository->getObjectByHttpSession($session);

        // товара для сравнения
        $compareProduct = $compareRepository->getProductObjectByHttpRequest($request);
        if (!$compareProduct) {
            throw new \Exception('Товар не получен');
        }

        // добавление товара к сравнению
        $compareRepository->deleteProductForObject($compare, $compareProduct);

        // ид магазина
        $shopId = (new \EnterTerminal\Repository\Shop())->getIdByHttpRequest($request); // FIXME

        // запрос магазина
        $shopItemQuery = new Query\Shop\GetItemById($shopId);
        $curl->prepare($shopItemQuery);

        $curl->execute();

        // магазин
        $shop = (new Repository\Shop())->getObjectByQuery($shopItemQuery);
        if (!$shop) {
            throw new \Exception(sprintf('Магазин #%s не найден', $shopId));
        }

        $productItemQuery = new Query\Product\GetItemById($compareProduct->id, $shop->regionId);
        $curl->prepare($productItemQuery);

        $curl->execute();

        // сохранение сравнения в сессию
        $compareRepository->saveObjectToHttpSession($session, $compare);

        // response
        return (new Controller\Compare())->execute($request);
    }
}