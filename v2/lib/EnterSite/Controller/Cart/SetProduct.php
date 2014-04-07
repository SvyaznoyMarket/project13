<?php

namespace EnterSite\Controller\Cart;

use Enter\Http;
use Enter\Util\JsonDecoderTrait;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\LoggerTrait;
use EnterSite\Controller;
use EnterSite\Curl\Query;
use EnterSite\Model;
//use EnterSite\Model\JsonPage as Page;
use EnterSite\Repository;

class SetProduct {
    use ConfigTrait;
    use LoggerTrait, CurlClientTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, CurlClientTrait;
        LoggerTrait::getLogger insteadof CurlClientTrait;
    }
    use JsonDecoderTrait;

    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();
        $productRepository = new Repository\Product();

        $response = new Http\JsonResponse();

        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequest($request);

        $productId = (new Repository\Product())->getIdByHttpRequest($request);
        if (!$productId) {
            $response->statusCode = Http\Response::STATUS_BAD_REQUEST;

            return $response;
        }

        // запрос региона
        $regionQuery = new Query\Region\GetItemById($regionId);
        $curl->prepare($regionQuery);

        $curl->execute(1, 2);

        // регион
        $region = (new Repository\Region())->getObjectByQuery($regionQuery);

        // запрос товара
        $productItemQuery = new Query\Product\GetItemById($productId, $region);
        $curl->prepare($productItemQuery);

        $curl->execute(1, 2);

        // товар
        $product = $productRepository->getObjectByQuery($productItemQuery);
        if (!$product) {
            return (new Controller\Error\NotFound())->execute(sprintf('Товар @%s не найден', $productId));
        }

        // TODO: похоже, придется модель описывать
        $productData = $this->jsonToArray($request->getContent());

        $cartProduct = new Model\Cart\Product();
        $cartProduct->id = $product->id;
        $cartProduct->quantity = $productData['cart']['quantity'];

        $productData['buyButton']['templateData'] = (new Repository\Partial\Cart\ProductButton())->getObject($product, $cartProduct);
        $productData['inCart'] = true;

        return new Http\JsonResponse([
            'result' => $productData, // TODO: вынести на уровень JsonPage.result
        ]);
    }
}