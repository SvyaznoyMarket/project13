<?php

namespace EnterSite\Repository;

use Enter\Exception;
use Enter\Http;
use Enter\Curl\Query;
use EnterSite\ConfigTrait;
use EnterSite\Model;

class Product {
    use ConfigTrait;

    /**
     * @param Http\Request $request
     * @return string
     */
    public function getTokenByHttpRequest(Http\Request $request) {
        $token = explode('/', $request->query['productPath']);
        $token = end($token);

        return $token;
    }

    /**
     * @param Query $query
     * @return Model\Product
     * @throws Exception\NotFound
     */
    public function getObjectByQuery(Query $query) {
        $product = null;

        $item = $query->getResult();
        if (!$item) {
            throw new Exception\NotFound('Товар не найден');
        }

        $product = new Model\Product($item);

        return $product;
    }

    /**
     * @param Query $query
     * @return Model\Product[]
     */
    public function getObjectListByQueryIndexedById(Query $query) {
        $products = [];

        foreach ($query->getResult() as $item) {
            $item['property'] = []; // оптимизация
            $item['property_group'] = []; // оптимизация
            $item['media'] = [reset($item['media'])]; // оптимизация
            $products[$item['id']] = new Model\Product($item);
        }

        return $products;
    }

    /**
     * @param Model\Product $product
     * @param Query $videoListQuery
     */
    public function setVideoForObjectByQuery(Model\Product $product, Query $videoListQuery) {
        try {
            foreach ($videoListQuery->getResult() as $videoItem) {
                $product->media->videos[] = new Model\Product\Media\Video($videoItem);
            }
        } catch (\Exception $e) { }
    }

    /**
     * @param Model\Product[] $productsById
     * @param Query $videoGroupedListQuery
     */
    public function setVideoForObjectListByQuery(array $productsById, Query $videoGroupedListQuery) {
        try {
            foreach ($videoGroupedListQuery->getResult() as $videoItem) {
                if (!isset($productsById[$videoItem['product_id']])) continue;

                $productsById[$videoItem['product_id']]->media->videos[] = new Model\Product\Media\Video($videoItem);
            }
        } catch (\Exception $e) { }
    }

    /**
     * @param Model\Product[] $productsById
     * @param Query $ratingListQuery
     */
    public function setRatingForListByQuery(array $productsById, Query $ratingListQuery) {
        try {
            foreach ($ratingListQuery->getResult() as $ratingItem) {
                if (!isset($productsById[$ratingItem['product_id']])) continue;

                $productsById[$ratingItem['product_id']]->rating = new Model\Product\Rating($ratingItem);
            }
        } catch (\Exception $e) {
            //trigger_error($e, E_USER_ERROR);
        }
    }
}