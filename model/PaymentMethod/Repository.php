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
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $collection = [];
        $client->addQuery('payment-method/get',
            [
                'geo_id' => $region ? $region->getId() : \App::user()->getRegion()->getId(),
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
     * @param int                  $id
     * @param \Model\Region\Entity $region
     * @return Entity
     */
    public function getEntityById($id, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $entity = null;
        $client->addQuery('payment-method/get',
            [
                'id'     => [$id],
                'geo_id' => $region ? $region->getId() : \App::user()->getRegion()->getId(),
            ],
            [],
            function ($data) use (&$entity) {
                $data = reset($data);
                $entity = $data ? new Entity($data) : null;
            }
        );

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $entity;
    }

    /**
     * @param \Model\Region\Entity $region
     * @param bool                 $isCorporative
     * @param                      $done
     * @param                      $fail
     */
    public function prepareCollection(\Model\Region\Entity $region = null, $isCorporative = false, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('payment-method/get', array(
            'geo_id'         => $region ? $region->getId() : \App::user()->getRegion()->getId(),
            'is_corporative' => $isCorporative,
        ), [], $done, $fail);
    }
}