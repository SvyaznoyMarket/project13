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
        $response = $this->client->query('shop/get', array(
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
        $response = $this->client->query('shop/get', array(
            'slug' => array($token),
        ));

        $data = (bool)$response ? reset($response) : null;

        return $data ? new Entity($data) : null;
    }
}