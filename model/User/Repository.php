<?php

namespace Model\User;

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
     * @param string $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $entity = null;
        $client->addQuery(
            'user/get',
            [
                'token' => $token
            ],
            [],
            function ($data) use (&$entity) {
                $entity = (bool)$data ? new Entity($data) : null;
            },
            function (\Exception $e) {
                \App::exception()->remove($e);
            },
            \App::config()->coreV2['timeout'] * 2
        );

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $entity;
    }

    /**
     * @param string $token
     * @param        $done
     * @param        $fail
     */
    public function prepareEntityByToken($token, $done, $fail = null) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery(
            'user/get',
            [
                'token' => $token,
            ],
            [],
            $done,
            $fail,
            \App::config()->coreV2['timeout'] * 2
        );
    }
}