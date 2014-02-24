<?php

namespace EnterSite\Controller\Product;

use Enter\Exception;
use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\Repository;
use EnterSite\Curl\Query;
use EnterSite\Model;
//use EnterSite\Model\Page\Product\RecommendedList as Page;

class RecommendedList {
    use ConfigTrait;
    use CurlClientTrait, MustacheRendererTrait {
        ConfigTrait::getConfig insteadof CurlClientTrait;
        ConfigTrait::getConfig insteadof MustacheRendererTrait;
    }

    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();
        $productRepository = new Repository\Product();

        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequest($request);

        // токен товара
        $productId = $productRepository->getIdByHttpRequest($request);
    }
}