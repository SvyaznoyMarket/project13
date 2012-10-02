<?php

namespace Core;

class ClientV1 implements ClientInterface {
    private $config;
    /** @var \Logger\LoggerInterface */
    private $logger;

    public function __construct(array $config, \Logger\LoggerInterface $logger = null) {
        $this->config = array_merge(array(
            'client_id'    => null,
            'consumer_key' => null,
            'signature'    => null,
        ), $config);
    }

    public function query($action, array $params = array(), array $data = array())
    {

    }
}