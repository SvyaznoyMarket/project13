<?php

namespace Model\Survey;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    /**
     * @param \Core\ClientInterface $client
     */
    public function __construct(\DataStore\Client $client) {
        $this->client = $client;
    }


    /**
     * @return Entity|null
     */
    public function getEntity() {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $data = $this->client->query('survey/survey.json');
        $entity = (bool)$data ? new Entity($data) : null;

        return $entity;
    }

}