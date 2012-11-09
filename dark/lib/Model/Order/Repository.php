<?php

namespace Model\Order;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    public function countByUserToken($userToken) {
        $result = $this->client->query('order/get', array('token' => $userToken));

        return $result ? count($result) : 0;
    }

}