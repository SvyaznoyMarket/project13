<?php

namespace Model\Subway;

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
     * @param \Model\Region\Entity $region
     * @return Entity[]
     */
    public function getCollectionByRegion(\Model\Region\Entity $region) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $collection = [];
        $client->addQuery('subway/get',
            [
                'geo_id' => $region->getId(),
            ],
            [],
            function ($data) use (&$collection) {
                foreach ($data as $item) {
                    $collection[] = new Entity($item);
                }
            }
        );

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $collection;
    }

    /**
     * @param \Model\Region\Entity $region
     * @param $done
     * @param $fail
     */
    public function prepareCollectionByRegion(\Model\Region\Entity $region, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('subway/get', array(
            'geo_id' => $region->getId(),
        ), [], $done, $fail);
    }
}