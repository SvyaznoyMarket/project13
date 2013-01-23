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
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $response = $this->client->query('user/get', array(
            'token' => $token,
        ));

        $data = (bool)$response ? $response : null;

        return $data ? new Entity($data) : null;
    }

    /**
     * @param string $token
     * @param        $done
     * @param        $fail
     */
    public function prepareEntityByToken($token, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('user/get', array(
            'token' => $token,
        ), array(), $done, $fail);
    }
}