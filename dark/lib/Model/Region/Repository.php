<?php

namespace Model\Region;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    public function getDefaultEntity() {
        return $this->getEntityById(\App::config()->region['defaultId']);
    }

    public function getEntityById($id) {
        $response = $this->client->query('geo/get', array(
            'id' => array($id),
        ));

        $data = (bool)$response ? reset($response) : null;

        return $data ? new Entity($data) : null;
    }
}