<?php

namespace Model\Survey;

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
    public function getEntity() {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $entity = null;
        $client->addQuery('survey/survey.json', [], [],
            function ($data) use (&$entity) {
                $entity = (bool)$data ? new Entity($data) : null;
            },
            function (\Exception $e) {
                \App::exception()->remove($e);
            }
        );

        $client->execute(\App::config()->dataStore['retryTimeout']['default']);

        return $entity;
    }

    /**
     * @param        $done
     * @param        $fail
     */
    public function prepareEntity($done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('survey/survey.json', [], [], $done, $fail);
    }
}