<?php

namespace Model\Brand;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    /**
     * @param \Core\ClientInterface $client
     */
    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @param string               $token
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareEntityByToken($token, \Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('brand/get', [
            'token'  => $token,
            'geo_id' => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ], [], $callback);
    }

    /**
     * @param \Model\Product\Category\BasicEntity $category
     * @param int                                 $limit
     * @param int                                 $offset
     * @return array
     */
    public function getCollectionByCategory(\Model\Product\Category\BasicEntity $category, $limit, $offset) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $collection = [];
        $client->addQuery('brand/get-by-category', [
            'category_id' => [$category->getId()],
            'limit'       => $limit,
            'offset'      => $offset,
        ], [], function($data) use (&$collection) {
            foreach ($data as $entity) {
                $collection[] = new Entity($entity);
            }
        });

        $client->execute(\App::config()->coreV2['retryTimeout']['short'], \App::config()->coreV2['retryCount']);

        return $collection;
    }
}