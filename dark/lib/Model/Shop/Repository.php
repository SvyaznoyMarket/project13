<?php

namespace Model\Shop;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @param int $id
     * @return Entity|null
     */
    public function getEntityById($id) {
        \App::logger()->info('Start ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $data = $this->client->query('shop/get', array(
            'id' => array($id),
        ));
        $data = (bool)$data ? reset($data) : null;

        return $data ? new Entity($data) : null;
    }

    /**
     * @param string $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        \App::logger()->info('Start ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $data = $this->client->query('shop/get', array(
            'slug' => array($token),
        ));
        $data = (bool)$data ? reset($data) : null;

        return $data ? new Entity($data) : null;
    }

    /**
     * @param \Model\Region\Entity $region
     * @return Entity[]
     */
    public function getCollectionByRegion(\Model\Region\Entity $region) {
        \App::logger()->info('Start ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $response = $this->client->query('shop/get', array(
            'geo_id' => $region->getId(),
        ));

        $collection = array();
        foreach ($response as $data) {
            $collection[] = new Entity($data);
        }

        return $collection;
    }

    /**
     * @param array $ids
     * @return Entity[]
     */
    public function getCollectionById(array $ids = array()) {
        \App::logger()->info('Start ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        if (!(bool)$ids) return array();

        $response = $this->client->query('shop/get', array(
            'id' => $ids,
        ));

        $collection = array();
        foreach ($response as $data) {
            $collection[] = new Entity($data);
        }
        return $collection;
    }
}