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

        $client = clone $this->client;

        $entity = null;
        $client->addQuery('tag/get',
            [
                'slug'   => $token,
                'geo_id' => \App::user()->getRegion()->getId(),
            ],
            [],
            function ($data) use (&$entity) {
                $data = reset($data);
                $entity = $data ? new Entity($data) : null;
            }
        );

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $entity;
    }

    /**
     * @param $id
     * @return Entity|null
     */
    public function getEntityById($id) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $entity = null;
        $client->addQuery('tag/get',
            [
                'id'     => $id,
                'geo_id' => \App::user()->getRegion()->getId(),
            ],
            [],
            function ($data) use (&$entity) {
                $data = reset($data);
                $entity = $data ? new Entity($data) : null;
            }
        );

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $entity;
    }
}