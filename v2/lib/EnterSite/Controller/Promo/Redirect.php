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

        $url = '/';

        return (new Controller\Redirect())->execute($url, 302);
    }
}