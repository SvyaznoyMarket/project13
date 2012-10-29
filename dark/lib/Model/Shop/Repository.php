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