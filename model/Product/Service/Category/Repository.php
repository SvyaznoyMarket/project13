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
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [
            'max_depth' => 3,
        ];
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }
        $this->client->addQuery('service/get-category-tree', $params, [], $callback);
    }

    /**
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareCollection(\Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [
            'max_depth' => 3,
        ];
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }
        $this->client->addQuery('service/get-category-tree', $params, [], $callback);
    }

    /**
     * @param \Model\Region\Entity $region
     * @return Entity[]
     */
    public function getCollection(\Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $params = [
            'max_depth' => 3,
        ];
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }

        $collection = [];
        $client->addQuery('service/get-category-tree', $params, [], function ($data) use (&$collection) {
            if (!isset($data['children']) || !is_array($data['children'])) {
                $e = new \Exception('Неверные данные для категорий услуг');
                \App::exception()->add($e);
                \App::logger()->error($e);
                $data = [];
            } else {
                $data = $data['children'];
            }

            foreach ($data as $item) {
                $collection[] = new Entity($item);
            }
        });

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $collection;
    }

    /**
     * @param string               $token
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareEntityByToken($token, \Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [
            'slug' => $token,
        ];
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }

        $this->client->addQuery('service/get-category-tree', $params, [], $callback);
    }
}