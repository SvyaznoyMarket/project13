<?php

namespace EnterTerminal\Controller;

use Enter\Http;
use Enter\Util\JsonDecoderTrait;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\LoggerTrait;
use EnterSite\SessionTrait;
use EnterSite\Curl\Query;
use EnterSite\Model;
use EnterSite\Repository;
use EnterTerminal\Model\Page\Compare as Page;

class Compare {
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
        $session = $this->getSession();
        $curl = $this->getCurlClient();
        $compareRepository = new Repository\Compare();

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

        // сравнение из сессии
        $compare = $compareRepository->getObjectByHttpSession($session);

        $productsById = [];
        foreach ($compare->product as $compareProduct) {
            $productsById[$compareProduct->id] = null;
        }

        $productListQuery = null;
        if ((bool)$productsById) {
            $productListQuery = new Query\Product\GetListByIdList(array_keys($productsById), $shop->regionId);
            $curl->prepare($productListQuery);
        }

        $curl->execute();

        if ($productListQuery) {
            $productsById = (new Repository\Product())->getIndexedObjectListByQueryList([$productListQuery], function(&$item) {
                // оптимизация
                $item['media'] = [reset($item['media'])];
            });
        }

        // сравнение свойств товара
        $compareRepository->compareProductObjectList($compare, $productsById);

        // страница
        $page = new Page();
        $page->groups = $compareRepository->getGroupListByObject($compare, $productsById);
        foreach ($compare->product as $compareProduct) {
            $product = !empty($productsById[$compareProduct->id])
                ? $productsById[$compareProduct->id]
                : new Model\Product([
                    'id' => $compareProduct->id,
                ]);

            $page->products[] = $product;
        }

        // response
        return new Http\JsonResponse($page);
    }
}