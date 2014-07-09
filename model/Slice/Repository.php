<?php

namespace Model\Slice;

class Repository {
    /** @var \DataStore\Client */
    private $client;

    /**
     * @param \DataStore\Client $client
     */
    public function __construct(\DataStore\Client $client) {
        $this->client = $client;
    }

    /**
     * @param $token
     * @param $done
     * @param $fail
     */
    public function prepareEntityByToken($token, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery(sprintf('slice/%s.json', $token), [], $done, $fail);
    }

    /**
     * @param $token
     * @param $done
     * @param null $fail
     */
    public function prepareSeoJsonByToken($token, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery(sprintf('seo/slice/%s.json', $token), [], $done, $fail);
    }
}