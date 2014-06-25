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
        $logger = $this->getLogger();
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

            // запрос списка товаров
            $productListQueries = [];
            foreach (array_chunk($productIds, $config->curl->queryChunkSize) as $idsInChunk) {
                $productListQuery = new Query\Product\GetListByIdList($idsInChunk, $region->id);
                $curl->prepare($productListQuery);

                $productListQueries[] = $productListQuery;
            }

            $productCategoryListQuery = null;
            if ((bool)$productCategoryIds) {
                $productCategoryListQuery = new Query\Product\Category\GetListByIdList($productCategoryIds, $region->id);
                $curl->prepare($productCategoryListQuery);
            }

            $curl->execute();

            if ((bool)$productListQueries) {
                if (1 == count($productIds)) {
                    /** @var \Enter\Curl\Query $productListQuery */
                    $productListQuery = reset($productListQueries);
                    try {
                        $item = $productListQuery->getResult();
                        $item = reset($item);
                        if (!empty($item['link'])) {
                            $url = rtrim((string)$item['link'], '/');
                        }

                    } catch (\Exception $e) {
                        $logger->push(['type' => 'error', 'error' => $e, 'tag' => ['controller', ['promo']]]);
                    }
                } else {
                    $barcodes = [];
                    array_walk($productListQueries, function(\Enter\Curl\Query $query) use (&$barcodes, &$logger) {
                            try {
                                foreach ($query->getResult() as $item) {
                                    if (empty($item['bar_code'])) continue;

                                    $barcodes[] = (string)$item['bar_code'];
                                }
                            } catch (\Exception $e) {
                                $logger->push(['type' => 'error', 'error' => $e, 'tag' => ['controller', ['promo']]]);
                            }
                        });

                    if ((bool)$barcodes) {
                        // FIXME: использовать маршрут
                        $url = sprintf('/products/set/%s', implode(',', $barcodes));
                    }
                }
            } else if ((bool)$productCategoryListQuery) {
                try {
                    $item = $productCategoryListQuery->getResult();
                    $item = reset($item);
                    if (!empty($item['link'])) {
                        $url = rtrim((string)$item['link'], '/');
                    }

                } catch (\Exception $e) {
                    $logger->push(['type' => 'error', 'error' => $e, 'tag' => ['controller', ['promo']]]);
                }
            }
        }

        return (new Controller\Redirect())->execute($url, 302);
    }
}