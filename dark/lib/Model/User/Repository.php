<?php

namespace Model\User;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    public function getEntityByToken($token) {
        $response = $this->client->query('user/get', array(
            'token' => $token,
        ));

        $data = (bool)$response ? $response : null;

        return $data ? new Entity($data) : null;
    }
}