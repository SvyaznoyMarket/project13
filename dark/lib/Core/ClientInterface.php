<?php

namespace Core;

interface ClientInterface {
    public function __construct(array $config, \Logger\LoggerInterface $logger = null);
    public function query($action, array $params = array(), array $data = array());
}