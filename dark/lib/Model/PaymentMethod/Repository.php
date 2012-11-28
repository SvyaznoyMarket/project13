<?php

namespace Model\PaymentMethod;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @param \Model\Region\Entity $region
     * @return Entity[]
     */
    public function getCollection(\Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $data = $this->client->query('payment-method/get', array(
            'geo_id' => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ));

        $collection = array();
        foreach ($data as $item) {
            $collection[] = new Entity($item);
        }

        return $collection;
    }

    /**
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareCollection(\Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $this->client->addQuery('payment-method/get', array(
            'geo_id' => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ), array(), $callback);
    }
}