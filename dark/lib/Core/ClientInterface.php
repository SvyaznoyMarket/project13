<?php

namespace Core;

interface ClientInterface {
    public function __construct(array $config, \Logger\LoggerInterface $logger = null);
    public function query($action, array $params = array(), array $data = array());
    public function addQuery($action, array $params = array(), array $data = array(), $callback);
    public function execute();
}