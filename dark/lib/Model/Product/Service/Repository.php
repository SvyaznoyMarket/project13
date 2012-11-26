<?php

namespace Model\Product\Service;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    public function prepareCollectionById(array $ids, \Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        if (!(bool)$ids) return;

        $params = array(
            'id' => $ids,
        );
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }
        $this->client->addQuery('service/get2', $params, array(), $callback);
    }
}