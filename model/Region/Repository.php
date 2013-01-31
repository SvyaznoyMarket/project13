<?php

namespace Model\Region;

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
     * @return Entity|null
     */
    public function getDefaultEntity() {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        return $this->getEntityById(\App::config()->region['defaultId']);
    }

    /**
     * @param int $id
     * @return Entity|null
     */
    public function getEntityById($id) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $response = $this->client->query('geo/get', array(
            'id' => array($id),
        ));

        $data = (bool)$response ? reset($response) : null;

        return $data ? new Entity($data) : null;
    }

    /**
     * @param int $id
     * @param $callback
     */
    public function prepareEntityById($id, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('geo/get', array('id' => array($id)), [], $callback);
    }

    /**
     * @param string $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

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
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $response = $this->client->query('geo/get-shop-available');

        $collection = [];
        foreach ($response as $data) {
            $collection[] = new Entity($data);
        }

        return $collection;
    }

    /**
     * @param $callback
     */
    public function prepareShopAvailableCollection($callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('geo/get-shop-available', [], [], $callback);
    }

    /**
     * @return Entity[]
     */
    public function getShowInMenuCollection() {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $response = $this->client->query('geo/get-menu-cities');

        $collection = [];
        foreach ($response as $data) {
            $collection[] = new Entity($data);
        }

        return $collection;
    }

    /**
     * @param $callback
     */
    public function prepareShowInMenuCollection($callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('geo/get-menu-cities', [], [], $callback);
    }
}