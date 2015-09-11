<?php

namespace Model\Promo;

class Repository {
    /**
     * @param \Scms\Client $client
     */
    public function __construct(\Scms\Client $client) {
        $this->client = $client;
    }

    /**
     * @deprecated
     * @param $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $data = $this->client->query(
            'api/promo-catalog/get',
            [
                'slugs' => [$token],
            ]
        )['result'][0];
        if (is_array($data)) {
            $data['token'] = $token;
        }

        return (bool)$data ? new Entity($data) : null;
    }

    /**
     * @param $token
     * @param $done
     */
    public function prepareByToken($token, $done) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery(
            'api/promo-catalog/get',
            [
                'slugs' => [$token],
            ],
            [],
            $done
        );
    }
}