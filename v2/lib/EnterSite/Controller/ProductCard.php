<?php

namespace EnterSite\Controller;

use Enter\Exception;
use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\Repository;
use EnterSite\Curl\Query;
use EnterSite\Model;
//use EnterSite\Model\Page\ProductCard as Page;

class ProductCard {
    use ConfigTrait;
    use CurlClientTrait, MustacheRendererTrait {
        ConfigTrait::getConfig insteadof CurlClientTrait;
        ConfigTrait::getConfig insteadof MustacheRendererTrait;
    }

    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();

        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequest($request);

        // токен товара
        $productToken = (new Repository\Product)->getTokenByHttpRequest($request);

        // запрос региона
        $regionQuery = new Query\Region\GetItemById($regionId);
        $curl->prepare($regionQuery);

        $curl->execute(1, 2);

        // регион
        $region = (new Repository\Region())->getObjectByQuery($regionQuery);

        // запрос товара
        $productItemQuery = new Query\Product\GetItemByToken($productToken, $region);
        $curl->prepare($productItemQuery);

        $curl->execute(1, 2);

        // товар
        $product = (new Repository\Product())->getObjectByQuery($productItemQuery);
        if ($product->link !== $request->getPathInfo()) {
            $redirect = new Exception\PermanentlyRedirect();
            $redirect->setLink($product->link. ((bool)$request->getQueryString() ? ('?' . $request->getQueryString()) : ''));

            throw $redirect;
        }

        die(var_dump($product));


    }
}