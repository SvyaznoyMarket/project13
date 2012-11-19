<?php

namespace Model\Order;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @param string $userToken
     * @return int Количество заказов
     */
    public function countByUserToken($userToken) {
        \App::logger()->info('Start ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $data = $this->client->query('order/get', array('token' => $userToken));

        return $data ? count($data) : 0;
    }

    /**
     * @param string $userToken
     * @return Entity[]
     */
    public function getCollectionByUserToken($userToken) {
        \App::logger()->info('Start ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $data = $this->client->query('order/get', array('token' => $userToken));

        $collection = array();
        foreach ($data as $item) {
            $collection[] = new Entity($item);
        }

        return $collection;
    }
}