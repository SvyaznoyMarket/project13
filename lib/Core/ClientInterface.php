<?php

namespace Core;

interface ClientInterface {
    public function __construct(array $config, \Curl\Client $curl);
    public function query($action, array $params = [], array $data = []);
    public function addQuery($action, array $params = [], array $data = [], $callback);
    public function execute();
}