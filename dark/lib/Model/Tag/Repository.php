<?php

namespace Model\Tag;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @param $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        $response = $this->client->query('tag/get', array(
            'slug'   => $token,
            'geo_id' => \App::user()->getRegion()->getId(),
        ));
        $data = reset($response);
        // mock
        $data = $data[0];

        return $data ? new Entity($data) : null;
    }

    /**
     * @param $id
     * @return Entity|null
     */
    public function getEntityById($id) {
        $response = $this->client->query('tag/get', array(
            'id'     => $id,
            'geo_id' => \App::user()->getRegion()->getId(),
        ));
        $data = reset($response);

        return $data ? new Entity($data) : null;
    }
}