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

        $data = $this->client->query('order/get', array('token' => $userToken));

        return (bool)$data ? count($data) : 0;
    }

    /**
     * @param string $userToken
     * @return Entity[]
     */
    public function getCollectionByUserToken($userToken) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $data = $this->client->query('order/get', array('token' => $userToken));

        $collection = array();
        foreach ($data as $item) {
            $collection[] = new Entity($item);
        }

        return $collection;
    }

    /**
     * @param string $userToken
     * @param $callback
     * @return void
     */
    public function prepareCollectionByUserToken($userToken, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('order/get', array('token' => $userToken), array(), $callback);
    }

    /**
     * @param string $number
     * @param string $phone
     * @return Entity|null
     */
    public function getEntityByNumberAndPhone($number, $phone) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $data = $this->client->query('order/get-by-mobile', array('number' => $number, 'mobile' => $phone));
        $data = reset($data);

        return $data ? new Entity($data) : null;
    }
}