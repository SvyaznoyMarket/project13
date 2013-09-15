<?php

namespace Model\Product\Video;

class Repository {
    /**
     * @param \DataStore\Client $client
     */
    public function __construct(\DataStore\Client $client) {
        $this->client = $client;
    }

    /**
     * @param \Model\Product\BasicEntity $product
     * @return Entity[]
     */
    public function getCollectionByProduct(\Model\Product\BasicEntity $product) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $data = $this->client->query(sprintf('video/product/%s.json', $product->getId()));

        return is_array($data)
            ? array_map(function (array $item) { return new Entity($item); }, $data)
            : [];
    }

    /**
     * @param array         $productIds
     * @param callback      $done
     * @param callback|null $fail
     */
    public function prepareCollectionByProductIds(array $productIds, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery(sprintf('video/product/index.json'), [
            'id' => $productIds,
        ], $done, $fail);
    }


    /**
     * @param \Iterator\EntityPager $productPager
     * @param array                 $productVideosByProduct
     * @return array
     */
    public function getVideoByProductPager($productPager, $productVideosByProduct = [])
    {
        foreach ($productPager as $product) {
            /** @var $product \Model\Product\Entity */
            $productVideosByProduct[$product->getId()] = [];
        }
        return $this->getVideosByProduct($productVideosByProduct);
    }


    /**
     * @param array     $productVideosByProduct
     * @return array    [productId => array [Model\Product\Video\Entity] ]
     */
    public function getVideosByProduct( $productVideosByProduct ) {
        if ((bool)$productVideosByProduct) {
            $this->prepareCollectionByProductIds(array_keys($productVideosByProduct), function($data) use (&$productVideosByProduct) {
                foreach ($data as $id => $items) {
                    if (!is_array($items)) continue;
                    foreach ($items as $item) {
                        if (!$item) continue;
                        $productVideosByProduct[$id][] = new \Model\Product\Video\Entity((array)$item);
                    }
                }
            });
            \App::dataStoreClient()->execute(\App::config()->dataStore['retryTimeout']['tiny'], \App::config()->dataStore['retryCount']);
        }
        return $productVideosByProduct;
    }

}