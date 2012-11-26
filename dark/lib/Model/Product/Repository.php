<?php

namespace Model\Product;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;
    private $entityClass = '\Model\Product\Entity';

    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    public function setEntityClass($class) {
        $this->entityClass = $class;
    }

    /**
     * @param $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $response = $this->client->query('product/get', array(
            'select_type' => 'slug',
            'slug'        => $token,
            'geo_id'      => \App::user()->getRegion()->getId(),
        ));
        $data = reset($response);

        return $data ? new Entity($data) : null;
    }

    /**
     * @param string               $token
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareEntityByToken($token, \Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $params = array(
            'select_type' => 'slug',
            'slug'        => $token,
        );
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }

        $this->client->addQuery('product/get', $params, array(), $callback);
    }

    /**
     * @param $id
     * @return Entity|null
     */
    public function getEntityById($id) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $response = $this->client->query('product/get', array(
            'id'        => $id,
            'geo_id'      => \App::user()->getRegion()->getId(),
        ));
        $data = reset($response);

        return $data ? new Entity($data) : null;
    }

    /**
     * @param array $tokens
     * @return Entity[]
     */
    public function getCollectionByToken(array $tokens) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        if (!(bool)$tokens) return array();

        $response = $this->client->query('product/get', array(
            'select_type' => 'slug',
            'slug'        => $tokens,
            'geo_id'      => \App::user()->getRegion()->getId(),
        ));

        $collection = array();
        foreach ($response as $data) {
            $collection[] = new $this->entityClass($data);
        }

        return $collection;
    }

    /**
     * @param array $ids
     * @return Entity[]
     */
    public function getCollectionById(array $ids) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        if (!(bool)$ids) return array();

        $response = $this->client->query('product/get', array(
            'select_type' => 'id',
            'id'          => $ids,
            'geo_id'      => \App::user()->getRegion()->getId(),
        ));

        $collection = array();
        foreach ($response as $data) {
            $collection[] = new $this->entityClass($data);
        }

        return $collection;
    }

    public function prepareCollectionById(array $ids, \Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        if (!(bool)$ids) return;

        $params = array(
            'select_type' => 'id',
            'id'          => $ids,
        );
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }
        $this->client->addQuery('product/get', $params, array(), $callback);
    }

    /**
     * @param array $filter
     * @return int
     */
    public function countByFilter(array $filter = array()) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $response = $this->client->query('listing/list', array(
            'filter' => array(
                'filters' => $filter,
                'sort'    => array(),
                'offset'  => null,
                'limit'   => null,
            ),
            'region_id' => \App::user()->getRegion()->getId(),
        ));

        return !empty($response['count']) ? (int)$response['count'] : 0;
    }

    /**
     * @param array $filter
     * @param array $sort
     * @param null $offset
     * @param null $limit
     * @return \Iterator\EntityPager
     */
    public function getIteratorByFilter(array $filter = array(), array $sort = array(), $offset = null, $limit = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $response = $this->client->query('listing/list', array(
            'filter' => array(
                'filters' => $filter,
                'sort'    => $sort,
                'offset'  => $offset,
                'limit'   => $limit,
            ),
            'region_id' => \App::user()->getRegion()->getId(),
        ));

        $collection = !empty($response['list']) ? $this->getCollectionById($response['list']) : array();

        return new \Iterator\EntityPager($collection, (int)$response['count']);
    }

    /**
     * @param array $filters
     * @param array $sort
     * @param null $offset
     * @param null $limit
     * @return \Iterator\EntityPager[]
     */
    public function getIteratorsByFilter(array $filters = array(), array $sort = array(), $offset = null, $limit = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $response = $this->client->query('listing/multilist', array(), array(
            'filter_list' => array_map(function($filter) use ($sort, $offset, $limit) {
                return array(
                    'filters' => $filter,
                    'sort'    => $sort,
                    'offset'  => $offset,
                    'limit'   => $limit,
                );
            }, $filters),
            'region_id' => \App::user()->getRegion()->getId(),
        ));

        if (!(bool)$response) {
            return array();
        }

        // собираем все идентификаторы товаров, чтобы сделать один запрос в ядро
        $ids = array();
        foreach ($response as $data) {
            $ids = array_merge($ids, $data['list']);
        }
        // товары сгруппированные по идентификаторам
        $collectionById = array();
        foreach ($this->getCollectionById($ids) as $entity) {
            $collectionById[$entity->getId()] = $entity;
        }
        $iterators = array();
        foreach ($response as $data) {
            $collection = array();
            foreach ($data['list'] as $id) {
                $collection[] = $collectionById[$id];
            }

            $iterators[] = new \Iterator\EntityPager($collection, $data['count']);
        }

        return $iterators;
    }
}