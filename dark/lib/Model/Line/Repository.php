<?php

namespace Model\Line;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @param string $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        $data = $this->client->query('line/list', array(
            'slug'   => array($token),
            'geo_id' => \App::user()->getRegion()->getId(),
        ));
        $data = (bool)$data ? reset($data) : null;

        return $data ? new Entity($data) : null;
    }

    /**
     * @param int $id
     * @return Entity|null
     */
    public function getEntityById($id) {
        $data = $this->client->query('line/list', array(
            'id'   => array($id),
            'geo_id' => \App::user()->getRegion()->getId(),
        ));
        $data = (bool)$data ? reset($data) : null;

        return $data ? new Entity($data) : null;
    }
}
