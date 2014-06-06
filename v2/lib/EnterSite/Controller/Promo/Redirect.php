<?php

namespace EnterSite\Controller\Promo;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\CurlClientTrait;
use EnterSite\RouterTrait;
use EnterSite\Controller;
use EnterSite\Routing;
use EnterSite\Repository;
use EnterSite\Curl\Query;
use EnterSite\Model;

class Redirect {
    use ConfigTrait, LoggerTrait, CurlClientTrait, RouterTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, CurlClientTrait, RouterTrait;
        LoggerTrait::getLogger insteadof CurlClientTrait;
    }

    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();
        $router = $this->getRouter();
        $productCategoryRepository = new Repository\Product\Category();
        $promoRepository = new Repository\Promo();

        // ид баннера
        $promoId = $promoRepository->getIdByHttpRequest($request);

        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequestCookie($request);

        // запрос региона
        $regionQuery = new Query\Region\GetItemById($regionId);
        $curl->prepare($regionQuery);

        $curl->execute();

        // регион
        $region = (new Repository\Region())->getObjectByQuery($regionQuery);

        // запрос баннеров
        $promoListQuery = new Query\Promo\GetList($region->id);
        $curl->prepare($promoListQuery);

        $curl->execute();

        // баннеры
        $promo = $promoRepository->getObjectByIdAndQuery($promoId, $promoListQuery);
        if (!$promo) {
            return (new Controller\Redirect())->execute($router->getUrlByRoute(new Routing\Index()), 500);
        }
        //die(var_dump($promo));

        $url = '/';
        if (false !== strpos($promo->url, '/')) {
            $url = $promo->url;
        } else {
            $productIds = [];
            $productCategoryIds = [];
            foreach ($promo->items as $item) {
                if ((Model\Promo\Item::TYPE_PRODUCT === $item->typeId) && $item->productId) {
                    $productIds[] = $item->productId;
                } else  if ((Model\Promo\Item::TYPE_PRODUCT_CATEGORY === $item->typeId) && $item->productCategoryId) {
                    $productCategoryIds[] = $item->productCategoryId;
                }
            }

            if ((bool)$productIds) {
                $productListQuery = new Query\Product\GetListByIdList($productIds);
                $curl->prepare($productListQuery);
            }
            if ((bool)$productCategoryIds) {
                $productCategoryListQuery = new Query\Product\Category\GetListByIdList($productCategoryIds);
                $curl->prepare($productCategoryListQuery);
            }

            $curl->execute();
        }

        return (new Controller\Redirect())->execute($url, 302);
    }
}