<?php

namespace Model\Menu;

class Repository {
    /** @var \Scms\Client */
    private $client;

    public function __construct() {
        $this->client = \App::scmsClient();
    }

    /**
     * @return Entity[]
     */
    public function getCollection() {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = \App::dataStoreClient();
        $data = $client->query('/main-menu.json');

        return $data;
    }

    /**
     * @param callback      $done
     * @param callback|null $fail
     */
    public function prepareCollection($done, $fail = null) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('seo/main-menu', ['tags' => ['site-web']], [], $done, $fail, \App::config()->scms['timeout'] * 2);
    }
}