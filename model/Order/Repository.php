<?php

namespace Model\Order;

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
     * @param string $userToken
     * @return int Количество заказов
     */
    public function countByUserToken($userToken) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $entity = null;
        $client->addQuery('order/get', ['token' => $userToken], [], function ($data) use (&$entity) {
            $data = reset($data);
            $entity = $data ? new Entity($data) : null;
        });

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $entity;
    }

    /**
     * @param string $userToken
     * @return Entity[]
     */
    public function getCollectionByUserToken($userToken) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $collection = [];
        $client->addQuery('order/get', ['token' => $userToken], [], function ($data) use (&$collection) {
            foreach ($data as $item) {
                $collection[] = new Entity($item);
            }
        });

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $collection;
    }

    /**
     * @param string $userToken
     * @param $callback
     * @return void
     */
    public function prepareCollectionByUserToken($userToken, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('order/get', ['token' => $userToken], [], $callback);
    }

    /**
     * @param string $number
     * @param string $phone
     * @return Entity|null
     */
    public function getEntityByNumberAndPhone($number, $phone) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $entity = null;
        $client->addQuery('order/get-by-mobile', ['number' => $number, 'mobile' => $phone], [], function ($data) use (&$entity) {
            $data = reset($data);
            $entity = $data ? new Entity($data) : null;
        });

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $entity;
    }
}