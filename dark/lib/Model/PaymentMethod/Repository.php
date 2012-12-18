<?php

namespace Model\PaymentMethod;

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
    public function getCollection(\Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $data = $this->client->query('payment-method/get', array(
            'geo_id' => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ));

        $enabledIds = \App::config()->payment['enabledIds'];

        $collection = array();
        foreach ($data as $item) {
            if (!in_array($item['id'], $enabledIds)) continue;

            $collection[] = new Entity($item);
        }

        return $collection;
    }

    /**
     * @param int                  $id
     * @param \Model\Region\Entity $region
     * @return Entity
     */
    public function getEntityById($id, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $data = $this->client->query('payment-method/get', array(
            'id'     => $id,
            'geo_id' => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ));
        $data = reset($data);

        return $data ? new Entity($data) : null;
    }

    /**
     * @param \Model\Region\Entity $region
     * @param bool                 $isCorporative
     * @param                      $done
     * @param                      $fail
     */
    public function prepareCollection(\Model\Region\Entity $region = null, $isCorporative = false, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $this->client->addQuery('payment-method/get', array(
            'geo_id'         => $region ? $region->getId() : \App::user()->getRegion()->getId(),
            'is_corporative' => $isCorporative,
        ), array(), $done, $fail);
    }
}