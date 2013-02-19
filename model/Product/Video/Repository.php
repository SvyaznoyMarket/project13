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
     * @param \Model\Product\BasicEntity $product
     * @param callback                   $done
     * @param callback|null              $fail
     */
    public function prepareCollectionByProduct(\Model\Product\BasicEntity $product, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery(sprintf('video/product/%s.json', $product->getId()), $done, $fail);
    }
}