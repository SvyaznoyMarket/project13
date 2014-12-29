<?php

namespace Model\Shop;

class Repository {
    /** @var \Scms\Client */
    private $client;

    /**
     * @param \Scms\Client $client
     */
    public function __construct(\Scms\Client $client) {
        $this->client = $client;
    }

    /**
     * @param int $id
     * @return Entity|null
     */
    public function getEntityById($id) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $entity = null;
        $client->addQuery('shop/get',
            [
                'id' => $id,
            ],
            [],
            function ($data) use (&$entity) {
                $data = reset($data);
                $entity = $data ? new Entity($data) : null;
            }
        );

        $client->execute();

        return $entity;
    }

    /**
     * @param string $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $entity = null;
        $client->addQuery('shop/get',
            [
                'slug' => $token,
            ],
            [],
            function ($data) use (&$entity) {
                $data = reset($data);
                $entity = $data ? new Entity($data) : null;
            }
        );

        $client->execute();

        return $entity;
    }

    /**
     * @param string $token
     * @param        $callback
     */
    public function prepareEntityByToken($token, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('shop/get', array(
            'slug' => $token,
        ), [], $callback);
    }

    /**
     * @param \Model\Region\Entity $region
     * @return Entity[]
     */
    public function getCollectionByRegion(\Model\Region\Entity $region) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $collection = [];
        $client->addQuery('shop/gets',
            [
                'geo_id' => $region->getId(),
            ],
            [],
            function ($data) use (&$collection) {
                foreach ($data as $item) {
                    $collection[] = new Entity($item);
                }
            }
        );

        $client->execute();

        return $collection;
    }

    /**
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareCollectionByRegion(\Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [];
        if (!is_null($region)) {
            $params['geo_id'] = $region->getId();
        }

        $this->client->addQuery('shop/gets', $params, [], $callback);
    }

    /**
     * @param array $ids
     * @return Entity[]
     */
    public function getCollectionById(array $ids = []) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        if (!(bool)$ids) return [];

        $client = clone $this->client;

        $collection = [];
        $client->addQuery('shop/gets',
            [
                'id' => $ids,
            ],
            [],
            function ($data) use (&$collection) {
                foreach ($data as $item) {
                    $collection[] = new Entity($item);
                }
            }
        );

        $client->execute();

        return $collection;
    }

    /**
     * @param array $ids
     * @param $done
     * @param null $fail
     * @return array
     */
    public function prepareCollectionById(array $ids = [], $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        if (!(bool)$ids) return [];

        $this->client->addQuery('shop/gets',
            [
                'id' => $ids,
            ],
            [],
            $done,
            $fail
        );
    }
}