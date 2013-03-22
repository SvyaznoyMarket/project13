<?php

namespace Model\Menu;

class Repository {
    /**
     * @param \DataStore\Client $client
     */
    public function __construct(\DataStore\Client $client) {
        $this->client = $client;
    }

    /**
     * @return Entity[]
     */
    public function getCollection() {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;
        $data = $client->query('/main-menu.json');

        $collection = [];
        foreach ($data['item'] as $item) {
            $collection[] = new Entity($item);
        }

        return $collection;
    }

    /**
     * @param callback      $done
     * @param callback|null $fail
     */
    public function prepareCollection($done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('main-menu.json', $done, $fail);
    }
}