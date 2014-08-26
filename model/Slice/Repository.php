<?php

namespace Model\Slice;

class Repository {
    /** @var \Scms\ClientV2 */
    private $client;

    public function __construct(\Scms\ClientV2 $client) {
        $this->client = $client;
    }

    /**
     * @param $token
     * @param $done
     * @param $fail
     */
    public function prepareEntityByToken($token, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('get-slice', ['url' => $token], [], $done, $fail);
    }
}