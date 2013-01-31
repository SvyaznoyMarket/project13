<?php

namespace Model\Product\Service;

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
     * @param $id
     * @param \Model\Region\Entity $region
     * @return Entity|null
     */
    public function getEntityById($id, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = array(
            'id' => array($id),
        );
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }
        $data = $this->client->query('service/get2', $params, []);
        $data = reset($data);

        return $data ? new Entity($data) : null;
    }

    /**
     * @param array                $ids
     * @param \Model\Region\Entity $region
     * @return Entity[]
     */
    public function getCollectionById(array $ids, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        if (!(bool)$ids) return [];

        $params = array(
            'id' => $ids,
        );
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }
        $data = $this->client->query('service/get2', $params, []);

        $collection = [];
        foreach ($data as $item) {
            $collection[] = new Entity($item);
        }

        return $collection;
    }

    /**
     * @param array                $ids
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareCollectionById(array $ids, \Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        if (!(bool)$ids) return;

        $params = array(
            'id' => $ids,
        );
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }
        $this->client->addQuery('service/get2', $params, [], $callback);
    }

    /**
     * @param string               $token
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareEntityByToken($token, \Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = array(
            'slug' => $token,
        );
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }
        $this->client->addQuery('service/get2', $params, [], $callback);
    }

    /**
     * @param Category\Entity      $category
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareIdsByCategory(Category\Entity $category, \Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = array(
            'category_id' => $category->getId(),
        );
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }
        $this->client->addQuery('service/list', $params, [], $callback);
    }

    /**
     * @param Category\Entity $category
     * @param \Model\Region\Entity $region
     * @return Entity[]
     */
    public function getCollectionByCategory(Category\Entity $category, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = array(
            'category_id' => $category->getId(),
        );
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }
        $data = $this->client->query('service/list', $params, []);
        $ids = isset($data['list']) ? (array)$data['list'] : [];

        return $this->getCollectionById($ids, $region);
    }
}