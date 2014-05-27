<?php

namespace Model\Order\BonusCard;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    /**
     * @param \Core\ClientInterface $client
     */
    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    public function prepareCollection($done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('payment-method/get-bonus-card', [], [], $done, $fail);
    }

    public function getCollection() {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;
        $data = $client->query('payment-method/get-bonus-card');

        $collection = [];
        foreach ($data as $item) {
            $collection[] = new Entity($item);
        }

        return $collection;
    }
} 