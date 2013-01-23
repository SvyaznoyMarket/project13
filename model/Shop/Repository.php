<?php

namespace Model\Shop;

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
     * @param int $id
     * @return Entity|null
     */
    public function getEntityById($id) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

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
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $data = $this->client->query('shop/get', array(
            'slug' => array($token),
        ));
        $data = (bool)$data ? reset($data) : null;

        return $data ? new Entity($data) : null;
    }

    /**
     * @param string $token
     * @param        $callback
     */
    public function prepareEntityByToken($token, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('shop/get', array(
            'slug' => array($token),
        ), array(), $callback);
    }

    /**
     * @param \Model\Region\Entity $region
     * @return Entity[]
     */
    public function getCollectionByRegion(\Model\Region\Entity $region) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

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
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareCollectionByRegion(\Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = array();
        if (!is_null($region)) {
            $params['geo_id'] = $region->getId();
        }

        $this->client->addQuery('shop/get', $params, array(), $callback);
    }

    /**
     * @param array $ids
     * @return Entity[]
     */
    public function getCollectionById(array $ids = array()) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

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