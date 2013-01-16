<?php

namespace Model\Product;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;
    private $entityClass = '\Model\Product\Entity';

    /**
     * @param \Core\ClientInterface $client
     */
    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @param string $class
     */
    public function setEntityClass($class) {
        $this->entityClass = $class;
    }

    /**
     * @param $token
     * @param \Model\Region\Entity $region
     * @return Entity|null
     */
    public function getEntityByToken($token, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $response = $this->client->query('product/get', array(
            'select_type' => 'slug',
            'slug'        => $token,
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
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

        $this->client->addQuery('product/get', array(
            'select_type' => 'slug',
            'slug'        => $token,
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ), array(), $callback);
    }

    /**
     * @param $id
     * @param \Model\Region\Entity $region
     * @return Entity|null
     */
    public function getEntityById($id, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $response = $this->client->query('product/get', array(
            'id'     => $id,
            'geo_id' => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ));
        $data = reset($response);

        return $data ? new Entity($data) : null;
    }

    /**
     * @param array $tokens
     * @param \Model\Region\Entity $region
     * @return Entity[]
     */
    public function getCollectionByToken(array $tokens, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        if (!(bool)$tokens) return array();

        $response = $this->client->query('product/get', array(
            'select_type' => 'slug',
            'slug'        => $tokens,
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ));

        $collection = array();
        foreach ($response as $data) {
            $collection[] = new $this->entityClass($data);
        }

        return $collection;
    }

    /**
     * @param array $barcodes
     * @param \Model\Region\Entity $region
     * @return Entity[]
     */
    public function getCollectionByBarcode(array $barcodes, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        if (!(bool)$barcodes) return array();

        $response = $this->client->query('product/get', array(
            'select_type' => 'bar_code',
            'bar_code'    => $barcodes,
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ));

        $collection = array();
        foreach ($response as $data) {
            $collection[] = new $this->entityClass($data);
        }

        return $collection;
    }

    /**
     * @param array                $barcodes
     * @param \Model\Region\Entity $region
     * @param                      $done
     * @param                      $fail
     */
    public function prepareCollectionByBarcode(array $barcodes, \Model\Region\Entity $region = null, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $this->client->addQuery('product/get', array(
            'select_type' => 'bar_code',
            'bar_code'    => $barcodes,
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ), array(), $done, $fail);
    }

    /**
     * @param array $ids
     * @return Entity[]
     */
    public function getCollectionById(array $ids, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        if (!(bool)$ids) return array();

        $response = $this->client->query('product/get', array(
            'select_type' => 'id',
            'id'          => $ids,
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ));

        $collection = array();
        foreach ($response as $data) {
            $collection[] = new $this->entityClass($data);
        }

        return $collection;
    }

    /**
     * @param array                $ids
     * @param \Model\Region\Entity $region
     * @param                      $done
     * @param                      $fail
     */
    public function prepareCollectionById(array $ids, \Model\Region\Entity $region = null, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        if (!(bool)$ids) return;

        $this->client->addQuery('product/get', array(
            'select_type' => 'id',
            'id'          => $ids,
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ), array(), $done, $fail);
    }

    /**
     * @param array $filter
     * @param \Model\Region\Entity $region
     * @return int
     */
    public function countByFilter(array $filter = array(), \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $response = $this->client->query('listing/list', array(
            'filter' => array(
                'filters' => $filter,
                'sort'    => array(),
                'offset'  => null,
                'limit'   => null,
            ),
            'region_id' => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ));

        return !empty($response['count']) ? (int)$response['count'] : 0;
    }

    /**
     * @param array $filter
     * @param array $sort
     * @param null $offset
     * @param null $limit
     * @param \Model\Region\Entity $region
     * @return \Iterator\EntityPager
     */
    public function getIteratorByFilter(array $filter = array(), array $sort = array(), $offset = null, $limit = null, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $response = $this->client->query('listing/list', array(
            'filter' => array(
                'filters' => $filter,
                'sort'    => $sort,
                'offset'  => $offset,
                'limit'   => $limit,
            ),
            'region_id' => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ));

        $collection = !empty($response['list']) ? $this->getCollectionById($response['list'], $region) : array();

        return new \Iterator\EntityPager($collection, (int)$response['count']);
    }

    /**
     * @param array $filters
     * @param array $sort
     * @param null $offset
     * @param null $limit
     * @param \Model\Region\Entity $region
     * @return \Iterator\EntityPager[]
     */
    public function getIteratorsByFilter(array $filters = array(), array $sort = array(), $offset = null, $limit = null, \Model\Region\Entity $region = null) {
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
            'region_id' => $region ? $region->getId() : \App::user()->getRegion()->getId(),
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