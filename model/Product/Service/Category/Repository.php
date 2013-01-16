<?php

namespace Model\Product\Service\Category;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareRootCollection(\Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $params = array(
            'max_depth' => 3,
        );
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }
        $this->client->addQuery('service/get-category-tree', $params, array(), $callback);
    }

    /**
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareCollection(\Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $params = array(
            'max_depth' => 3,
        );
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }
        $this->client->addQuery('service/get-category-tree', $params, array(), $callback);
    }

    /**
     * @param \Model\Region\Entity $region
     * @return Entity[]
     */
    public function getCollection(\Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $params = array(
            'max_depth' => 3,
        );
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }

        $data = $this->client->query('service/get-category-tree', $params);
        if (!isset($data['children']) || !is_array($data['children'])) {
            $e = new \Exception('Неверные данные для категорий услуг');
            \App::exception()->add($e);
            \App::logger()->error($e);
            $data = array();
        } else {
            $data = $data['children'];
        }

        $collection = array();
        foreach ($data as $item) {
            $collection[] = new Entity($item);
        }

        return $collection;
    }

    /**
     * @param string               $token
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareEntityByToken($token, \Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $params = array(
            'slug' => $token,
        );
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }

        $this->client->addQuery('service/get-category-tree', $params, array(), $callback);
    }
}