<?php

namespace Model\Tag;

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
     * @param $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $response = $this->client->query('tag/get', array(
            'slug'   => $token,
            'geo_id' => \App::user()->getRegion()->getId(),
        ));
        $data = reset($response);

        return $data ? new Entity($data) : null;
    }

    /**
     * @param $id
     * @return Entity|null
     */
    public function getEntityById($id) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $response = $this->client->query('tag/get', array(
            'id'     => $id,
            'geo_id' => \App::user()->getRegion()->getId(),
        ));
        $data = reset($response);

        return $data ? new Entity($data) : null;
    }
}