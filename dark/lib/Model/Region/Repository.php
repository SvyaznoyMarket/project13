<?php

namespace Model\Region;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @return Entity|null
     */
    public function getDefaultEntity() {
        \App::logger()->info('Start ' . __METHOD__);

        return $this->getEntityById(\App::config()->region['defaultId']);
    }

    /**
     * @param int $id
     * @return Entity|null
     */
    public function getEntityById($id) {
        \App::logger()->info('Start ' . __METHOD__);

        $response = $this->client->query('geo/get', array(
            'id' => array($id),
        ));

        $data = (bool)$response ? reset($response) : null;

        return $data ? new Entity($data) : null;
    }

    /**
     * @param string $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        \App::logger()->info('Start ' . __METHOD__);

        $response = $this->client->query('geo/get', array(
            'slug' => array($token),
        ));

        $data = (bool)$response ? reset($response) : null;

        return $data ? new Entity($data) : null;
    }

    /**
     * @return Entity[]
     */
    public function getShopAvailableCollection() {
        \App::logger()->info('Start ' . __METHOD__);

        $response = $this->client->query('geo/get-shop-available');

        $collection = array();
        foreach ($response as $data) {
            $collection[] = new Entity($data);
        }

        return $collection;
    }
}